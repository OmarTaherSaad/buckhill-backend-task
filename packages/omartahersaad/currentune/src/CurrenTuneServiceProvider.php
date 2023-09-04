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
            // Load migrations
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    protected function registerRoutes()
    {
        Route::group([
            'prefix' => config('conversion_route_path'),
            'namespace' => 'OmarTaherSaad\CurrenTune\Http\Controllers',
            'as' => 'currentune.',
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });
    }
}
