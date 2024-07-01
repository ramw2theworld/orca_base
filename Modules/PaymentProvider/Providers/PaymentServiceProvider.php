<?php

namespace Modules\PaymentProvider\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Modules\PaymentProvider\Repositories\Contracts\CurrencyRepositoryInterface;
use Modules\PaymentProvider\Repositories\Contracts\PaymentProviderRepositoryInterface;
use Modules\PaymentProvider\Repositories\Contracts\PaymentRepositoryInterface;
use Modules\PaymentProvider\Repositories\Contracts\PlanRepositoryInterface;
use Modules\PaymentProvider\Repositories\Eloquent\CurrencyRepository;
use Modules\PaymentProvider\Repositories\Eloquent\PaymentProviderRepository;
use Modules\PaymentProvider\Repositories\Eloquent\PaymentRepository;
use Modules\PaymentProvider\Repositories\Eloquent\PlanRepository;

class PaymentServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        Route::prefix('api')
            ->middleware('api')
            ->group(__DIR__.'/../Routes/api.php');

        $this->loadMigrationsFrom(__DIR__.'/../Database/migrations');
        $this->publishes([
            __DIR__.'/../Config/PaymentProvider.php' => config_path('paymentprovider.php'),
        ], 'config');

        // $stripePublicKey = config('paymentprovider.payment.stripe.public_key');
        // // Log or use the keys as required
        
        // Log::info("Stripe Public Key: $stripePublicKey");
    }

    public function register()
    {
        $this->app->bind(PaymentProviderRepositoryInterface::class, PaymentProviderRepository::class);
        $this->app->bind(CurrencyRepositoryInterface::class, CurrencyRepository::class);
        $this->app->bind(PlanRepositoryInterface::class, PlanRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);

        $this->mergeConfigFrom(
            __DIR__.'/../Config/PaymentProvider.php', 'paymentprovider'
        );
    }
}
