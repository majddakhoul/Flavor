<?php

namespace App\Http\Controllers\Api\IngredientsControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Ingredient;

class GetAllIngredientsController extends Controller
{
    public function get_all_ingredients(Request $request)
    {
        try {
            // Direct model query with conditional eager loading
            $ingredients = $request->has('with_relations') 
                ? Ingredient::with('meals')->get()
                : Ingredient::all();

            if ($ingredients->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No ingredients found.',
                    'data' => null,
                    'status' => 200
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Ingredients retrieved successfully.',
                'data' => $ingredients,
                'status' => 200
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving ingredients: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve ingredients.',
                'data' => null,
                'status' => 500
            ], 500);
        }
    }
}