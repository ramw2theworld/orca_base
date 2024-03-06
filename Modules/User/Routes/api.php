<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\AuthController;
use Modules\User\Http\Controllers\UserController;

 Route::prefix('api/users')->group(function () {
     Route::get('/', [UserController::class,'index'])->name('users.index');
     Route::post('/', [UserController::class,'create'])->name('users.create');
     Route::get('/{username}', [UserController::class,'show'])->name('users.show');
     Route::put('/{username}', [UserController::class,'update'])->name('users.update');
     Route::delete('/{username}', [UserController::class,'delete'])->name('users.delete');
 });

 Route::prefix('api')->group(function () { 
    //login
    Route::post('/login', [AuthController::class, 'login'])->name('user.login');
 });
