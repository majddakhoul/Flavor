<?php

namespace Database\Factories;

use App\Enums\ReservationStatus;
use App\Enums\ReservationType;
use App\Models\Customer;
use App\Support\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'reservation_code' => Ticket::code(8),
            'party_size' => $this->faker->numberBetween(2, 8),
            'status' => ReservationStatus::Confirmed->value,
            'type' => ReservationType::Application->value,
            'date' => now()->addDays($this->faker->numberBetween(1, 14)),
            'special_requests' => null,
            'customer_id' => Customer::factory(),
            'employee_id' => null,
            'order_id' => null,
        ];
    }
}
