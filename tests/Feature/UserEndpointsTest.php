<?php

namespace Tests\Feature;

use App\Http\Resources\OrdersCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Database\Seeders\OrderSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class UserEndpointsTest extends TestCase
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
     * Test user registration
     */
    public function test_user_registration(): void
    {
        $data = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'testemail' . now()->getTimestamp() . '@testing.com',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
            'address' => '123 Test Street',
            'phone_number' => '01234567890',
            'is_marketing' => false,
        ];
        $response = $this->postJson(route('user.create'), $data);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'success' => true,
        ]);
        $this->assertDatabaseHas('users', [
            'email' => $data['email'],
        ]);
    }

    /**
     * Test user registration fails with invalid data
     */
    public function test_user_registration_fails_with_invalid_data(): void
    {
        $data = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'testemail' . now()->getTimestamp() . '@testing.com',
            'password' => 'Password1',
            // Use invalid password confirmation
            'password_confirmation' => 'Password12',
            'address' => '123 Test Street',
            'phone_number' => '01234567890',
            'is_marketing' => false,
        ];
        // Replace email with invalid email
        $response = $this->postJson(route('user.create'), array_merge($data, [
            'email' => 'invalid',
        ]));
        $response->assertStatus(422);
        // Expect errors for email and password
        $response->assertJsonStructure([
            'errors' => [
                'email',
                'password',
            ]
        ]);
        $this->assertDatabaseMissing('users', [
            'email' => $data['email'],
        ]);
    }

    /**
     * Test user registration fails with duplicate email
     */
    public function test_user_registration_fails_with_duplicate_email(): void
    {
        $this->seed(UserSeeder::class);
        $user = User::inRandomOrder()->first();
        $data = [
            'first_name' => 'Test',
            'last_name' => 'User',
            // Use existing email
            'email' => $user->email,
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
            'address' => '123 Test Street',
            'phone_number' => '01234567890',
            'is_marketing' => false,
        ];
        $response = $this->postJson(route('user.create'), $data);
        $response->assertStatus(422);
        // Expect error for email
        $response->assertJsonStructure([
            'errors' => [
                'email',
            ]
        ]);
    }

    /**
     * Test Get Authenticated User API
     */
    public function test_get_authenticated_user(): void
    {
        $this->seed(UserSeeder::class);
        $user = User::inRandomOrder()->first();
        $token = issueToken($user);
        $response = $this->getJson(route('user.showSelf'), [
            'Authorization' => 'Bearer ' . $token,
        ]);
        $response->assertStatus(200);
        // Expect user data
        $response->assertJsonFragment([
            'data' => (new UserResource($user))->toArray(request()),
        ]);
    }

    /**
     * Test Get Authenticated User API fails without token
     */
    public function test_get_authenticated_user_fails_without_token(): void
    {
        $response = $this->getJson(route('user.showSelf'));
        $response->assertStatus(401);
        $response->assertJsonMissingPath('data');
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

    /**
     * Test Update User API
     */
    public function test_update_user(): void
    {
        $data = [
            'first_name' => 'Updated Test',
            'last_name' => 'Updated User',
            'email' => 'updatedtestemail' . now()->getTimestamp() . '@testing.com',
            'password' => 'NewPassword1',
            'password_confirmation' => 'NewPassword1',
            'address' => '123 New Test Street',
            'phone_number' => '91234567890',
            'is_marketing' => false,
        ];
        $this->seed(UserSeeder::class);
        $user = User::inRandomOrder()->first();
        $token = issueToken($user);
        $response = $this->putJson(route('user.updateSelf'), $data, [
            'Authorization' => 'Bearer ' . $token,
        ]);
        //Assert response is correct
        $response->assertStatus(200);
        //Assert user data is correct
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'address' => $data['address'],
            'phone_number' => $data['phone_number'],
            'is_marketing' => $data['is_marketing'],
        ]);
    }

    /**
     * Test Update User API fails without token
     */
    public function test_update_user_fails_without_token(): void
    {
        $data = [
            'first_name' => 'Updated Test',
            'last_name' => 'Updated User',
            'email' => 'updatedtestemail' . now()->getTimestamp() . '@testing.com',
            'password' => 'NewPassword1',
            'password_confirmation' => 'NewPassword1',
            'address' => '123 New Test Street',
            'phone_number' => '91234567890',
            'is_marketing' => false,
        ];
        $this->seed(UserSeeder::class);
        $user = User::inRandomOrder()->first();
        $response = $this->putJson(route('user.updateSelf'), $data);
        //Assert response is correct
        $response->assertStatus(401);
        //Assert user data is correct
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'address' => $data['address'],
            'phone_number' => $data['phone_number'],
            'is_marketing' => $data['is_marketing'],
        ]);
    }

    /**
     * Test Delete User API
     */
    public function test_delete_user(): void
    {
        $this->seed(UserSeeder::class);
        $user = User::inRandomOrder()->first();
        $token = issueToken($user);
        $response = $this->deleteJson(route('user.destroySelf'), [], [
            'Authorization' => 'Bearer ' . $token,
        ]);
        //Assert response is correct
        $response->assertStatus(200);
        //Assert user is deleted
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    /**
     * Test Delete User API fails without token
     */
    public function test_delete_user_fails_without_token(): void
    {
        $this->seed(UserSeeder::class);
        $user = User::inRandomOrder()->first();
        $response = $this->deleteJson(route('user.destroySelf'));
        //Assert response is correct
        $response->assertStatus(401);
        //Assert user is not deleted
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);
    }

    /**
     * Test Get User Orders API
     */
    public function test_get_user_orders(): void
    {
        $this->seed(UserSeeder::class);
        $user = User::inRandomOrder()->first();
        $token = issueToken($user);
        $response = $this->getJson(route('user.orders.index', [
            'limit' => 10,
        ]), [
            'Authorization' => 'Bearer ' . $token,
        ]);
        //Assert response is correct
        $response->assertStatus(200);
        //Assert user orders are correct
        $response->assertJsonFragment([
            'data' => (new OrdersCollection($user->orders()->paginate(10)))->toArray(request()),
        ]);
    }

    /**
     * Test Get User Orders API fails without token
     */
    public function test_get_user_orders_fails_without_token(): void
    {
        $this->seed(UserSeeder::class);
        $response = $this->getJson(route('user.orders.index', [
            'limit' => 10,
        ]));
        //Assert response is correct
        $response->assertStatus(401);
    }
}
