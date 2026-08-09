<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Customer;
use App\Models\Meal;
use App\Models\Offer;
use App\Models\Ingredient;

class TestDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\Location::factory()->count(2)->create();

        Customer::factory()->count(5)->create();
        Meal::factory()->count(5)->create();
        Ingredient::factory()->count(10)->create();
        Offer::factory()->count(3)->create();
    }
}

