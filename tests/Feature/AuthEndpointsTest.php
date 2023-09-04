<?php

namespace Tests\Feature;

use App\Http\Resources\OrdersCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Database\Seeders\OrderSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AuthEndpointsTest extends TestCase
{
    /**
     * Test user login
     */
    public function test_user_login(): void
    {
        $this->seed(UserSeeder::class);
        $user = User::inRandomOrder()->first();
        $response = $this->postJson(route('user.login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'token',
                'user'
            ]
        ]);
        $tokenUser = validateToken($response->json('data.token'));
        $this->assertTrue($user->is($tokenUser));
    }

    /**
     * Test Login fails with invalid credentials
     */
    public function test_login_fails_with_invalid_credentials(): void
    {
        $this->seed(UserSeeder::class);
        $user = User::inRandomOrder()->first();
        $response = $this->postJson(route('user.login'), [
            'email' => $user->email,
            'password' => 'invalid',
        ]);
        $response->assertStatus(401);
        $response->assertJsonFragment([
            'success' => false,
        ]);
        $response->assertJsonMissingPath('data');
    }

    /**
     * Test logout
     */
    public function test_logout(): void
    {
        $this->seed(UserSeeder::class);
        $user = User::inRandomOrder()->first();
        $token = issueToken($user);
        $response = $this->getJson(route('user.logout'), [
            'Authorization' => 'Bearer ' . $token,
        ]);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'success' => true,
        ]);
        // Try to use token again
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson(route('user.showSelf'))->assertStatus(401);
    }

    /**
     * Test Forgot Password API
     */
    public function test_forgot_password(): void
    {
        $this->seed(UserSeeder::class);
        $user = User::inRandomOrder()->first();
        $response = $this->postJson(route('user.forgot-password'), [
            'email' => $user->email,
        ]);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'success' => true,
        ]);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'token'
            ]
        ]);
    }

    /**
     * Test Forgot Password API fails with invalid email
     */
    public function test_forgot_password_fails_with_invalid_email(): void
    {
        $this->seed(UserSeeder::class);
        $response = $this->postJson(route('user.forgot-password'), [
            'email' => 'invalid',
        ]);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'errors' => [
                'email',
            ]
        ]);
    }

    /**
     * Test Reset Password API
     */
    public function test_reset_password(): void
    {
        $this->seed(UserSeeder::class);
        $user = User::inRandomOrder()->first();
        $token = Password::getRepository()->create($user);
        $response = $this->postJson(route('user.reset-password'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
        ]);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'success' => true,
        ]);
    }

    /**
     * Test Reset Password API fails with invalid token
     */
    public function test_reset_password_fails_with_invalid_token(): void
    {
        $this->seed(UserSeeder::class);
        $user = User::inRandomOrder()->first();
        $response = $this->postJson(route('user.reset-password'), [
            'token' => 'invalid-token',
            'email' => $user->email,
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
        ]);
        $response->assertStatus(400);
        $response->assertJsonFragment([
            'success' => false,
        ]);
    }

    /**
     * Test Reset Password API fails with invalid email
     */
    public function test_reset_password_fails_with_invalid_email(): void
    {
        $this->seed(UserSeeder::class);
        $user = User::inRandomOrder()->first();
        $token = Password::getRepository()->create($user);
        $response = $this->postJson(route('user.reset-password'), [
            'token' => $token,
            'email' => 'invalid',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
        ]);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'errors' => [
                'email',
            ]
        ]);
    }

    /**
     * Test Reset Password API fails with another user's token
     */
    public function test_reset_password_fails_with_another_users_token(): void
    {
        $this->seed(UserSeeder::class);
        $user = User::inRandomOrder()->first();
        $anotherUser = User::where('id', '!=', $user->id)->first();
        $token = Password::getRepository()->create($anotherUser);
        $response = $this->postJson(route('user.reset-password'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
        ]);
        $response->assertStatus(400);
        $response->assertJsonFragment([
            'success' => false,
        ]);
    }
}
