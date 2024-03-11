<?php

use Illuminate\Support\Facades\Route;
use Modules\Role\Http\Controllers\RoleController;

Route::prefix('/roles')->middleware(['jwt.auth'])
   ->controller(RoleController::class)
   ->group(function () {
      Route::get('/', 'index')
         ->name('roles.index')
         ->middleware(['role_or_permission:admin|fetch roles']);

      Route::post('/', 'create')
         ->name('roles.create')
         ->middleware(['role_or_permission:admin|create role']);

      Route::get('/{slug}', 'show')
         ->name('roles.show')
         ->middleware(['role_or_permission:admin|show role']);

      Route::put('/{slug}', 'update')
         ->name('roles.update')
         ->middleware(['role_or_permission:admin|update role']);

      Route::delete('/{slug}', 'delete')
         ->name('roles.delete')
         ->middleware(['role_or_permission:admin|delete role']);

      // Attach permissions to role
      Route::post('/{slug}/attach-detach-permissions', 'attachOrDetachPermissionsToRole')
         ->name('roles.attach_detach_permissions')
         ->middleware(['role_or_permission:admin|attach or detach permissions to role']);
});