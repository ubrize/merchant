<?php

namespace Arbory\Merchant;

use Arbory\Merchant\Models\Order;
use Arbory\Merchant\Models\Transaction;
use Arbory\Merchant\Utils\GatewayHandlerFactory;
use Illuminate\Http\Request;
use Omnipay\Common\GatewayInterface;
use Omnipay\Common\Message\ResponseInterface;

class PaymentsService
{

    public function purchase(Order $order, string $gatewayName, array $customArgs)
    {
        try {
            // Will throw exception if not existing gateway
            $gatewayObj = \Omnipay::gateway($gatewayName);

            /** @var Transaction $transaction */
            $transaction = $this->createTransaction($order, $gatewayObj);
            $this->setPurchaseArgs($transaction, $order, $customArgs);

            $response = $gatewayObj->purchase($transaction->options['purchase'])->send();

            // Save transactions reference if gateway responds with it
            $this->saveTransactionReference($transaction, $response);
            $this->setTransactionInitialized($transaction);

            if ($response->isSuccessful()) {
                // Payment successful
            } elseif ($response->isRedirect() || $response->isTransparentRedirect()) {
                $response->redirect();
            } else {
                // Payment failed
                echo $response->getMessage();
            }
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function completePurchase(string $gatewayName, Request $request)
    {
        try {
            // Will throw exception if not existing gateway
            $gatewayObj = \Omnipay::gateway($gatewayName);

            /** @var Transaction $transaction */
            $transaction = $this->getRequestsTransaction($gatewayObj, $request);
            $this->setCompletionArgs($transaction, $gatewayObj);

            //TODO: add parameters for each gateway
            $response = $gatewayObj->completePurchase($transaction->options['completePurchase'])->send();

            if ($response->isSuccessful()) {
                // purchase finished
                $gut = 1;
            } else {
                //we got error, save and responde
                echo $response->getMessage();
            }

        } catch (\Exception $e) {
            dd($e);
        }
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
        } else {
            //TODO: if fails, try to search by transaction id ?
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

    private function setPurchaseArgs(Transaction $transaction, Order $order, $customArgs)
    {
        $defArgs = [
            'language' => $order->language, //gateway dependant
            'amount' => $order->getAmountDecimal(),
            'currency' => $order->payment_currency
        ];

        $options = $transaction->options;
        $options['purchase'] = ($customArgs + $defArgs);
        $transaction->options = $options;
        $transaction->save();
    }

    private function createTransaction(Order $order, GatewayInterface $gateway)
    {
        return Transaction::create([
            'object_class' => get_class($order),
            'object_id' => $order->id,
            'status' => Transaction::STATUS_CREATED,
            'gateway' => get_class($gateway),
            'options' => [], // will be populated on every request
            'amount' => $order->getAmountDecimal(),
            'token_id' => str_random('20'), //TODO: Do we need internal token?
            'description' => '',
            'language_code' => $order->language,
            'currency_code' => $order->payment_currency,
            'client_ip' => request()->ip()
        ]);
    }

    private function setTransactionInitialized(Transaction $transaction)
    {
        $transaction->status == Transaction::STATUS_INITIALIZED;
        $transaction->save();
    }
}