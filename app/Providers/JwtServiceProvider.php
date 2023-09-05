<?php

namespace App\Providers;

use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Illuminate\Support\ServiceProvider;

class JwtServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(Configuration::class, function ($app) {
            $privateKeyPath = storage_path(config('jwt.private_key_path'));
            if (!file_exists($privateKeyPath)) {
                throw new \Exception('Private key not found');
            }
            $publicKeyPath = storage_path(config('jwt.public_key_path'));
            if (!file_exists($publicKeyPath)) {
                throw new \Exception('Public key not found');
            }
            $config = Configuration::forAsymmetricSigner(
                new Signer\Rsa\Sha256(),
                InMemory::file($privateKeyPath),
                InMemory::file($publicKeyPath)
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
