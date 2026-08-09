<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['Syria', 'Old City', '00:30:00', 'Damascus', 'Damascus', 'Straight Street'],
            ['Syria', 'Mezzeh', '00:40:00', 'Damascus', 'Damascus', 'Mezzeh Highway'],
            ['Syria', 'Malki', '00:35:00', 'Damascus', 'Damascus', 'Malki Square'],
            ['Syria', 'Azizieh', '00:45:00', 'Aleppo', 'Aleppo', 'Baron Street'],
            ['Syria', 'Corniche', '00:50:00', 'Latakia', 'Latakia', 'Corniche Road'],
            ['Syria', 'Wadi Al Dahab', '00:55:00', 'Homs', 'Homs', 'Hama Road'],
        ];

        foreach ($locations as [$country, $region, $deliveryTime, $state, $city, $street]) {
            Location::query()->firstOrCreate(
                ['city' => $city, 'region' => $region],
                [
                    'country' => $country,
                    'delivery_time' => $deliveryTime,
                    'state' => $state,
                    'street' => $street,
                ]
            );
        }
    }
}
