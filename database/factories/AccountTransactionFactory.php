<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AccountTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountTransactionFactory extends Factory
{
    public function definition(): array
    {
        $currencies = ['USD', 'EUR', 'GBP'];
        $types = [
            AccountTransaction::TRANSACTION_TYPE_DEPOSIT,
            AccountTransaction::TRANSACTION_TYPE_WITHDRAWAL,
            AccountTransaction::TRANSACTION_TYPE_TRANSFER,
        ];

        $type = $types[array_rand($types)];

        return [
            'client_account_id' => fake()->numberBetween(1, 5),
            'reference_account_id' => $type === AccountTransaction::TRANSACTION_TYPE_TRANSFER
                ? fake()->numberBetween(50, 100)
                : null,
            'transaction_type' => $type,
            'transaction_amount' => fake()->randomFloat(2),
            'transaction_currency' => $currencies[array_rand($currencies)],
        ];
    }
}
