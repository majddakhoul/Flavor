<?php

namespace Database\Seeders;

use App\Enums\Allergy;
use App\Enums\Gender;
use App\Enums\UserType;
use App\Models\Customer;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $locations = Location::query()->pluck('id')->all();

        $customers = [
            ['Yara', 'Mansour', 'customer@flavor.test', Gender::Female, null, false],
            ['Hadi', 'Suleiman', 'hadi@flavor.test', Gender::Male, Allergy::Peanut, false],
            ['Maya', 'Fares', 'maya@flavor.test', Gender::Female, Allergy::Wheat, false],
            ['Tarek', 'Odeh', 'tarek@flavor.test', Gender::Male, null, false],
            ['Salma', 'Rahal', 'salma@flavor.test', Gender::Female, Allergy::Fish, false],
            ['Ziad', 'Halabi', 'ziad@flavor.test', Gender::Male, null, true],
            ['Dina', 'Kassem', 'dina@flavor.test', Gender::Female, Allergy::Egg, false],
            ['Bassel', 'Rifai', 'bassel@flavor.test', Gender::Male, null, false],
        ];

        foreach ($customers as $index => [$first, $last, $email, $gender, $allergy, $banned]) {
            $user = User::query()->firstOrCreate(
                ['email' => $email],
                [
                    'first_name' => $first,
                    'last_name' => $last,
                    'password' => Hash::make('password'),
                    'phone' => '0933'.str_pad((string) (200000 + $index), 6, '0', STR_PAD_LEFT),
                    'gender' => $gender->value,
                    'status' => true,
                    'user_type' => UserType::Customer->value,
                    'location_id' => $locations[$index % count($locations)],
                    'email_verified_at' => now(),
                ]
            );

            Customer::query()->firstOrCreate(
                ['user_id' => $user->id],
                [
                    'allergies' => $allergy?->value,
                    'favorite_categories' => null,
                    'ban' => $banned,
                    'ban_date' => $banned ? now()->subDays(3)->toDateString() : null,
                ]
            );
        }
    }
}
