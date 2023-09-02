<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;

class JwtTokenTest extends TestCase
{
    /**
     * Test issuing a token
     */
    public function test_issuing_token(): void
    {
        $user = User::factory()->create();
        $token = issueToken($user);
        $this->assertIsString($token);
    }

    /**
     * Test validating a token
     */
    public function test_validating_token(): void
    {
        $user = User::factory()->create();
        $token = issueToken($user);
        $user = validateToken($token);
        $this->assertInstanceOf(User::class, $user);
    }

    /**
     * Test validation fails on invalid token
     */
    public function test_validation_fails_on_invalid_token(): void
    {
        $user = User::factory()->create();
        $token = issueToken($user);
        $user = validateToken($token . 'invalid');
        $this->assertFalse($user);
    }

    /**
     * Test revoking a token
     */
    public function test_revoking_token(): void
    {
        $user = User::factory()->create();
        $token = issueToken($user);
        $user = validateToken($token);
        $this->assertInstanceOf(User::class, $user);
        revokeToken($token);
        $this->assertFalse(validateToken($token));
    }

    /**
     * Test expired token
     */
    public function test_expired_token(): void
    {
        $user = User::factory()->create();
        $token = issueToken($user);
        // Travel 61 minutes into the future to expire the token
        $this->travel(61)->minutes();
        $user = validateToken($token);
        $this->assertFalse($user);
    }
}
