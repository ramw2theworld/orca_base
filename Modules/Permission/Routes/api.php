 <?php

 use Illuminate\Support\Facades\Route;
use Modules\Permission\Http\Controllers\PermissionController;

 Route::prefix('/api/permissions')->group(function () {
     Route::get('/', [PermissionController::class,'index'])->name('index');
 });
