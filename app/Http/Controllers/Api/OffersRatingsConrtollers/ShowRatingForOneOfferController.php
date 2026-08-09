<?php

namespace App\Http\Controllers\Api\OffersRatingsConrtollers;

use App\Models\Offer;

class ShowRatingForOneOfferController
{

    public function show_rating_for_one_offer($OfferId)
    {
        $Offer = Offer::find($OfferId);

        if (!$Offer) {
            $Response['data'] = null;
            $Response['success'] = false;
            $Response['message'] = 'No offer found with the specified attributes.';
            $Response['status'] = 404;

            return response()->json($Response, 404);
        }

        $Ratings = $Offer->ratings()->get();

        if (count($Ratings) > 0) {
            $sumStars = $Ratings->sum('number_stars');
            $avg = round($sumStars / count($Ratings), 1);

            $Response['data'] = $avg;
            $Response['success'] = true;
            $Response['message'] = 'Rating fetched successfully.';
            $Response['status'] = 200;
        } else {
            $Response['data'] = 0;
            $Response['success'] = true;
            $Response['message'] = 'No ratings yet.';
            $Response['status'] = 200;
        }

        return response()->json($Response, 200);
    }
}
