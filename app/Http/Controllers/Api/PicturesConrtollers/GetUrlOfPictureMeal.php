<?php

namespace App\Http\Controllers\Api\PicturesConrtollers;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use App\Models\Picture;
use Illuminate\Http\Request;

class GetUrlOfPictureMeal extends Controller
{
    public function show($MealId)
    {
        $Meal = Meal::find($MealId);
        if (!$Meal) {

            $Response['data'] = null;
            $Response['success'] = false;
            $Response['message'] = 'An error occurred, Undefined Meal.';
            $Response['status'] = 400;

            return response()->json($Response, 400);
        }
        $photo = $Meal->picture;
        if (!$photo) {

            $Response['data'] = null;
            $Response['success'] = false;
            $Response['message'] = 'An error occurred, Undefined photo.';
            $Response['status'] = 400;

            return response()->json($Response, 400);
        }

        $photo['url'] = asset('/' . $photo->path);

        return response()->json([
            'success' => true,
            'data' => [
                'meal' => $Meal,
            ],
            'status' => 200,
            'message' => 'Picture fetched successfully.'
        ],200);
    }
}
