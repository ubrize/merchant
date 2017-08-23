<?php

namespace Arbory\Merchant\Utils;

use Arbory\Merchant\Models\Transaction;
use Illuminate\Http\Request;

interface GatewayHandler{
    public function getTransactionReference(Request $request);
    public function getCompletePurchaseArguments(Transaction $transaction);
}