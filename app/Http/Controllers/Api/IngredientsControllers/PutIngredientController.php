<?php

namespace App\Http\Controllers\Api\IngredientsControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\IngredientsRequests\PutIngredientRequest;
use App\Models\Ingredient;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Exception;

class PutIngredientController extends Controller
{
    public function edit_ingredient(PutIngredientRequest $request, $id)
    {
        try {
            $ingredient = Ingredient::find($id);
            if (!$ingredient) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ingredient not found',
                    'data' => null,
                    'status' => 404
                ], 404);
            }
            $ingredient->update($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Ingredient updated successfully',
                'data' => $ingredient,
                'status' => 200
            ], 200);

        } catch (Exception $e) {
            Log::error('Error updating ingredient: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update ingredient',
                'data' => null,
                'status' => 500
            ], 500);
        }
    }
}