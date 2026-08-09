<?php

namespace App\Http\Controllers\Api\MealsControllers;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use App\Http\Controllers\Api\MealsRatingsControllers\ShowRatingForOneMealController;
class GetOneMealController extends Controller
{
    public function get_one_meal($id)
    {
        // تحميل الوجبة مع الصورة ومتوسط التقييم
        $meal = Meal::with(['picture'])
            ->find($id);

        if (!$meal) {
            return response()->json([
                'data' => null,
                'success' => false,
                'message' => 'Meal not found',
                'status' => 404
            ], 404);
        }

        // حساب الحقول المطلوبة
        $prepCost = $meal->prep_cost();
        $price = $meal->price();
        $profitMargin = $meal->profit_margine();
        $isActive = $meal->is_active();

        // الحصول على مسار الصورة
        $picturePath = $meal->picture ? asset($meal->picture->path) : null;
        $rating = app(ShowRatingForOneMealController::class)->show_rating_for_one_meal($id);


        // بناء المصفوفة مع الحقول المطلوبة
        $mealDetails = [
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

        return response()->json([
            'data' => $mealDetails,
            'success' => true,
            'message' => 'Meal fetched successfully',
            'status' => 200
        ], 200);
    }
}
