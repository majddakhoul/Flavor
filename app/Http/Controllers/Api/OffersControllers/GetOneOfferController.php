<?php

namespace App\Http\Controllers\Api\OffersControllers;

use App\Http\Controllers\Api\OffersRatingsConrtollers\ShowRatingForOneOfferController;
use App\Http\Controllers\Controller;
use App\Models\Offer;

class GetOneOfferController extends Controller
{
    public function get_one($id)
    {
        // جلب العرض مع الوجبات المرتبطة (بدون مكونات) والعلاقات الأخرى
        $offer = Offer::with(['meals' => function ($query) {
            $query->without('ingredients')
                  ->with('category', 'picture'); // تحميل العلاقات الضرورية
        }])->find($id);

        if (!$offer) {
            return response()->json([
                'success' => false,
                'message' => 'Offer not found',
                'data' => null,
                'status' => 404
            ], 404);
        }

        $price = $offer->price();
        $final_price = $offer->final_price();

        $rating = app(ShowRatingForOneOfferController::class)->show_rating_for_one_offer($id);

        // تحضير الوجبات مع إضافة الكمية لكل وجبة
        $mealsWithQuantity = $offer->meals->map(function ($meal) {
            // إنشاء مسار عام للصورة باستخدام asset() مباشرة
            $picturePath = $meal->picture && $meal->picture->path
                ? asset($meal->picture->path) // استخدام asset() مباشرة
                : null;

            return [
                'id' => $meal->id,
                'name' => $meal->name,
                'prep_time' => $meal->prep_time,
                'is_vegetarian' => $meal->is_vegetarian,
                'description' => $meal->description,
                'availability' => $meal->availability,
                'prep_cost' => $meal->prep_cost(),
                'price' => $meal->price(),
                'profit_margine' => $meal->profit_margine(),
                'is_active' => $meal->is_active(),
                'picture' => $picturePath, // استخدام المسار العام هنا
                'quantity' => $meal->pivot->quantity // الكمية من جدول الربط
            ];
        });

        // بناء بيانات العرض
        $payload = [
            'id' => $offer->id,
            'title' => $offer->title,
            'description' => $offer->description,
            'discount_amount' => $offer->discount_amount,
            'is_active' => $offer->is_active(),
            'start_date' => $offer->start_date,
            'end_date' => $offer->end_date,
            'meals' => $mealsWithQuantity,
            'rating' => $rating->original['data'],
            'price' => $price,
            'total_price' => $final_price,
            'discount_margine' => $offer->discount_margine()
        ];

        return response()->json([
            'success' => true,
            'message' => null,
            'data' => $payload,
            'status' => 200
        ], 200);
    }
}
