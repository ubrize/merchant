<?php

Route::any('payments/{gateway}', Arbory\Payments\PaymentsController::class . '@handleGatewayResponse');

