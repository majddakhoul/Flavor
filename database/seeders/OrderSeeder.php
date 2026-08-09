<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Location;
use App\Models\Meal;
use App\Models\Offer;
use App\Models\Order;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::query()->pluck('id')->all();
        $employees = Employee::query()->pluck('id')->all();
        $locations = Location::query()->pluck('id')->all();
        $meals = Meal::query()->pluck('id')->all();
        $offers = Offer::query()->pluck('id')->all();

        if (empty($customers) || empty($meals)) {
            return;
        }

        $statuses = [
            OrderStatus::Completed, OrderStatus::Completed, OrderStatus::Completed,
            OrderStatus::Confirmed, OrderStatus::Confirmed, OrderStatus::Pending,
        ];

        $types = [OrderType::Delivery, OrderType::Takeaway, OrderType::Reservation];

        for ($index = 0; $index < 42; $index++) {
            $status = $statuses[$index % count($statuses)];
            $type = $types[$index % count($types)];
            $placedAt = now()->subDays(intdiv($index, 3))->setTime(11 + ($index % 10), ($index * 7) % 60);

            $order = Order::query()->create([
                'notes' => $index % 5 === 0 ? 'Please add extra bread.' : null,
                'status' => $status->value,
                'order_type' => $type->value,
                'dated_at' => $placedAt->toDateTimeString(),
                'customer_id' => $customers[$index % count($customers)],
                'employee_id' => $type === OrderType::Delivery && $employees ? $employees[$index % count($employees)] : null,
                'reservation_id' => null,
                'location_id' => $type === OrderType::Delivery && $locations ? $locations[$index % count($locations)] : null,
            ]);

            $order->forceFill([
                'created_at' => $placedAt,
                'updated_at' => $placedAt,
            ])->save();

            $lines = [];
            $picks = 2 + ($index % 3);

            for ($line = 0; $line < $picks; $line++) {
                $mealId = $meals[($index + $line * 3) % count($meals)];
                $lines[$mealId] = ['quantity' => 1 + (($index + $line) % 3), 'notes' => null];
            }

            $order->meals()->sync($lines);

            if ($offers && $index % 4 === 0) {
                $order->offers()->sync([
                    $offers[$index % count($offers)] => ['quantity' => 1, 'notes' => null],
                ]);
            }
        }
    }
}
