<?php

namespace Modules\Role\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\Role\Repositories\Contracts\RoleRepositoryInterface;
use Modules\Role\Repositories\Eloquent\RoleRepository;

class RoleServiceProvider extends ServiceProvider
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
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
    }
}
