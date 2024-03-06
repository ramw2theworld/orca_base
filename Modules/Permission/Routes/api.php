 <?php

use Illuminate\Support\Facades\Route;
use Modules\Permission\Http\Controllers\PermissionController;

Route::group(['middleware' => 'jwt.auth'], function () {
    Route::prefix('api/permissions')->group(function (){
        Route::get('/', [PermissionController::class, 'index'])->name('permissions.index');
        Route::post('/', [PermissionController::class, 'create'])->name('permissions.create');
        Route::get('/{slug}', [PermissionController::class, 'show'])->name('permissions.show');
        Route::put('/{slug}', [PermissionController::class, 'update'])->name('permissions.update');
        Route::delete('/{slug}', [PermissionController::class, 'delete'])->name('permissions.delete');

    });
});



