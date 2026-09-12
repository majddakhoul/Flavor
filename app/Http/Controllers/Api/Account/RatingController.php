<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Account\RateItemRequest;
use App\Http\Resources\MealRatingResource;
use App\Http\Resources\OfferRatingResource;
use App\Models\Meal;
use App\Models\Offer;
use App\Services\People\RatingService;
use Illuminate\Http\JsonResponse;

class RatingController extends Controller
{
    public function __construct(private readonly RatingService $ratings)
    {
    }

    public function meal(RateItemRequest $request, Meal $meal): JsonResponse
    {
        $rating = $this->ratings->rateMeal($request->user()->customer, $meal->id, (int) $request->validated()['stars']);

        return $this->ok(new MealRatingResource($rating), __('flash.ratings.saved'));
    }

    public function offer(RateItemRequest $request, Offer $offer): JsonResponse
    {
        $rating = $this->ratings->rateOffer($request->user()->customer, $offer->id, (int) $request->validated()['stars']);

        return $this->ok(new OfferRatingResource($rating), __('flash.ratings.saved'));
    }
}
