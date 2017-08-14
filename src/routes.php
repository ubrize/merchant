<?php

Route::get('payments/{gateway}/purchase/{transactionId?}', Arbory\Payments\PaymentsController::class . '@purchase')->name('purchase');

//Route::get('payments/{gatewayHandler}/purchase/{transactionId?}', Arbory\Payments\PaymentsController::class . '@purchase')->name('purchase');