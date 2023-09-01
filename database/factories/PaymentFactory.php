<?php

namespace Database\Factories;

use App\Enums\PaymentType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(PaymentType::cases());
        $details = [];
        switch ($type) {
            case PaymentType::Credit_Card:
                $details = [
                    'holder_name' => $this->faker->name,
                    'number' => $this->faker->creditCardNumber,
                    'cvv' => $this->faker->randomNumber(3),
                    'expire_date' => $this->faker->creditCardExpirationDate,
                ];
                break;
            case PaymentType::Cash_On_Delivery:
                $details = [
                    'first_name' => $this->faker->firstName,
                    'last_name' => $this->faker->lastName,
                    'address' => $this->faker->address,
                ];
                break;
            case PaymentType::Bank_Transfer:
                $details = [
                    'swift' => $this->faker->swiftBicNumber,
                    'iban' => $this->faker->iban('IT'),
                    'name' => $this->faker->name,
                ];
                break;
        }
        return [
            'uuid' => $this->faker->uuid,
            'type' => $type,
            'details' => $details,
        ];
    }
}
