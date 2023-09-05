<?php

namespace OmarTaherSaad\CurrenTune;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CurrenTuneServiceProvider extends ServiceProvider
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
                __DIR__ . '/../config/currentune.php' => $this->app->configPath('currentune.php'),
            ], 'currentune-config');
            // Load migrations
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/currentune.php',
            'currentune'
        );
    }

    /**
     * Register the package routes.
     */
    protected function registerRoutes(): void
    {
        Route::group([
            'prefix' => config('currentune.conversion_route_path'),
            'namespace' => 'OmarTaherSaad\CurrenTune\Http\Controllers',
            'as' => 'currentune.',
        ], function (): void {
            $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        });
    }
}
