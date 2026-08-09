<?php

namespace App\Http\Controllers\Api\IngredientsControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\IngredientsRequests\PostIngredientRequest;
use App\Models\Ingredient;
use Illuminate\Support\Facades\Log;
use Exception;

class PostIngredientController extends Controller
{
    public function add_ingredient(PostIngredientRequest $request)
    {
        try {
            $ingredient = Ingredient::create($request->validated());

            foreach ($ingredient->meals as $meal) {
                $meal->recalculateCost();
            }

            return response()->json([
                'success' => true,
                'message' => 'Ingredient created successfully',
                'data' => $ingredient,
                'status' => 201
            ], 201);

        } catch (Exception $e) {
            Log::error('Error creating ingredient: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create ingredient',
                'data' => null,
                'status' => 500
            ], 500);
        }
    }
}