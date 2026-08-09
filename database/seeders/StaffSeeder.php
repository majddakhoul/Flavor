<?php

namespace Database\Seeders;

use App\Enums\EmployeePosition;
use App\Enums\Gender;
use App\Enums\UserType;
use App\Models\Employee;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        $locationId = Location::query()->value('id');

        $staff = [
            ['Rania', 'Haddad', 'manager@flavor.test', Gender::Female, UserType::Manager, EmployeePosition::Manager, 2600000, 400000],
            ['Samer', 'Khoury', 'chef@flavor.test', Gender::Male, UserType::Employee, EmployeePosition::Chef, 1900000, 250000],
            ['Nour', 'Abbas', 'waiter@flavor.test', Gender::Female, UserType::Employee, EmployeePosition::Waiter, 950000, 90000],
            ['Fadi', 'Naser', 'delivery@flavor.test', Gender::Male, UserType::Employee, EmployeePosition::Delivery, 880000, 70000],
            ['Omar', 'Saleh', 'security@flavor.test', Gender::Male, UserType::Employee, EmployeePosition::Security, 820000, 40000],
            ['Lina', 'Darwish', 'waiter2@flavor.test', Gender::Female, UserType::Employee, EmployeePosition::Waiter, 930000, 60000],
            ['Karim', 'Ayoub', 'chef2@flavor.test', Gender::Male, UserType::Employee, EmployeePosition::Chef, 1550000, 150000],
        ];

        foreach ($staff as $index => [$first, $last, $email, $gender, $type, $position, $salary, $bonus]) {
            $user = User::query()->firstOrCreate(
                ['email' => $email],
                [
                    'first_name' => $first,
                    'last_name' => $last,
                    'password' => Hash::make('password'),
                    'phone' => '0955'.str_pad((string) (100000 + $index), 6, '0', STR_PAD_LEFT),
                    'gender' => $gender->value,
                    'status' => true,
                    'user_type' => $type->value,
                    'location_id' => $locationId,
                    'email_verified_at' => now(),
                ]
            );

            Employee::query()->firstOrCreate(
                ['user_id' => $user->id],
                [
                    'national_id' => '0'.str_pad((string) (1000000000 + $index), 10, '0', STR_PAD_LEFT),
                    'position' => $position->value,
                    'salary' => $salary,
                    'bonus' => $bonus,
                    'notes' => null,
                    'hire_date' => now()->subMonths(6 + $index * 5)->toDateString(),
                    'birth_date' => now()->subYears(28 + $index)->toDateString(),
                ]
            );
        }
    }
}
