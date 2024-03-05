<?php

namespace Modules\User\Providers;

use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
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
