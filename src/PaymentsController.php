<?php

namespace Arbory\Payments;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Omnipay\Omnipay;

class PaymentsController extends Controller
{
    public function purchase($gatewayHandler, $transactionId = null){

        //TODO: load from config available gateways
        $gateways = [
            'FirstDataLatvia' => []
        ];

        $test = Omnipay::create('FirstDataLatvia');

        $supported = Omnipay::getSupportedGateways();

        //$gateway = Omnipay::create();

        dump($test, $supported);
    }
}
