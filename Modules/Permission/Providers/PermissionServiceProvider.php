<?php

namespace Modules\Permission\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Permission\Repositories\Contracts\PermissionRepositoryInterface;
use Modules\Permission\Repositories\Eloquent\PermissionRepository;
use Illuminate\Support\Facades\Route;

class PermissionServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        Route::prefix('api')
            ->middleware('api')
            ->group(__DIR__.'/../Routes/api.php');
        $this->loadMigrationsFrom(__DIR__.'/../Database/migrations');
    }

    public function register()
    {
        $this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);

    }
}

