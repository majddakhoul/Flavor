<?php

namespace App\Http\Controllers\Api\OrdersControllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class CompleteOrderController extends Controller
{
    public function update($orderId)
    {
        try {
            $order = Order::find($orderId);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'message' => 'Order not found',
                    'data' => null,
                ], 404);
            }

            if ($order->status === 'Completed') {
                return response()->json([
                    'success' => false,
                    'status' => 400,
                    'message' => 'Order already completed',
                    'data' => null,
                ], 400);
            }

            $order->update(['status' => 'Completed']);

            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => 'Order marked as completed',
                'data' => [
                    'order_id' => $order->id,
                    'new_status' => $order->status,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Order completion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => 'Order completion failed',
                'data' => null,
            ], 500);
        }
    }
}
