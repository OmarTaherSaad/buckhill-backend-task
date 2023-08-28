<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;

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

        return $token->toString();
    }
}

if (!function_exists('validateToken')) {
    /**
     * Validate a JWT token
     * @param string $token
     * @return \App\Models\User|false
     */
    function validateToken(string $token)
    {
        try {
            /** @var Lcobucci\JWT\Configuration */
            $configuration = resolve(Configuration::class);
            $parser = $configuration->parser();
            /** @var \Lcobucci\JWT\Token */
            $token = $parser->parse($token);
            if ($token->isExpired(new \DateTimeImmutable('now'))) {
                return false;
            }
            $validator = $configuration->validator();
            $constraints = $configuration->validationConstraints();
            foreach ($constraints as $constraint) {
                if (!$validator->validate($token, $constraint)) {
                    return false;
                }
            }
            $userUuid = $token->claims()->get('user_uuid');
            $user = User::firstWhere('uuid', $userUuid) ?? false;
        } catch (\Throwable $th) {
            return false;
        }
        return $user;
    }
}
