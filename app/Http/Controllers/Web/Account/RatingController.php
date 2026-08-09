<?php

namespace App\Http\Controllers\Web\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\RateItemRequest;
use App\Models\Meal;
use App\Models\Offer;
use App\Services\People\RatingService;
use Illuminate\Http\RedirectResponse;

class RatingController extends Controller
{
    public function __construct(private readonly RatingService $ratings)
    {
    }

    public function meal(RateItemRequest $request, Meal $meal): RedirectResponse
    {
        $this->ratings->rateMeal($request->user()->customer, $meal->id, (int) $request->validated()['stars']);

        return $this->flash(back(), __('flash.ratings.saved'));
    }

    public function offer(RateItemRequest $request, Offer $offer): RedirectResponse
    {
        $this->ratings->rateOffer($request->user()->customer, $offer->id, (int) $request->validated()['stars']);

        return $this->flash(back(), __('flash.ratings.saved'));
    }
}
