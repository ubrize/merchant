<?php

Route::any('payments/{gateway}', Arbory\Merchant\PaymentsController::class . '@handleGatewayResponse');

