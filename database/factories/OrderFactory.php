<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $products = [];
        for ($i = 0; $i < $this->faker->numberBetween(1, 5); $i++) {
            $products[] = [
                'product' => $this->faker->uuid,
                'quantity' => $this->faker->numberBetween(1, 10),
            ];
        }
        $orderStatus = \App\Models\OrderStatus::inRandomOrder()->first();
        return [
            'uuid' => $this->faker->uuid,
            'products' => $products,
            'address' => [
                'billing' => $this->faker->address,
                'shipping' => $this->faker->address,
            ],
            'delivery_fee' => $this->faker->numberBetween(0, 50),
            'amount' => $this->faker->numberBetween(100, 1000),
            'shipped_at' => $this->faker->boolean ? $this->faker->dateTimeBetween('-1 year', 'now') : null,
            'user_id' => \App\Models\User::factory(),
            'order_status_id' => $orderStatus ?? \App\Models\OrderStatus::factory(),
            'payment_id' => \App\Models\Payment::factory(),
        ];
    }
}
