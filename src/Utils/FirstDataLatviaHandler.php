<?php

namespace Arbory\Merchant\Utils;

use Illuminate\Http\Request;
use Arbory\Merchant\Models\Transaction;

class FirstDataLatviaHandler implements GatewayHandler
{
    public function getTransactionReference(Request $request)
    {
        $transactionRef = $request->get('trans_id', null);
        return $transactionRef;
    }

    public function getCompletePurchaseArguments(Transaction $transaction)
    {
        $purchaseParameters = $transaction->options['purchase'];
        $purchaseParameters['transactionReference'] = $transaction->token_reference;
        return $purchaseParameters;
    }
}