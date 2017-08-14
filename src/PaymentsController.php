<?php

namespace Arbory\Payments;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Arbory\Payments\Events\Initialized;

class PaymentsController extends Controller
{
    public function purchase($gateway, $transactionId = null){
        $gatewayObj = \Omnipay::gateway($gateway);

        $response = $gatewayObj->purchase([
            'amount'   => '10.00',
            'currency' => 'EUR',
            'description' => 'asd',
            'clientIp' => '195.62.153.34'
        ])->send();

        if ($response->isSuccessful()) {
            // Payment was successful
            echo  $response->getMessage();
        } elseif ($response->isRedirect()) {
            // Redirect to offsite payment gateway
            $response->redirect();
        } else {
            // Payment failed
            echo $response->getMessage();
        }

        dd($gatewayObj);
    }


}
