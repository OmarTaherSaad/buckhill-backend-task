<?php

use App\Models\User;
use App\Models\Auth\JwtToken;
use Lcobucci\JWT\Configuration;

if (!function_exists('issueToken')) {
    /**
     * Create a JWT token
     */
    function issueToken(User $user): string
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
     */
    function validateToken(?string $bearerToken = null): \App\Models\User|false
    {
        try {
            if (!$bearerToken) {
                return false;
            }
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
     */
    function revokeToken(string $bearerToken): bool
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
