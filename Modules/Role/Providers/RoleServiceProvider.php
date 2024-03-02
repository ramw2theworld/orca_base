<?php

namespace Modules\Role\Providers;

use Illuminate\Routing\Route;
use Illuminate\Support\ServiceProvider;

class RoleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');
        $this->loadMigrationsFrom(__DIR__.'/../Database/migrations');

    }

    public function register()
    {
        // Register bindings in the container.
    }
}
