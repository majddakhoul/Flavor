<?php

namespace App\Http\Controllers\Api\OffersRatingsConrtollers;

use App\Http\Requests\Api\OffersRatingsRequests\UpdateRatingToOfferRequest;
use App\Models\OfferRating;

class UpdateRatingToOfferController
{

    public function update_rating_to_offer(UpdateRatingToOfferRequest $updateRatingToOfferRequest)
    {

         $updateRatingToOfferRequest->validated();

        $User = auth()->user();
        $Customer = $User->customer;

        if (!$Customer) {
            $Response['data'] = null;
            $Response['success'] = false;
            $Response['message'] = 'Undefined customer.';
            $Response['status'] = 404;

            return response()->json($Response, 404);
        }
        $Rating = $Customer->offer_rating()->where('offer_id',$updateRatingToOfferRequest->offer_id)->first();

        if (!$Rating) {
            $Response['data'] = null;
            $Response['success'] = false;
            $Response['message'] = 'Undefined rating.';
            $Response['status'] = 404;

            return response()->json($Response, 404);
        }
        $Rating->number_stars = $updateRatingToOfferRequest->number_stars ?? $updateRatingToOfferRequest->number_stars;
        $Updated = $Rating->update();

        if ($Updated) {
            $Response['data'] = $Rating->fresh();
            $Response['success'] = true;
            $Response['message'] = 'Rating information updated successfully.';
            $Response['status'] = 200;
            return response()->json($Response, 200);
        } else {
            $Response['data'] = null;
            $Response['success'] = false;
            $Response['message'] = 'Failed to update your Rating information.';
            $Response['status'] = 400;

            return response()->json($Response, 400);
        }
    }
}
