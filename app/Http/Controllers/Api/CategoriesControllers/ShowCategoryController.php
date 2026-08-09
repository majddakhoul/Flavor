<?php

namespace App\Http\Controllers\Api\CategoriesControllers;

use App\Http\Controllers\Api\MealsRatingsControllers\ShowRatingForOneMealController;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Support\Facades\Log;

class ShowCategoryController extends Controller
{
    public function show_category($CategoryId)
    {
        try {
            $category = Category::with([
                'meals.ratings',
                'meals.ingredients',
                'meals.picture'
            ])->find($CategoryId);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Undefined category.',
                    'data' => null,
                    'status' => 404
                ], 404);
            }

            $category->meals->each(function ($meal) {
                $ratingController = app(ShowRatingForOneMealController::class);
                $rating = $ratingController->show_rating_for_one_meal($meal->id);

                $meal->price = $meal->price();
                $meal->rating = $rating->original['data'];
                $meal->preparation_cost = $meal->prep_cost();
                $meal->profit_margin = $meal->profit_margine();
                $meal->is_active = $meal->is_active();

                $meal->picture = $meal->picture
                    ? $meal->picture->path
                    : null;

                $meal->makeHidden([
                    'ingredients',
                    'ingredient_meal',
                    'pivot',
                    'ratings',
                    'picture_id',
                    'category_id'
                ]);
            });

            return response()->json([
                'data' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description,
                    'meals' => $category->meals,
                    'created_at' => $category->created_at,
                    'updated_at' => $category->updated_at
                ],
                'success' => true,
                'message' => 'Category retrieved successfully.',
                'status' => 200
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving category: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'categories_retrieval_failed.',
                'data' => null,
                'status' => 500
            ], 500);
        }
    }
}
