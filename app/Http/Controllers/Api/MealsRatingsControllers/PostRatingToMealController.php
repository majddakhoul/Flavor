<?php

namespace App\Http\Controllers\Api\MealsRatingsControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\MealsRatingsRequests\PostRatingToMealRequest;
use App\Models\Customer;
use App\Models\Meal;
use App\Models\MealRating;

class PostRatingToMealController extends Controller
{

    public function post_rating_to_meal(PostRatingToMealRequest $postRatingToMealRequest)
    {

        $postRatingToMealRequest->validated();

       $User = auth()->user();

        $Customer = $User->customer;
        $Meal = Meal::find($postRatingToMealRequest->meal_id);

        $check = MealRating::where('customer_id', $Customer->id)
                   ->where('meal_id', $Meal->id)
                   ->first();
        if($check){
             $Response['data'] = null;
            $Response['success'] = false;
            $Response['message'] = 'You have already rated this meal!';
            $Response['status'] = 400;

            return response()->json($Response, 400);
        }
        if (!$Customer || !$Meal) {

            $Response['data'] = null;
            $Response['success'] = false;
            $Response['message'] = 'Wrong in validation customer id or meal id.';
            $Response['status'] = 400;

            return response()->json($Response, 400);
        }


        $NewRating = [
            'number_stars' => $postRatingToMealRequest->number_stars,
            'customer_id' => $Customer->id,
            'meal_id' => $Meal->id
        ];
        $Rating = new MealRating();
        $Rating->customer_id = $NewRating['customer_id'];
        $Rating->meal_id = $NewRating['meal_id'];
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
            $Response['message'] = 'An error occurred, You can try again later.';
            $Response['status'] = 400;

            return response()->json($Response, 400);
        }
    }
}
