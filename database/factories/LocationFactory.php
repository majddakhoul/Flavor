<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'country' => 'Syria',
            'region' => $this->faker->citySuffix(),
            'delivery_time' => sprintf('00:%02d:00', $this->faker->numberBetween(20, 55)),
            'state' => $this->faker->state(),
            'city' => $this->faker->city(),
            'street' => $this->faker->streetName(),
        ];
    }
}
