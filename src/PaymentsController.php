<?php

namespace Arbory\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    public function handleGatewayResponse(string $gateway, Request $request){
        dd($gateway, $request);
    }
}
