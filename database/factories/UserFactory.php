<?php

namespace Database\Factories;

use App\Enums\Gender;
use App\Enums\UserType;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'phone' => '09'.$this->faker->numerify('########'),
            'gender' => $this->faker->randomElement(Gender::cases())->value,
            'status' => true,
            'user_type' => UserType::Customer->value,
            'location_id' => Location::query()->inRandomOrder()->value('id'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    public function manager(): static
    {
        return $this->state(fn () => ['user_type' => UserType::Manager->value]);
    }

    public function employee(): static
    {
        return $this->state(fn () => ['user_type' => UserType::Employee->value]);
    }

    public function unverified(): static
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }
}
