<?php

namespace App\Http\Controllers\Api\MealsControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Meal;
use App\Models\Ingredient;

class AttachIngredientsToMealController extends Controller
{
    public function attach_ingredients(Request $request, $meal_id)
    {
        $validated = $request->validate([
            'ingredients' => 'required|array|min:1',
            'ingredients.*.id' => 'required|exists:ingredients,id',
            'ingredients.*.quantity' => 'required|numeric|min:0.01',
        ]);

        $meal = Meal::find($meal_id);
        
        if (!$meal) {
            return response()->json([
                'success' => false,
                'status' => 404,
                'message' => 'Meal not found',
                'data' => null
            ], 404);
        }

        $syncData = collect($validated['ingredients'])
            ->mapWithKeys(function ($item) {
                return [$item['id'] => ['quantity' => $item['quantity']]];
            })
            ->all();

        $meal->ingredients()->sync($syncData);

        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => 'Ingredients attached to meal successfully',
            'data' => $meal->fresh()->load('ingredients'),
        ], 200);
    }

    public function detach_ingredient($mealId, $ingredientId)
    {
        $meal = Meal::find($mealId);
        $ingredient = Ingredient::find($ingredientId);

        if (!$meal) {
            return response()->json([
                'success' => false,
                'status' => 404,
                'message' => 'Meal not found',
                'data' => null
            ], 404);
        }

        if (!$ingredient) {
            return response()->json([
                'success' => false,
                'status' => 404,
                'message' => 'Ingredient not found',
                'data' => null
            ], 404);
        }

        if (!$meal->ingredients()->where('ingredient_id', $ingredientId)->exists()) {
            return response()->json([
                'success' => false,
                'status' => 400,
                'message' => 'Ingredient not attached to this meal',
                'data' => null
            ], 400);
        }

        $meal->ingredients()->detach($ingredientId);

        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => 'Ingredient detached successfully',
            'data' => null
        ], 200);
    }
}