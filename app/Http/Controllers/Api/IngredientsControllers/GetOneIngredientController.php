<?php

namespace App\Http\Controllers\Api\IngredientsControllers;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GetOneIngredientController extends Controller
{
    public function get_one_ingredient($id)
    {
        try {
            // تحميل الوجبات مع علاقة الصنف
            $ingredient = Ingredient::with(['meals.category'])->find($id);

            if (!$ingredient) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ingredient not found',
                    'data' => null,
                    'status' => 404
                ], 404);
            }

            // إضافة اسم الصنف لكل وجبة
            if ($ingredient->meals) {
                $ingredient->meals->each(function ($meal) {
                    $meal->category_name = $meal->category ? $meal->category->name : null;

                    // إخفاء حقل category_id
                    $meal->makeHidden(['category_id', 'category']);
                });
            }

            return response()->json([
                'success' => true,
                'data' => $ingredient,
                'message' => null,
                'status' => 200
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ingredient not found',
                'data' => null,
                'status' => 404
            ], 404);
        }
    }
}
