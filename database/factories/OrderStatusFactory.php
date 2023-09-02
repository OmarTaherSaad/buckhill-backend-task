<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderStatus>
 */
class OrderStatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = [
            'open',
            'pending payment',
            'paid',
            'shipped',
            'cancelled',
        ];
        return [
            'uuid' => $this->faker->uuid,
            'title' => $this->faker->randomElement($statuses),
        ];
    }
}
