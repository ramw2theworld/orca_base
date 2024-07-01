<?php

use Illuminate\Support\Facades\Route;
use Modules\PaymentProvider\Http\Controllers\CurrencyController;
use Modules\PaymentProvider\Http\Controllers\PaymentController;
use Modules\PaymentProvider\Http\Controllers\PaymentProviderController;
use Modules\PaymentProvider\Http\Controllers\PlanController;

Route::resource('/payment-providers', PaymentProviderController::class)->withoutMiddleware(['jwt.auth']);
Route::resource('/currencies', CurrencyController::class)->withoutMiddleware(['jwt.auth']);
Route::resource('/plans', PlanController::class)->withoutMiddleware(['jwt.auth']);
Route::get('/payment', [PaymentController::class, 'index'])->withoutMiddleware(['jwt.auth']);
Route::get('/check-active-provider', [PaymentController::class, 'checkActiveProvider'])->withoutMiddleware(['jwt.auth']);

Route::prefix('payment')->group(function(){
    Route::post('/token/create', [PaymentController::class, 'createPaymentIntent'])
        ->name('payment.token.create')->withoutMiddleware(['jwt.auth']);

    Route::post('/process', [PaymentController::class, 'createSubscription'])
        ->name('payment.process')->withoutMiddleware(['jwt.auth']);
    
});

// Route::group(['prefix'=> 'payment'], function (){
//     Route::post('/process', [PaymentsController::class, 'createSubscription'])->name('payment.process');
//     Route::post('/token/create', [PaymentsController::class, 'createPaymentIntent'])->name('payment.token.create');
//     Route::post('/refund', [PaymentsController::class, 'refundPayment'])->name('payment.refund');
//     Route::get('/checkout/check', [PaymentsController::class, 'checkJob'])->name("payment.checkout.check");

// });