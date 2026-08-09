<?php

namespace App\Http\Controllers\Api\OrdersControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OrdersRequests\postOrderRequest;
use App\Models\Meal;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostOrderController extends Controller
{
    public function add_order(postOrderRequest $request)
    {
        DB::beginTransaction();

        try {
            $user = auth()->user();
            $customerId = $user->customer?->id;
            $employeeId = $user->employee?->id;

            $locationId = $request->location_id;

            if (!$locationId && $customerId && in_array($request->order_type, ['Delivery', 'Takeaway'])) {
                $customer = Customer::with('user.location')->find($customerId);

                if (!$customer || !$customer->user || !$customer->user->location) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No location found for this customer. Please provide a location.'
                    ], 422);
                }

                $locationId = $customer->user->location->id;
            }

            $order = Order::create([
                'notes' => $request->notes,
                'order_type' => $request->order_type,
                'status' => $request->status ?? 'Pending',
                'dated_at' => now(),
                'customer_id' => $customerId,
                'employee_id' => $employeeId,
                'reservation_id' => $request->reservation_id,
                'location_id' => $locationId,
            ]);

            if ($request->has('meals')) {
                $mealSyncData = [];

                foreach ($request->meals as $mealInput) {
                    $meal = Meal::with('ingredients')->findOrFail($mealInput['id']);

                    if (!$meal->is_active()) {
                        return response()->json([
                            'success' => false,
                            'message' => "Meal {$meal->name} with an id of {$meal->id} is not available."
                        ], 422);
                    }

                    foreach ($meal->ingredients as $ingredient) {
                        $requiredQty = $ingredient->pivot->quantity * $mealInput['quantity'];
                        if ($ingredient->stock_quantity < $requiredQty) {
                            throw new \Exception("Not enough stock for ingredient {$ingredient->name} in meal {$meal->name}.");
                        }
                        $ingredient->stock_quantity -= $requiredQty;
                        $ingredient->save();
                    }

                    $mealSyncData[$meal->id] = ['quantity' => $mealInput['quantity']];
                }

                $order->meals()->syncWithoutDetaching($mealSyncData);
            }

            if ($request->has('offers')) {
                $offerSyncData = [];

                foreach ($request->offers as $offerInput) {
                    $offer = Offer::with('meals.ingredients')->findOrFail($offerInput['id']);

                    if (!$offer->is_active()) {
                        return response()->json([
                            'success' => false,
                            'message' => "Offer {$offer->title} with an id of {$offer->id} is not available."
                        ], 422);
                    }

                    foreach ($offer->meals as $meal) {
                        foreach ($meal->ingredients as $ingredient) {
                            $requiredQty = $ingredient->pivot->quantity * $offerInput['quantity'];
                            if ($ingredient->stock_quantity < $requiredQty) {
                                throw new \Exception("Not enough stock for ingredient {$ingredient->name} in offer {$offer->title}.");
                            }
                            $ingredient->stock_quantity -= $requiredQty;
                            $ingredient->save();
                        }
                    }

                    $offerSyncData[$offer->id] = ['quantity' => $offerInput['quantity']];
                }

                $order->offers()->syncWithoutDetaching($offerSyncData);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully.',
                'data' => [
                    'order' => $order->load('meals', 'offers'),
                    'price' => $order->price(),
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Order creation failed: ' . $e->getMessage(),
                'data' => [],
                'status' => 500
            ], 500);
        }
    }
}
