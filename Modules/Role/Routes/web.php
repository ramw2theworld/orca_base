<?php

use Illuminate\Support\Facades\Route;
use Modules\Role\Http\Controllers\RoleController;


Route::group(['middleware' => 'jwt.auth'], function () {
    Route::prefix('api/roles')->group(function (){
        Route::get('/', [RoleController::class, 'index'])->name('roles.index');
        Route::post('/', [RoleController::class, 'create'])->name('roles.create');
        Route::get('/{slug}', [RoleController::class, 'show'])->name('roles.show');
        Route::put('/{slug}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/{slug}', [RoleController::class, 'delete'])->name('roles.delete');

    });
});