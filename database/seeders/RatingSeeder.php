<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Meal;
use App\Models\MealRating;
use App\Models\Offer;
use App\Models\OfferRating;
use Illuminate\Database\Seeder;

class RatingSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::query()->pluck('id')->all();
        $meals = Meal::query()->pluck('id')->all();
        $offers = Offer::query()->pluck('id')->all();

        if (empty($customers) || empty($meals)) {
            return;
        }

        foreach ($meals as $mealIndex => $mealId) {
            foreach (array_slice($customers, 0, 4) as $customerIndex => $customerId) {
                MealRating::query()->firstOrCreate(
                    ['meal_id' => $mealId, 'customer_id' => $customerId],
                    ['number_stars' => 3 + (($mealIndex + $customerIndex) % 3)]
                );
            }
        }

        foreach ($offers as $offerIndex => $offerId) {
            foreach (array_slice($customers, 0, 3) as $customerIndex => $customerId) {
                OfferRating::query()->firstOrCreate(
                    ['offer_id' => $offerId, 'customer_id' => $customerId],
                    ['number_stars' => 4 + (($offerIndex + $customerIndex) % 2)]
                );
            }
        }
    }
}
