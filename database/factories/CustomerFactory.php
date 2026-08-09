<?php

namespace Database\Factories;

use App\Enums\Allergy;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'allergies' => $this->faker->boolean(30) ? $this->faker->randomElement(Allergy::cases())->value : null,
            'favorite_categories' => null,
            'ban' => false,
            'ban_date' => null,
        ];
    }

    public function banned(): static
    {
        return $this->state(fn () => ['ban' => true, 'ban_date' => now()->subDays(2)]);
    }
}
