<?php

namespace OmarTaherSaad\CurrenTune;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CurrenTuneServiceProvider extends ServiceProvider
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
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/currentune.php',
            'currentune'
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
            'prefix' => config('currentune.conversion_route_path'),
            'namespace' => 'OmarTaherSaad\CurrenTune\Http\Controllers',
            'as' => 'currentune.',
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        });
    }
}
