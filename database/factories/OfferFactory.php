<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OfferFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->unique()->words(3, true),
            'description' => $this->faker->sentence(14),
            'discount_amount' => $this->faker->numberBetween(5, 35),
            'is_active' => true,
            'start_date' => now()->subDays(3),
            'end_date' => now()->addDays(14),
        ];
    }
}
