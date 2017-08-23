<?php

namespace Arbory\Merchant\Utils;

use Arbory\Merchant\Models\Transaction;
use Illuminate\Http\Request;

abstract class GatewayHandler{
    public abstract function getTransactionReference(Request $request) : string ;

    public function getCompletePurchaseArguments(Transaction $transaction) : array
    {
        return [];
    }

    public function getPurchaseArguments(Transaction $transaction) : array
    {
        return [];
    }
}