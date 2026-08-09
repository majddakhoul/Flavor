<?php

namespace App\Http\Controllers\Api\OffersRatingsConrtollers;

use App\Http\Requests\Api\OffersRatingsRequests\PostRatingToOfferRequest;
use App\Models\Customer;
use App\Models\Offer;
use App\Models\OfferRating;

class PostRatingToOfferController
{

    public function post_rating_to_offer(PostRatingToOfferRequest $postRatingToOfferRequest)
    {

        $postRatingToOfferRequest->validated();

        $User = auth()->user();

        $Customer = $User->customer;

        $Offer = Offer::find($postRatingToOfferRequest->offer_id);

        $check = OfferRating::where('customer_id', $Customer->id)
                   ->where('offer_id', $Offer->id)
                   ->first();
        if($check){
             $Response['data'] = null;
            $Response['success'] = false;
            $Response['message'] = 'You have already rated this offer!';
            $Response['status'] = 400;

            return response()->json($Response, 400);
        }
        if (!$Customer || !$Offer) {

            $Response['data'] = null;
            $Response['success'] = false;
            $Response['message'] = 'Wrong in validation customer id or offer id.';
            $Response['status'] = 400;

            return response()->json($Response, 400);
        }


        $NewRating = [
            'number_stars' => $postRatingToOfferRequest->number_stars,
            'customer_id' => $Customer->id,
            'offer_id' => $Offer->id
        ];
        $Rating = new OfferRating();
        $Rating->customer_id = $NewRating['customer_id'];
        $Rating->offer_id = $NewRating['offer_id'];
        $Rating->number_stars = $NewRating['number_stars'];


        if ($Rating->save()) {
            $Response['data'] = null;
            $Response['success'] = true;
            $Response['message'] = 'Rating posted successfully.';
            $Response['status'] = 201;

            return response()->json($Response, 201);
        } else {
            $Response['data'] = null;
            $Response['success'] = false;
            $Response['message'] ='An error occurred, You can try again later.';
            $Response['status'] = 400;

            return response()->json($Response, 400);
        }
    }
}
