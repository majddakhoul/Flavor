<?php

namespace App\Http\Controllers\Api\MealsControllers;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SearchMealController extends Controller
{
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
                'status' => 422
            ], 422);
        }

        $searchTerm = $request->input('search');

        $meals = Meal::where(function($query) use ($searchTerm) {
                $query->where('name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('description', 'LIKE', "%{$searchTerm}%");
            })
            ->get();

        if ($meals->isEmpty()) {
            return response()->json([
                'data' => null,
                'success' => true,
                'message' => 'There is nothing to display.',
                'status' => 200
            ], 200);
        }

        return response()->json([
            'data' => $meals,
            'success' => true,
            'message' => 'All results fetched successfully.',
            'status' => 200
        ], 200);
    }
}
