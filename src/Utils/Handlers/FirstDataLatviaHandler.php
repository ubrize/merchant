<?php

namespace Arbory\Merchant\Utils\Handlers;

use Illuminate\Http\Request;
use Arbory\Merchant\Models\Transaction;
use Arbory\Merchant\Utils\GatewayHandler;

class FirstDataLatviaHandler extends GatewayHandler
{
    public function getReversalArguments(Transaction $transaction): array
    {
        return [
            'transactionReference' => $transaction->token_reference
        ];
    }

    public function getTransactionReference(Request $request): string
    {
        $transactionRef = $request->get('trans_id', '');
        return $transactionRef;
    }

    public function getCompletePurchaseArguments(Transaction $transaction, Request $request): array
    {
        $possibleActions = ['purchase', 'authorizeRecurring', 'executeRecurring'];
        $purchaseParameters = [];

        foreach ($possibleActions as $action) {
            if (!empty($transaction->options[$action])) {
                $purchaseParameters = $transaction->options[$action];
            }
        }
        $purchaseParameters['transactionReference'] = $transaction->token_reference;
        return $purchaseParameters;
    }

    public function getPurchaseArguments(Transaction $transaction): array
    {
        return [
            'clientIp' => $transaction->client_ip
        ];
    }
}