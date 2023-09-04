<?php

namespace App\Providers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key\InMemory;

class JwtServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(Configuration::class, function ($app) {
            $config = Configuration::forAsymmetricSigner(
                new Signer\Rsa\Sha256(),
                InMemory::file(storage_path(config('jwt.private_key_path'))),
                InMemory::file(storage_path(config('jwt.public_key_path')))
            );
            $config->setValidationConstraints(
                new \Lcobucci\JWT\Validation\Constraint\IssuedBy(config('app.url')),
                new \Lcobucci\JWT\Validation\Constraint\PermittedFor(config('app.url'))
            );
            return $config;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
