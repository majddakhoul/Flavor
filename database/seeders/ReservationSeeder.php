<?php

namespace Database\Seeders;

use App\Enums\ReservationStatus;
use App\Enums\ReservationType;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Reservation;
use App\Models\Table;
use App\Support\Ticket;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::query()->pluck('id')->all();
        $employee = Employee::query()->value('id');
        $tables = Table::query()->orderBy('table_number')->get();

        if (empty($customers) || $tables->isEmpty()) {
            return;
        }

        $plan = [
            [-6, 19, 2, 4, ReservationStatus::Completed, ReservationType::Application],
            [-4, 20, 2, 6, ReservationStatus::Completed, ReservationType::Locally],
            [-2, 13, 2, 2, ReservationStatus::Cancelled, ReservationType::Application],
            [0, 19, 3, 4, ReservationStatus::Confirmed, ReservationType::Application],
            [0, 21, 2, 2, ReservationStatus::Pending, ReservationType::Application],
            [1, 14, 2, 8, ReservationStatus::Confirmed, ReservationType::Locally],
            [2, 20, 3, 6, ReservationStatus::Confirmed, ReservationType::Application],
            [4, 19, 2, 4, ReservationStatus::Pending, ReservationType::Application],
            [6, 21, 2, 2, ReservationStatus::Confirmed, ReservationType::Application],
        ];

        foreach ($plan as $index => [$dayOffset, $hour, $hours, $party, $status, $type]) {
            $start = now()->addDays($dayOffset)->setTime($hour, 0);
            $end = $start->copy()->addHours($hours);

            $table = $tables[$index % $tables->count()];

            $reservation = Reservation::query()->create([
                'reservation_code' => Ticket::code(8),
                'party_size' => $party,
                'status' => $status->value,
                'type' => $type->value,
                'date' => $start->toDateString(),
                'special_requests' => $index % 3 === 0 ? 'Quiet corner if possible.' : null,
                'customer_id' => $customers[$index % count($customers)],
                'employee_id' => $type === ReservationType::Locally ? $employee : null,
                'order_id' => null,
            ]);

            $reservation->tables()->attach($table->id, [
                'start_time' => $start->toDateTimeString(),
                'end_time' => $end->toDateTimeString(),
            ]);
        }
    }
}
