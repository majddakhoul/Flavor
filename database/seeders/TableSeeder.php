<?php

namespace Database\Seeders;

use App\Enums\TableLocation;
use App\Models\Table;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    public function run(): void
    {
        $plan = [
            [TableLocation::Indoor, 8, [2, 4], 0, ['Window side table', 'طاولة بجانب النافذة', 'Table côté fenêtre']],
            [TableLocation::Outdoor, 6, [4, 6], 15000, ['Garden terrace table', 'طاولة في تراس الحديقة', 'Table sur la terrasse']],
            [TableLocation::VIP, 4, [6, 8], 60000, ['Private room with service bell', 'غرفة خاصة مع جرس خدمة', 'Salon privé avec sonnette']],
            [TableLocation::Roof, 6, [2, 4], 35000, ['Rooftop table with city view', 'طاولة على السطح بإطلالة على المدينة', 'Table en rooftop avec vue']],
        ];

        $number = 1;

        foreach ($plan as [$location, $count, $capacities, $price, $labels]) {
            for ($index = 0; $index < $count; $index++) {
                $table = Table::query()->firstOrCreate(
                    ['table_number' => 'T'.str_pad((string) $number, 2, '0', STR_PAD_LEFT)],
                    [
                        'capacity' => $capacities[$index % count($capacities)],
                        'location' => $location->value,
                        'is_active' => true,
                        'price_per_hour' => $price,
                        'description' => $labels[0],
                    ]
                );

                $table->syncTranslations([
                    'ar' => ['description' => $labels[1]],
                    'fr' => ['description' => $labels[2]],
                ]);

                $number++;
            }
        }
    }
}
