<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LocationSeeder::class,
            CategorySeeder::class,
            IngredientSeeder::class,
            MealSeeder::class,
            OfferSeeder::class,
            TableSeeder::class,
            StaffSeeder::class,
            CustomerSeeder::class,
            ReservationSeeder::class,
            OrderSeeder::class,
            MaintenanceSeeder::class,
            RatingSeeder::class,
        ]);

        Cache::flush();

        $this->command->newLine();
        $this->command->info('Demo accounts (password: password)');
        $this->command->table(
            ['Role', 'Email'],
            [
                ['Manager', 'manager@flavor.test'],
                ['Chef', 'chef@flavor.test'],
                ['Waiter', 'waiter@flavor.test'],
                ['Delivery', 'delivery@flavor.test'],
                ['Security', 'security@flavor.test'],
                ['Customer', 'customer@flavor.test'],
            ]
        );
    }
}
