<?php

namespace Database\Factories;

use App\Enums\EmployeePosition;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->employee(),
            'national_id' => $this->faker->unique()->numerify('###########'),
            'position' => EmployeePosition::Waiter->value,
            'salary' => $this->faker->numberBetween(600000, 1800000),
            'bonus' => $this->faker->numberBetween(0, 200000),
            'notes' => null,
            'hire_date' => $this->faker->dateTimeBetween('-5 years', '-1 month'),
            'birth_date' => $this->faker->dateTimeBetween('-45 years', '-20 years'),
        ];
    }

    public function position(EmployeePosition $position): static
    {
        return $this->state(fn () => ['position' => $position->value]);
    }
}
