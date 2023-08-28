<?php

use App\Models\Auth\JwtToken;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\UnencryptedToken;

if (!function_exists('issueToken')) {
    /**
     * Create a JWT token
     * @return string
     */
    function issueToken(User $user)
    {
        /** @var Lcobucci\JWT\Configuration */
        $configuration = resolve(Configuration::class);
        $token = $configuration->builder()
            ->issuedBy(config('app.url'))
            ->permittedFor(config('app.url'))
            ->identifiedBy(uniqid())
            ->issuedAt(new \DateTimeImmutable('now'))
            ->expiresAt(new \DateTimeImmutable('+1 hour'))
            ->withClaim('user_uuid', $user->uuid)
            ->getToken($configuration->signer(), $configuration->signingKey());

        //Store token in database
        $tokenData = [
            'unique_id' => $token->claims()->get('jti'),
            'token_title' => 'Login',
            'restrictions' => null,
            'permissions' => null,
            'expires_at' => $token->claims()->get('exp'),
        ];
        $user->tokens()->create($tokenData);

        return $token->toString();
    }
}

if (!function_exists('validateToken')) {
    /**
     * Validate a JWT token
     * @param string $token
     * @return \App\Models\User|false
     */
    function validateToken(string $bearerToken)
    {
        try {
            /** @var Lcobucci\JWT\Configuration */
            $configuration = resolve(Configuration::class);
            $parser = $configuration->parser();
            /** @var \Lcobucci\JWT\Token\Plain */
            $parsedToken = $parser->parse($bearerToken);
            /** @var JwtToken */
            $token = JwtToken::firstWhere('unique_id', $parsedToken->claims()->get('jti'));
            if (!$token || $token->isExpired()) {
                return false;
            }
            $validator = $configuration->validator();
            $constraints = $configuration->validationConstraints();
            foreach ($constraints as $constraint) {
                if (!$validator->validate($parsedToken, $constraint)) {
                    $token->revoke();
                    return false;
                }
            }
            $token->update([
                'last_used_at' => now(),
            ]);
            $user = $token->user;
        } catch (\Throwable $th) {
            return false;
        }
        return $user;
    }
}

if (!function_exists('revokeToken')) {
    /**
     * Revoke a JWT token
     * @param string $token
     * @return bool
     */
    function revokeToken(string $bearerToken)
    {
        try {
            /** @var Lcobucci\JWT\Configuration */
            $configuration = resolve(Configuration::class);
            $parser = $configuration->parser();
            /** @var \Lcobucci\JWT\Token\Plain */
            $parsedToken = $parser->parse($bearerToken);
            /** @var JwtToken */
            $token = JwtToken::firstWhere('unique_id', $parsedToken->claims()->get('jti'));
            $token->revoke();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
