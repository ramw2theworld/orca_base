 <?php

 use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\UserController;

 Route::prefix('api/users')->group(function () {
     Route::get('/', [UserController::class,'index'])->name('user.index');
     Route::post('/', [UserController::class,'create'])->name('user.create');
     Route::get('/{username}', [UserController::class,'show'])->name('user.show');

 });
