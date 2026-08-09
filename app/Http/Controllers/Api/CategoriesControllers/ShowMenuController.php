<?php

namespace App\Http\Controllers\Api\CategoriesControllers;

use App\Http\Controllers\Api\MealsRatingsControllers\ShowRatingForOneMealController;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class ShowMenuController extends Controller
{
    public function show_menu()
    {
        // تحميل العلاقات المطلوبة: الوجبات مع التقييمات والصورة والمكونات (لحساب التكاليف)
        $categories = Category::with([
            'meals.ratings',
            'meals.ingredients' // مطلوبة لحساب التكاليف ولكن سنخفيها لاحقاً
        ])->get();

        $categories->each(function ($category) {
            $category->meals->each(function ($meal) {
                $rating = app(ShowRatingForOneMealController::class)->show_rating_for_one_meal($meal->id);
                $picture = $meal->picture()->first() ?? $meal->picture;

                if($picture)
                    $meal->picture = $picture;
                else
                    $meal->picture =null;
                // حساب الحقول الإضافية
                $meal->price = $meal->price();
                $meal->rating= $rating->original['data'];
                $meal->preparation_cost = $meal->prep_cost();
                $meal->profit_margin = $meal->profit_margine(); // استخدام الدالة كما هي في الموديل
                $meal->is_active = $meal->is_active(); // إضافة حالة التوفر

                // إخفاء العلاقات غير المرغوب فيها
                $meal->makeHidden([
                    'ingredients',
                    'ingredient_meal',
                    'pivot',
                    'ratings',
                    'picture_id',
                    'category_id']);
            });
        });

        return response()->json([
            'success' => true,
            'message' => 'categories_retrieved_successfully.',
            'data' => $categories,
            'status' => 200
        ]);
    }
}
