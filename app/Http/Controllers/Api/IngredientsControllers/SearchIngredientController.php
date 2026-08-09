<?php

namespace App\Http\Controllers\Api\IngredientsControllers;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class SearchIngredientController extends Controller
{
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
                'status' => 422
            ], 422);
        }

        try {
            $ingredients = Ingredient::where('name', 'LIKE', '%'.$request->search.'%')
                ->get();

            if ($ingredients->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No ingredients found',
                    'data' => null,
                    'status' => 200
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => 'Ingredients retrieved successfully',
                'data' => $ingredients,
                'status' => 200
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error searching ingredients: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to search ingredients',
                'data' => null,
                'status' => 500
            ], 500);
        }
    }
}