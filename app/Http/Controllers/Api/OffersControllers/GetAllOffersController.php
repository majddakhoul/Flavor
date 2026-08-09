<?php

namespace App\Http\Controllers\Api\OffersControllers;

use App\Http\Controllers\Api\OffersRatingsConrtollers\ShowRatingForOneOfferController;
use App\Http\Controllers\Controller;
use App\Models\Offer;
use Illuminate\Support\Facades\Log;

class GetAllOffersController extends Controller
{
    public function get_all()
    {
        try {
            // جلب جميع العروض مع العلاقات المطلوبة للتقييم
            $offers = Offer::with('ratings')->get();

            $offersWithDetails = [];

            foreach ($offers as $offer) {
                // حساب متوسط التقييم
                $averageRating = app(ShowRatingForOneOfferController::class)->show_rating_for_one_offer($offer->id) ?? 0;

                // بناء بيانات العرض الأساسية
                $offersWithDetails[] = [
                    'id' => $offer->id,
                    'title' => $offer->title,
                    'description' => $offer->description,
                    'discount_amount' => $offer->discount_amount,
                    'is_active' => $offer->is_active(),
                    'start_date' => $offer->start_date,
                    'end_date' => $offer->end_date,
                    'average_rating' => round($averageRating->original['data'], 1),
                    'price' => $offer->price(),
                    'total_price' => $offer->final_price(),
                    'discount_margine' => $offer->discount_margine()
                ];
            }

            return response()->json([
                'success' => true,
                'message' => null,
                'data' => $offersWithDetails,
                'status' => 200
            ], 200);

        } catch (\Exception $e) {
            Log::error('Offer fetch error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to get offers',
                'data' => null,
                'status' => 500
            ], 500);
        }
    }
}
