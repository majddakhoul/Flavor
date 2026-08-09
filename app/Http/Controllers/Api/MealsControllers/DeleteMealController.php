<?php

namespace App\Http\Controllers\Api\MealsControllers;

use App\Http\Controllers\Controller;
use App\Models\Meal;

class DeleteMealController extends Controller
{
    public function delete_meal($id)
    {
        $meal = Meal::find($id);

        if (!$meal) {
            return response()->json([
                'success' => false,
                'status' => 404,
                'message' => 'Meal not found',
                'data' => null
            ], 404);
        }

        $deleted = $meal->ingredients()->detach();
        $meal->delete();

        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => 'Meal deleted successfully',
            'data' => null
        ], 200);
    }
}