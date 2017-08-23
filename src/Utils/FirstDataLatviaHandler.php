<?php

namespace Arbory\Merchant\Utils;

use Illuminate\Http\Request;
use Arbory\Merchant\Models\Transaction;

class FirstDataLatviaHandler extends GatewayHandler
{
    public function getTransactionReference(Request $request) : string
    {
        $transactionRef = $request->get('trans_id', '');
        return $transactionRef;
    }

    public function getCompletePurchaseArguments(Transaction $transaction) : array
    {
        $purchaseParameters = $transaction->options['purchase'];
        $purchaseParameters['transactionReference'] = $transaction->token_reference;
        return $purchaseParameters;
    }
}