<?php

namespace App\Http\Controllers\Api\OrdersControllers;

use App\Http\Controllers\Controller;
use App\Models\Order;

class GetOneOrderController extends Controller
{
    public function get_one_order($id)
    {
        $order = Order::with(['meals', 'offers', 'location'])->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
                'data' => [],
                'status' => 404
            ], 404);
        }

        // Optional: restrict access to customer who owns the order
        $user = auth()->user();
        if ($user && $user->customer && $order->customer_id !== $user->customer->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to view this order',
                'data' => [],
                'status' => 403
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order fetched successfully',
            'data' => [
                'order' => $order,
                'total_price' => $order->price(),
                'delivery_time' => $order->getDeliveryTimeWithBuffer()
            ],
            'status' => 200
        ], 200);
    }
}
