<?php

namespace App\Http\Controllers\Api\OffersControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OffersRequests\AttachMealsToOfferRequest;
use App\Models\Offer;
use Illuminate\Support\Facades\Log;
use Exception;

class AttachMealsToOfferController extends Controller
{
    public function attach_meals(AttachMealsToOfferRequest $request, $offerId)
    {
        try {
            $offer = Offer::find($offerId);
            
            if (!$offer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Offer not found',
                    'data' => null,
                    'status' => 404
                ], 404);
            }

            $syncData = [];
            foreach ($request->meals as $meal) {
                $syncData[$meal['id']] = ['quantity' => $meal['quantity']];
            }

            $offer->meals()->syncWithoutDetaching($syncData);
            
            return response()->json([
                'success' => true,
                'message' => 'Meals attached successfully',
                'data' => $offer->load('meals'),
                'status' => 200
            ], 200);

        } catch (Exception $e) {
            Log::error('Failed to attach meals: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to attach meals',
                'data' => null,
                'status' => 500
            ], 500);
        }
    }

    public function detach_meal($offerId, $mealId) 
    {
        try {
            $offer = Offer::find($offerId);
            
            if (!$offer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Offer not found',
                    'data' => null,
                    'status' => 404
                ], 404);
            }

            if (!$offer->meals()->where('meal_id', $mealId)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Meal not attached to this offer',
                    'data' => null,
                    'status' => 404
                ], 404);
            }

            $offer->meals()->detach($mealId);

            return response()->json([
                'success' => true,
                'message' => 'Meal detached successfully',
                'data' => $offer->load('meals'),
                'status' => 200
            ], 200);

        } catch (Exception $e) {
            Log::error('Failed to detach meal: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to detach meal',
                'data' => null,
                'status' => 500
            ], 500);
        }
    }
}