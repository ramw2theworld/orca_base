 <?php

 use Illuminate\Support\Facades\Route;
use Modules\Role\Http\Controllers\RoleController;

 Route::prefix('/api/roles')->group(function () {
    Route::get('/', [RoleController::class, 'index'])->name('index');  
 });
