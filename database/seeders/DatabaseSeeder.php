<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Items;      // Plural
use App\Models\ItemsStocks; // Plural
use App\Models\Warehouses; // Plural
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(100)->create();

        User::factory()->create([
            'first_name' => 'Ian',
            'last_name' => 'Sopian',
            'email' => 'admin@example.com',
            'password' => 'carmen321',
        ]);
    }
}
