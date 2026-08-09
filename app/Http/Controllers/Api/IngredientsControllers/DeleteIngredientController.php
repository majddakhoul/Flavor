<?php

namespace App\Http\Controllers\Api\IngredientsControllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Models\Ingredient;

class DeleteIngredientController extends Controller
{
    public function delete_ingredient($id)
    {
        try {
            $ingredient = Ingredient::find($id);

            if (!$ingredient) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ingredient not found.',
                    'data' => null,
                    'status' => 404
                ], 404);
            }

            $ingredient->meals()->detach();
            $ingredient->delete();

            return response()->json([
                'success' => true,
                'message' => 'Ingredient deleted successfully.',
                'data' => null,
                'status' => 200
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting ingredient: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete ingredient.',
                'data' => null,
                'status' => 500
            ], 500);
        }
    }
}