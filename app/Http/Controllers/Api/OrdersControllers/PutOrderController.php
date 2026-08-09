<?php

namespace App\Http\Controllers\Api\OrdersControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OrdersRequests\putOrderRequest;
use App\Models\Order;
use App\Models\Meal;
use App\Models\Offer;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PutOrderController extends Controller
{
    public function edit_order(putOrderRequest $request, $orderId)
    {
        try {
            $validatedData = $request->validated();
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'status' => 401,
                    'message' => 'Unauthenticated.',
                    'data' => null,
                ], 401);
            }

            $order = Order::find($orderId);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'message' => 'Order not found.',
                    'data' => null,
                ], 404);
            }

            if ($user->user_type === 'Customer' && $user->customer?->isBanned()) {
                return response()->json([
                    'success' => false,
                    'status' => 403,
                    'message' => 'account_banned',
                    'data' => null,
                ], 403);
            }

            $order->update([
                'notes' => $validatedData['notes'],
                'order_type' => $validatedData['order_type'],
                'status' => $validatedData['status'] ?? $order->status,
            ]);

            // Meals
            if (isset($validatedData['meals'])) {
                $mealSyncData = [];
                foreach ($validatedData['meals'] as $mealInput) {
                    $meal = Meal::with('ingredients')->findOrFail($mealInput['id']);

                    foreach ($meal->ingredients as $ingredient) {
                        $ingredient->stock_quantity -= $ingredient->pivot->quantity * $mealInput['quantity'];
                        $ingredient->save();
                    }

                    $mealSyncData[$meal->id] = ['quantity' => $mealInput['quantity']];
                }
                $order->meals()->sync($mealSyncData);
            }

            // Offers
            if (isset($validatedData['offers'])) {
                $offerSyncData = [];
                foreach ($validatedData['offers'] as $offerInput) {
                    $offer = Offer::with('meals.ingredients')->findOrFail($offerInput['id']);

                    foreach ($offer->meals as $meal) {
                        foreach ($meal->ingredients as $ingredient) {
                            $ingredient->stock_quantity -= $ingredient->pivot->quantity * $offerInput['quantity'];
                            $ingredient->save();
                        }
                    }

                    $offerSyncData[$offer->id] = ['quantity' => $offerInput['quantity']];
                }
                $order->offers()->sync($offerSyncData);
            }

            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => 'The order has been updated successfully.',
                'data' => [
                    'order' => $order->load('meals', 'offers'),
                    'price' => $order->price(),
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Order update failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => 'Order update failed',
                'data' => null,
            ], 500);
        }
    }
}
