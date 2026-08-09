<?php

namespace App\Http\Controllers\Api\MealsControllers;

use App\Http\Controllers\Api\MealsRatingsControllers\ShowRatingForOneMealController;
use App\Http\Controllers\Controller;
use App\Models\Meal;
use Illuminate\Support\Facades\App;

class GetAllMealsController extends Controller
{
    public function get_all_meals()
    {
        // تحميل العلاقات المطلوبة مع حساب المتوسطات مسبقاً
        $meals = Meal::with(['picture'])
            ->get();

        if ($meals->isEmpty()) {
            return response()->json([
                'data' => null,
                'success' => true,
                'message' => 'There is nothing to display',
                'status' => 200
            ], 200);
        }

        $mealsWithDetails = $meals->map(function ($meal) {
            // حساب الحقول المطلوبة
            $prepCost = $meal->prep_cost();
            $price = $meal->price();
            $profitMargin = $meal->profit_margine();
            $isActive = $meal->is_active();
            $rating = app(ShowRatingForOneMealController::class)->show_rating_for_one_meal($meal->id);

            // الحصول على مسار الصورة
            $picturePath = $meal->picture ? asset($meal->picture->path) : null;

            // بناء المصفوفة مع الحقول المطلوبة
            return [
                'id' => $meal->id,
                'name' => $meal->name,
                'prep_time' => $meal->prep_time,
                'is_vegetarian' => $meal->is_vegetarian,
                'description' => $meal->description,
                'availability' => $meal->availability,
                'prep_cost' => $prepCost,
                'price' => $price,
                'profit_margine' => $profitMargin,
                'is_active' => $isActive,
                'picture' => $picturePath,
                'rating' => $rating->original['data']
            ];
        });

        return response()->json([
            'data' => $mealsWithDetails,
            'success' => true,
            'message' => 'Meals fetched successfully',
            'status' => 200
        ], 200);
    }
}
