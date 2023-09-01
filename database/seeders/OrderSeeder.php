<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Order::factory()->count(100)->create();
        // Create for each user 100 orders
        \App\Models\User::all()->each(function (\App\Models\User $user) {
            \App\Models\Order::factory()->count(100)->create([
                'user_id' => $user->id,
            ]);
        });
    }
}
