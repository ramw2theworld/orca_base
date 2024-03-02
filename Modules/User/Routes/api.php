 <?php

 use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\UserController;

 Route::prefix('api/users')->group(function () {
     Route::get('/', [UserController::class,'index'])->name('index');
 });
