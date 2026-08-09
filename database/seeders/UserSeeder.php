<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Customer;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // كود إنشاء العملاء والموظفين يبقى كما هو...

        // إضافة مدير واحد
        $rawPassword = 'manager_password';
        $managerEmail = 'manager.restaurant@gmail.com';
        $waiterEmail = 'special.waiter@gmail.com';
        $waiterPassword = 'waiter_password';

        $user = User::firstOrCreate(
            ['email' => $waiterEmail],
            [
                'first_name' => 'Special',
                'last_name' => 'Waiter',
                'password' => Hash::make($waiterPassword),
                'phone' => '0912345678',
                'gender' => 'Male',
                'status' => false,
                'user_type' => 'Employee',
                'email_verified_at' => now(),
                'location_id' => 1
            ]
        );

        Employee::firstOrCreate(
            ['user_id' => $user->id],
            [
                'national_id' => Crypt::encryptString('12345678901234'),
                'position' => 'Waiter',
                'salary' => 500000,
                'bonus' => 100000,
                'notes' => 'Special waiter account for testing',
                'hire_date' => now(),
                'birth_date' => $faker->dateTimeBetween('-30 years', '-20 years'),
            ]
        );

        // إضافة طاهي ببيانات محددة
        $chefEmail = 'special.chef@gmail.com';
        $chefPassword = 'chef_password';

        $user = User::firstOrCreate(
            ['email' => $chefEmail],
            [
                'first_name' => 'Special',
                'last_name' => 'Chef',
                'password' => Hash::make($chefPassword),
                'phone' => '0922345678',
                'gender' => 'Male',
                'status' => false,
                'user_type' => 'Employee',
                'email_verified_at' => now(),
                'location_id' => 2
            ]
        );

        Employee::firstOrCreate(
            ['user_id' => $user->id],
            [
                'national_id' => Crypt::encryptString('22345678901234'),
                'position' => 'Chef',
                'salary' => 1500000,
                'bonus' => 200000,
                'notes' => 'Special chef account for testing',
                'hire_date' => now(),
                'birth_date' => $faker->dateTimeBetween('-40 years', '-30 years'),
            ]
        );
        $user = User::firstOrCreate(
            ['email' => $managerEmail],
            [
                'first_name' => 'Restaurant',
                'last_name' => 'Manager',
                'password' => Hash::make($rawPassword),
                'phone' => $this->generateSyrianPhone($faker),
                'gender' => 'Male',
                'status' => false,
                'user_type' => 'Manager',
                'email_verified_at' => now(),
                'location_id' => $faker->numberBetween(1, 300)
            ]
        );

        // إنشاء سجل الموظف للمدير
        Employee::firstOrCreate(
            ['user_id' => $user->id],
            [
                'national_id' => Crypt::encryptString($faker->unique()->numerify('##############')),
                'position' => 'Manager',
                'salary' => 0, // كما طلبت
                'bonus' => 0,  // كما طلبت
                'notes' => 'System Administrator',
                'hire_date' => now(),
                'birth_date' => $faker->dateTimeBetween('-50 years', '-35 years'),
            ]
        );
        // Create 100 customers
        for ($i = 0; $i < 200; $i++) {
            $user = User::create([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password'),
                'phone' => $this->generateSyrianPhone($faker),
                'gender' => $faker->randomElement(['Male', 'Female']),
                'status' => false,
                'user_type' => 'Customer',
                'email_verified_at' => now(),
                'location_id' => $faker->boolean(70) ? $faker->numberBetween(1, 300) : null
            ]);

            Customer::create([
                'allergies' => $faker->optional(0.3)->randomElement([
                    'Cows Milk Allergy',
                    'Egg Allergy',
                    'Peanut Allergy',
                    'Tree Nut Allergy',
                    'Fish Allergy',
                    'Shellfish Allergy',
                    'Wheat Allergy',
                    'Soy Allergy',
                    'Seed Allergies',
                    'Red Meat Allergy',
                    'Fruit Allergies',
                    'Vegetable Allergies',
                    'Spice Allergies'
                ]),
                'favorite_categories' => $faker->optional(0.5)->randomElement([
                    'Burgers',
                    'Pizza',
                    'Pasta',
                    'Salads',
                    'Desserts',
                    'Seafood',
                    'Vegetarian',
                    'Grilled',
                    'Sandwiches',
                    'Breakfast'
                ]),
                'user_id' => $user->id
            ]);
        }

        // Create chefs (10)
        for ($i = 0; $i < 10; $i++) {
            $this->createEmployee('Chef', $faker);
        }

        // Create security (2)
        for ($i = 0; $i < 2; $i++) {
            $this->createEmployee('Security', $faker);
        }

        // Create delivery (10)
        for ($i = 0; $i < 10; $i++) {
            $this->createEmployee('Delivery', $faker);
        }

        // Create waiters (20)
        for ($i = 0; $i < 20; $i++) {
            $this->createEmployee('Waiter', $faker);
        }
    }

    private function createEmployee(string $position, $faker): void
    {
        $user = User::create([
            'first_name' => $faker->firstName,
            'last_name' => $faker->lastName,
            'email' => $faker->unique()->safeEmail,
            'password' => Hash::make('password'),
            'phone' => $this->generateSyrianPhone($faker),
            'gender' => $faker->randomElement(['Male', 'Female']),
            'status' => false,
            'user_type' => 'Employee',
            'email_verified_at' => now(),
            'location_id' => $faker->numberBetween(1, 300)
        ]);

        $salaryRanges = [
            'Chef' => [800000, 2000000],
            'Waiter' => [400000, 800000],
            'Delivery' => [500000, 1000000],
            'Security' => [600000, 1200000]
        ];

        Employee::create([
            'national_id' => Crypt::encryptString($faker->unique()->numerify('##############')),
            'position' => $position,
            'salary' => $faker->numberBetween(...$salaryRanges[$position]),
            'bonus' => $faker->optional(0.4)->numberBetween(50000, 300000),
            'notes' => $faker->optional(0.3)->sentence,
            'hire_date' => $faker->dateTimeBetween('-5 years', 'now'),
            'birth_date' => $faker->dateTimeBetween('-50 years', '-18 years'), // Employee must be at least 18 years old
            'user_id' => $user->id
        ]);
    }
    /**
     * Generate valid Syrian phone number (either 10 digits starting with 09
     * or 14 digits starting with 009639)
     */
    private function generateSyrianPhone($faker): string
    {
        if ($faker->boolean) {
            // 10-digit format: 09 + 8 digits
            return '09' . $faker->numerify('########');
        } else {
            // 14-digit format: 009639 + 7 digits
            return '009639' . $faker->numerify('#######');
        }
    }
}
