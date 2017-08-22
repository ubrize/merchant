<?php

namespace Arbory\Merchant;

use Arbory\Merchant\Models\Order;
use Arbory\Merchant\Models\Transaction;
use Omnipay\Common\Message\ResponseInterface;

class PaymentsService{
    /**
     * @param Order  $order
     * @param string $gatewayName
     * @param array  $parameters
     */
    public function purchase(Order $order, string $gatewayName, array $gatewayRequestArgs)
    {
        /** @var Transaction $transaction */
        $transaction = $this->createTransaction($order, $gatewayName, $gatewayRequestArgs);
        $gatewayObj = \Omnipay::gateway($gatewayName);

        // Send transaction request to gateway
        $response = $gatewayObj->purchase($this->getPurchaseArguments($transaction, $gatewayRequestArgs ))->send();

        // Save transactions reference if gateway responds with it
        $this->saveTransactionReference($transaction, $response);
        $this->setTransactionInitialized($transaction);

        // Check if transaction is complete
        if ($response->isSuccessful()) {
            // Payment was successful
            echo  $response->getMessage();
        } elseif ($response->isRedirect() || $response->isTransparentRedirect()) {
            // Redirect to offsite payment gateway
            $response->redirect();
        } else {
            // Payment failed
            echo $response->getMessage();
        }
    }

    /**
     * @param ResponseInterface $response
     */
    public function handleGatewayResponse(ResponseInterface $response)
    {

        //TODO: make me!
        dd($response);
    }

    /**
     * Some gateways will send their token reference (gateways own token for transaction)
     * @param ResponseInterface $response
     */
    private function saveTransactionReference(Transaction $transaction, ResponseInterface $response)
    {
        $transaction->token_reference = $response->getTransactionReference();
        $transaction->save();
    }

    /**
     * @param ResponseInterface $response
     * @param Order             $order
     * @param string            $gatewayName
     * @param array             $parameters
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    private function createTransaction(Order $order, string $gatewayName, array $parameters)
    {
        return Transaction::create([
            'object_class'      => get_class($order),
            'object_id'         => $order->id,
            'status'            => Transaction::STATUS_CREATED,
            'gateway'           => $gatewayName,
            'options'           => json_encode($parameters),
            'amount'            => $order->getAmountDecimal(),
            'token_id'          => str_random('20'), //TODO: Do we need internal token?
            'description'       => '',
            'language_code'     => $order->language,
            'currency_code'     => $order->payment_currency,
        ]);
    }

    private function getPurchaseArguments(Transaction $transaction, array $customArgs = []){
        $args = [
            'language'      => $transaction->language_code, //gateway dependant
            'amount'        => $transaction->amount,
            'currency'      => $transaction->currency_code
        ];
        return ($customArgs + $args);
    }

    private function setTransactionInitialized(Transaction $transaction)
    {
        $transaction->status == Transaction::STATUS_INITIALIZED;
        $transaction->save();
    }
}