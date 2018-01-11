<?php

namespace Arbory\Merchant;

use Arbory\Merchant\Models\Order;
use Arbory\Merchant\Models\Transaction;
use Arbory\Merchant\Utils\GatewayHandlerFactory;
use Illuminate\Http\Request;
use Omnipay\Common\GatewayInterface;
use Omnipay\Common\Message\ResponseInterface;
use Illuminate\Support\Facades\Log;
use Arbory\Merchant\Utils\CompletedPurchase;

class PaymentsService
{

    /**
     * @param Order $order
     * @param string $gatewayName
     * @param array $customArgs
     * @return bool redirects or returns bool ir payment is successful
     */
    public function purchase(Order $order, string $gatewayName, array $customArgs)
    {
        try {
            // Will throw exception if not existing gateway
            $gatewayObj = \Omnipay::gateway($gatewayName);

            /** @var Transaction $transaction */
            $transaction = $this->createTransaction($order, $gatewayObj);
            $this->setPurchaseArgs($transaction, $gatewayObj, $customArgs);

            $purchaseRequest = $gatewayObj->purchase($transaction->options['purchase']);
            $this->logTransactionRequest($transaction, $purchaseRequest->getData());

            $response = $purchaseRequest->send();
            $this->logTransactionResponse($transaction, $response->getData());

            // Save transactions reference if gateway responds with it
            $this->saveTransactionReference($transaction, $response);
            $this->setTransactionInitialized($transaction);

            if ($response->isSuccessful()) {
                $this->setTransactionProcessed($transaction);
                // Payment successful
                return true;
            } elseif ($response->isRedirect() || $response->isTransparentRedirect()) {
                $this->setTransactionAccepted($transaction);
                $this->logTransactionRequest($transaction, 'redirect to merchant..');
                $response->redirect();
            } else {
                // Payment failed
                $this->setTransactionError($transaction);
                $this->logTransactionError($transaction, $response->getMessage());
            }
        } catch (\Exception $e) {
            $this->setTransactionError($transaction);
            $this->logTransactionError($transaction, $e->getCode() .':'. $e->getMessage());
        }

        return false;
    }


    /**
     * Returns transaction if payment completed or false if payment failed
     * @param string $gatewayName
     * @param Request $request
     * @return CompletedPurchase
     */
    public function completePurchase(string $gatewayName, Request $request)
    {
        try {
            // Will throw exception if not existing gateway
            $gatewayObj = \Omnipay::gateway($gatewayName);

            /** @var Transaction $transaction */
            $transaction = $this->getRequestsTransaction($gatewayObj, $request);
            $this->logTransactionResponse($transaction, $request->input());

            $this->setCompletionArgs($transaction, $gatewayObj);

            //TODO: add parameters for each gateway?!
            $completeRequest = $gatewayObj->completePurchase($transaction->options['completePurchase']);
            $this->logTransactionRequest($transaction, $completeRequest->getData());

            $response = $completeRequest->send();
            $this->logTransactionResponse($transaction, $response->getData());

            if ($response->isSuccessful()) {
                // purchase finished
                $this->setTransactionProcessed($transaction);
                return new CompletedPurchase(true, $transaction);
            }

            //we got error
            $this->setTransactionError($transaction);
            $this->logTransactionError($transaction, $response->getData());

            return new CompletedPurchase(false, $transaction);
        } catch (\Exception $e) {
            \Log::warning('PaymentService:completePurchase:'.$e->getMessage());
        }

        return new CompletedPurchase(false);
    }

    /**
     * Some gateways will send their token reference (gateways own token for transaction)
     *
     * @param ResponseInterface $response
     */
    private function saveTransactionReference(Transaction $transaction, ResponseInterface $response)
    {
        $transaction->token_reference = $response->getTransactionReference();
        $transaction->save();
    }

    private function getRequestsTransaction(GatewayInterface $gateway, Request $request) : Transaction
    {
        $gatewayClassName = get_class($gateway);
        $gatewayHandler = (new GatewayHandlerFactory())->create($gateway);
        $transactionRef = $gatewayHandler->getTransactionReference($request);

        if ($transactionRef) {
            // Get by unique reference token per gateway
            return Transaction::where('token_reference', $transactionRef)->where('gateway', $gatewayClassName)->firstOrFail();
        }

        throw new \InvalidArgumentException('Transaction not found');
    }

