<?php

use Illuminate\Support\Facades\Route;
use Modules\PaymentProvider\Http\Controllers\CurrencyController;
use Modules\PaymentProvider\Http\Controllers\PaymentProviderController;

Route::resource('/payment-providers', PaymentProviderController::class)->withoutMiddleware(['jwt.auth']);
Route::resource('/currencies', CurrencyController::class)->withoutMiddleware(['jwt.auth']);
