<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Maintenance;
use Illuminate\Database\Seeder;

class MaintenanceSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::query()->pluck('id')->all();

        if (empty($employees)) {
            return;
        }

        $entries = [
            ['Grill hood deep clean', 320000, 10],
            ['Walk in fridge thermostat', 780000, 0],
            ['Dishwasher pump replacement', 540000, 5],
            ['Terrace lighting repair', 190000, 15],
            ['Coffee machine descaling', 120000, 0],
            ['Roof awning fabric change', 960000, 8],
        ];

        foreach ($entries as $index => [$item, $price, $discount]) {
            Maintenance::query()->firstOrCreate(
                ['maintenance_item' => $item],
                [
                    'price' => $price,
                    'discount' => $discount,
                    'total_price' => (int) round($price * (1 - $discount / 100)),
                    'notes' => null,
                    'employee_id' => $employees[$index % count($employees)],
                ]
            );
        }
    }
}
