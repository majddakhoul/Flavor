<?php

namespace Database\Factories;

use App\Enums\TableLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

class TableFactory extends Factory
{
    public function definition(): array
    {
        return [
            'table_number' => 'T'.$this->faker->unique()->numberBetween(1, 999),
            'capacity' => $this->faker->randomElement([2, 4, 6, 8]),
            'location' => $this->faker->randomElement(TableLocation::cases())->value,
            'is_active' => true,
            'price_per_hour' => $this->faker->numberBetween(0, 50000),
            'description' => null,
        ];
    }
}
