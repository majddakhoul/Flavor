<?php

namespace App\Services\People;

use App\Models\Customer;
use App\Models\MealRating;
use App\Models\OfferRating;
use App\Services\Support\CacheService;

class RatingService
{
    public function __construct(private readonly CacheService $cache)
    {
    }

    public function rateMeal(Customer $customer, int $mealId, int $stars): MealRating
    {
        $rating = MealRating::updateOrCreate(
            ['customer_id' => $customer->id, 'meal_id' => $mealId],
            ['number_stars' => $this->clamp($stars)]
        );

        $this->cache->flush(['menu', 'catalog']);

        return $rating;
    }

    public function rateOffer(Customer $customer, int $offerId, int $stars): OfferRating
    {
        $rating = OfferRating::updateOrCreate(
            ['customer_id' => $customer->id, 'offer_id' => $offerId],
            ['number_stars' => $this->clamp($stars)]
        );

        $this->cache->flush(['menu', 'catalog']);

        return $rating;
    }

    protected function clamp(int $stars): int
    {
        return max(1, min(5, $stars));
    }
}