    private function setCompletionArgs(Transaction $transaction, GatewayInterface $gatewayObj)
    {
        $gatewayHandler = (new GatewayHandlerFactory())->create($gatewayObj);
        $options = $transaction->options;
        $options['completePurchase'] = $gatewayHandler->getCompletePurchaseArguments($transaction);
        $transaction->options = $options;
        $transaction->save();
    }

    private function setPurchaseArgs(Transaction $transaction, GatewayInterface $gatewayObj, $customArgs)
    {
        // Each gateway can have a little different request arguments
        $gatewayHandler = (new GatewayHandlerFactory())->create($gatewayObj);
        $gatewayArgs = $gatewayHandler->getPurchaseArguments($transaction);

        // These arguments are common for all gateways
        $commonArgs = [
            'language' => $transaction->language_code, //gateway dependant
            'amount' => $this->transformToFloat($transaction->amount),
            'currency' => $transaction->currency_code
        ];

        // Custom arguments from checkout (purchase description etc.)
        $args = $customArgs + $gatewayArgs + $commonArgs;

        $options = $transaction->options;
        $options['purchase'] = $args;
        $transaction->options = $options;
        $transaction->save();
    }

    //Int to float with 2 numbers after floating point
    public function transformToFloat($intValue){
        // round and float
        $float = round(($intValue/100), 2, PHP_ROUND_HALF_EVEN);
        return number_format($float, 2, '.', '');
    }

    private function createTransaction(Order $order, GatewayInterface $gateway)
    {
        return Transaction::create([
            'object_class' => get_class($order),
            'object_id' => $order->id,
            'status' => Transaction::STATUS_CREATED,
            'gateway' => get_class($gateway),
            'options' => [], // will be populated on every request
            'amount' => $order->total,
            'token_id' => str_random('20'), //TODO: Do we need internal token?
            'description' => '',
            'language_code' => $this->getGatewaysLanguage($gateway, $order->language),
            'currency_code' => $order->payment_currency,
            'client_ip' => request()->ip()
        ]);
    }

    /**
     * Gets gateways supported language code. $suggestedLanguage serves two purposes :
     * 1. if gateway only accepts specific language codes, default or the closest one to $suggestedLanguage will be returned
     * 2. if gateway has custom language codes, then $suggestedLanguage will be returned and used
     * @param GatewayInterface $gateway
     * @param string $suggestedLanguage  //2 characters code for language https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
     * @return string
     */
    protected function getGatewaysLanguage(GatewayInterface $gateway, string $suggestedLanguage)
    {
         return (new GatewayHandlerFactory())->create($gateway)->getLanguage($suggestedLanguage);
    }

    protected function setTransactionInitialized(Transaction $transaction)
    {
        $transaction->status == Transaction::STATUS_INITIALIZED;
        $transaction->save();
    }

    protected function setTransactionAccepted(Transaction $transaction)
    {
        $transaction->status == Transaction::STATUS_ACCEPTED;
        $transaction->save();
    }

    protected function setTransactionProcessed(Transaction $transaction)
    {
        $transaction->status == Transaction::STATUS_PROCESSED;
        $transaction->save();
    }

    protected function setTransactionError(Transaction $transaction)
    {
        $transaction->status == Transaction::STATUS_PROCESSED;
        $transaction->save();
    }


    protected function logTransactionError(Transaction $transaction, $msg)
    {
        $transaction->refresh();
        $transaction->error = $transaction->error . '[error|'. date('Y.m.d h:i:s') .']: ' . print_r($msg, true);
        $transaction->save();
    }

    protected function logTransactionRequest(Transaction $transaction, $msg)
    {
        $transaction->refresh();
        $transaction->response = $transaction->response . '[request|'. date('Y.m.d h:i:s').']: '  . print_r($msg, true);
        $transaction->save();
    }

    protected function logTransactionResponse(Transaction $transaction, $msg)
    {
        $transaction->refresh();
        $transaction->response = $transaction->response . '[response|'. date('Y.m.d h:i:s').']: '  . print_r($msg, true);
        $transaction->save();
    }
}