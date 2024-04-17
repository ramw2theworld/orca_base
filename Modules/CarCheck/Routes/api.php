<?php

use Illuminate\Support\Facades\Route;
use Modules\CarCheck\Http\Controllers\CheckCarRegistrationController;

Route::controller(CheckCarRegistrationController::class)->group(function () {
    Route::get('/car-check/{reg_number}', 'checkRegNumber')->withoutMiddleware(['jwt.auth']);

});



