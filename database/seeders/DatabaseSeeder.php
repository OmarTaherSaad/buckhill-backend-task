<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //Create an admin account for testing if one doesn't exist
        $email = 'admin@buckhill.co.uk';
        $user = \App\Models\User::where('email', $email)->first();
        if (!$user) {
            \App\Models\User::factory()->create([
                'first_name'    => 'Test',
                'last_name'     => 'Admin',
                'email'         => 'admin@buckhill.co.uk',
                'is_admin'      => true,
                'password'      => bcrypt('password'),
            ]);
        }

        // Run User, OrderStatus, Order, and Payment seeders
        $this->call([
            UserSeeder::class,
            OrderStatusSeeder::class,
            OrderSeeder::class,
            PaymentSeeder::class,
        ]);
    }
}
