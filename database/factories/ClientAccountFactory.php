<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClientAccountFactory extends Factory
{
    public function definition(): array
    {
        $currencies = ['USD', 'EUR', 'GBP'];

        return [
            'client_id' => fake()->numberBetween(1, 50),
            'balance' => fake()->randomFloat(2),
            'currency' => $currencies[array_rand($currencies)],
        ];
    }
}
