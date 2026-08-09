<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaintenanceFactory extends Factory
{
    public function definition(): array
    {
        $price = $this->faker->numberBetween(50000, 900000);
        $discount = $this->faker->numberBetween(0, 20);

        return [
            'maintenance_item' => $this->faker->words(2, true),
            'price' => $price,
            'discount' => $discount,
            'total_price' => (int) round($price * (1 - $discount / 100)),
            'notes' => null,
            'employee_id' => Employee::factory(),
        ];
    }
}
