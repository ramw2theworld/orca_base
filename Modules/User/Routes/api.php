<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\AuthController;
use Modules\User\Http\Controllers\UserController;

 Route::prefix('api/users')->controller(UserController::class)
    ->group(function () {
     Route::post('/', 'create')->name('users.create');
 });

 Route::prefix('api/users')->middleware(['jwt.auth'])
    ->controller(UserController::class)
    ->group(function () {
        Route::get('/', 'index')->name('users.index');
        Route::get('/{username}', 'show')->name('users.show');
        Route::put('/{username}', 'update')->name('users.update');
        Route::delete('/{username}', 'delete')->name('users.delete');
 });

 Route::prefix('api')->controller(AuthController::class)->group(function () {
    Route::post('/login', 'login')->name('user.login');
    Route::post('/logout', 'logout')->name('logout')->middleware('jwt.auth');
});

 Route::prefix('api')->middleware('jwt.auth')->group(function () {
    Route::controller(UserController::class)->prefix('users')
        ->group(function () {
            Route::post('/{username}/attach-detach-permissions-users', 'attachDetachPermissionToUser')
                ->middleware('permission: attach or detach permissions to user');
    });
});


