<?php

namespace App\Providers;

use App\Services\Auth\JwtGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Add JWT Auth Guard driver to Auth
        Auth::extend('jwt', function ($app, $name, array $config) {
            $provider = Auth::createUserProvider($config['provider']);
            if (!$provider) {
                throw new \InvalidArgumentException('Auth user provider not found');
            }
            return new JwtGuard($provider, $app->make('request'));
        });

        //Set Password validation rules
        Password::defaults(function () {
            return Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers();
        });
    }
}
