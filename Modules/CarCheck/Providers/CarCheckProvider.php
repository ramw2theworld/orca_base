<?php

namespace Modules\CarCheck\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\CarCheck\Repositories\Contracts\CarCheckRepositoryInterface;
use Modules\CarCheck\Repositories\Eloquent\CarCheckRepository;
use Illuminate\Support\Facades\Route;

class CarCheckProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        Route::prefix('api/v1')
            ->middleware('api')
            ->group(__DIR__.'/../Routes/api.php');

        $this->loadMigrationsFrom(__DIR__.'/../Database/migrations');

        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('carcheck.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'carcheck'
        );
        

        $this->app->bind(CarCheckRepositoryInterface::class, CarCheckRepository::class);
    }
}
