<?php

namespace Database\Factories;

use App\Enums\MealAvailability;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class MealFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(2, true),
            'prep_time' => sprintf('00:%02d:00', $this->faker->numberBetween(5, 45)),
            'is_vegetarian' => $this->faker->boolean(30),
            'percentage' => $this->faker->numberBetween(25, 60),
            'description' => $this->faker->sentence(12),
            'availability' => MealAvailability::Available->value,
            'category_id' => Category::factory(),
            'picture_id' => null,
        ];
    }
}
