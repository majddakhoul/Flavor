<?php

namespace App\Http\Controllers\Api\MealsControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\MealsRequests\PostMealRequest;
use App\Models\Meal;

class PostMealController extends Controller
{
    public function add_meal(PostMealRequest $request)
    {
        $data = $request->validated();


        $meal = Meal::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'prep_time' => $data['prep_time'],
            'percentage' => $data['percentage'],
            'is_vegetarian' => $data['is_vegetarian'],
            'availability' => $data['availability'] ?? true,
            'category_id' => $data['category_id']
        ]);

        return response()->json([
            'success' => true,
            'status' => 201,
            'message' => 'Meal created successfully',
            'data' => $meal
        ], 201);
    }
}
