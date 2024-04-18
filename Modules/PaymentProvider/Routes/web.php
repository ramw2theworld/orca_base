<?php

use Illuminate\Support\Facades\Route;

Route::get('/paymentprovider', function () {
    return view('paymentprovider.index');
});
