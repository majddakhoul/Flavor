<?php

namespace App\Http\Controllers\Api\MealsControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\MealsRequests\PutMealRequest;
use App\Models\Meal;

class PutMealController extends Controller
{
    public function edit_meal(PutMealRequest $request, $id)
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

        $data = $request->validated();
        
        $meal->update([
            'name' => $data['name'] ?? $meal->name,
            'description' => $data['description'] ?? $meal->description,
            'percentage' => $data['percentage'] ?? $meal->percentage,
            'prep_time' => $data['prep_time'] ?? $meal->prep_time,
            'is_vegetarian' => $data['is_vegetarian'] ?? $meal->is_vegetarian,
            'availability' => $data['availability'] ?? $meal->availability,
        ]);

        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => 'Meal updated successfully',
            'data' => $meal->fresh()
        ], 200);
    }
}