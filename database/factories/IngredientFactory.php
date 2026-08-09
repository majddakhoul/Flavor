<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class IngredientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'unit' => $this->faker->randomElement(['kg', 'g', 'litre', 'piece']),
            'stock_quantity' => $this->faker->numberBetween(10, 400),
            'unit_cost' => $this->faker->numberBetween(500, 40000),
            'is_active' => true,
        ];
    }

    public function low(): static
    {
        return $this->state(fn () => ['stock_quantity' => $this->faker->numberBetween(0, 8)]);
    }
}
