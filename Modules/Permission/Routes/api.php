 <?php

use Illuminate\Support\Facades\Route;
use Modules\Permission\Http\Controllers\PermissionController;

Route::group(['middleware' => 'jwt.auth'], function () {
    Route::prefix('/permissions')->controller(PermissionController::class)->group(function () {
        Route::get('/', 'index')->middleware(['role_or_permission:admin|fetch permissions']);
        Route::post('/', 'create')->middleware(['role_or_permission:admin|create permission']);
        Route::get('/{slug}', 'show')->middleware(['role_or_permission:admin|show permission']);
        Route::put('/{slug}', 'update')->middleware(['role_or_permission:admin|update permission']);
        Route::delete('/{slug}', 'delete')->middleware(['role_or_permission:admin|delete permission']);

    });
});


