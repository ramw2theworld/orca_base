<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\AuthController;
use Modules\User\Http\Controllers\UserController;

Route::prefix('/users')
   ->controller(UserController::class)
   ->group(function () {

    Route::post('/', 'create')->withoutMiddleware(['jwt.auth']);

    Route::middleware(['jwt.auth', 'auth'])
        ->group(function () {
            Route::get('/', 'index')->name('users.index')
                ->middleware(['role_or_permission:admin|fetch roles']);

            Route::get('/{username}', 'show')->name('users.show');

            Route::put('/{username}', 'update')->name('users.update');

            Route::delete('/{username}', 'delete')->name('users.delete');

            Route::post('/{username}/attach-detach-permissions-users', 'attachDetachPermissionsToUser')
                ->middleware(['role_or_permission:admin|attach or detach permissions to user']);

            Route::post('/{username}/attach-detach-roles-user', 'attachDetachRolesToUser')
                ->middleware(['role_or_permission:admin|attach or detach roles to user']);
            
        });
});

 Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login')->name('login')
        ->withoutMiddleware(['jwt.auth', 'auth']);
    
    Route::post('/logout', 'logout')->name('logout')->middleware(['jwt.auth', 'auth']);
});



