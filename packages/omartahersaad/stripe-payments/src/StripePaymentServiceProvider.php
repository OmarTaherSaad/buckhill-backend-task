<?php

namespace OmarTaherSaad\StripePayment;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Stripe\Stripe;

class StripePaymentServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerRoutes();
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/stripe-payments.php' => $this->app->configPath('stripe-payments.php'),
            ], 'stripe-payments-config');
        }

        Stripe::setAppInfo(
            'Stripe Payments for Laravel',
            StripePayment::VERSION,
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/stripe-payments.php',
            'stripe-payments'
        );
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    protected function registerRoutes()
    {
        Route::group([
            'prefix' => config('stripe-payments.path-prefix'),
            'namespace' => 'OmarTaherSaad\StripePayment\Http\Controllers',
            'as' => 'stripe-payment.',
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });
    }
}
