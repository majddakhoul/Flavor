<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'notes' => null,
            'status' => OrderStatus::Pending->value,
            'order_type' => OrderType::Takeaway->value,
            'dated_at' => now(),
            'customer_id' => Customer::factory(),
            'employee_id' => null,
            'reservation_id' => null,
            'location_id' => null,
        ];
    }
}
