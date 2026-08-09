<?php

namespace App\Http\Controllers\Api\OffersControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OffersRequests\PutOfferRequest;
use App\Models\Meal;
use App\Models\Offer;
use Illuminate\Support\Facades\Log;

class PutOfferController extends Controller
{
    public function edit_offer(PutOfferRequest $request, $id)
    {
        try {
            $offer = Offer::find($id);
            
            if (!$offer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Offer not found',
                    'data' => null,
                    'status' => 404
                ], 404);
            }

            $data = $request->validated();

            // if (isset($data['meal_ids']) && is_array($data['meal_ids'] ) || $request->price) {
            //     $price = 0;

            //     foreach ($data['meal_ids'] as $mealId => $pivot) {
            //         $meal = Meal::findOrFail($mealId);
            //         $quantity = $pivot['quantity'] ?? 1;
            //         $price += $meal->price * $quantity;
            //     }

            //     if (isset($data['discount_amount']) && isset($data['type'])) {
            //         if ($data['type'] === 'Percentage') {
            //             $request->total_price -= $price * ($data['discount_amount'] / 100);
            //         } else {
            //             $request->total_price -= $data['discount_amount'];
            //         }
            //     }
            //     $price = max(0, $price);


            //     $syncData = [];
            //     foreach ($data['meal_ids'] as $mealId => $pivot) {
            //         $syncData[$mealId] = ['quantity' => $pivot['quantity'] ?? 1];
            //     }
            //     $offer->meals()->sync($syncData);
            // }

            $offer->update([
                'title'           => $data['title'] ?? $offer->title,
                'description'    => $data['description'] ?? $offer->description,
                'discount_amount' => $data['discount_amount'] ?? $offer->discount_amount,
                'type'           => $data['type'] ?? $offer->type,
                'is_active'     => $data['is_active'] ?? $offer->is_active,
                'start_date'    => $data['start_date'] ?? $offer->start_date,
                'end_date'      => $data['end_date'] ?? $offer->end_date,
            ]);

            return response()->json([
                'success' => true,
                'data' => $offer->load('meals'),
                'message' => 'Offer updated successfully',
                'status' => 200
            ], 200);

        } catch (\Exception $e) {
            Log::error('Offer update failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update offer',
                'data' => null,
                'status' => 500
            ], 500);
        }
    }
}