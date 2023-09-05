<?php

namespace OmarTaherSaad\StripePayments;

use Stripe\Stripe;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class StripePaymentServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        $this->registerRoutes();
        if ($this->app->runningInConsole()) {
            // Publish config file
            $this->publishes([
                __DIR__ . '/../config/stripe-payments.php' => $this->app->configPath('stripe-payments.php'),
            ], 'stripe-payments-config');
            // Load migrations
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }

        Stripe::setAppInfo(
            'Stripe Payments for Laravel',
            StripePayment::VERSION,
        );
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/stripe-payments.php',
            'stripe-payments'
        );
    }

    /**
     * Register the package routes.
     */
    protected function registerRoutes(): void
    {
        Route::group([
            'prefix' => config('stripe-payments.path_prefix'),
            'namespace' => 'OmarTaherSaad\StripePayments\Http\Controllers',
            'as' => 'stripe-payment.',
        ], function (): void {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });
    }
}
