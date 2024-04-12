<?php

namespace Database\Seeders;

use App\Models\AccountTransaction;
use App\Models\Client;
use App\Models\ClientAccount;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Client::factory()->count(50)->create();
        ClientAccount::factory()->count(100)->create();
        AccountTransaction::factory()->count(150)->create();
    }
}
