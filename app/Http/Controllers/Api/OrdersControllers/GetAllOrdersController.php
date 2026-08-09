<?php

namespace App\Http\Controllers\Api\OrdersControllers;

use App\Http\Controllers\Controller;
use App\Models\Order;

class GetAllOrdersController extends Controller
{
    public function get_all_orders()
    {
        $user = auth()->user();

        if (!$user || !$user->customer) {
            return response()->json([
                'success' => false,
                'message' => 'Only customers can view orders.',
                'data' => [],
                'status' => 403
            ], 403);
        }

        $orders = Order::with(['meals', 'offers', 'location'])
            ->where('customer_id', $user->customer->id)
            ->get();

        $ordersWithDetails = [];
        foreach ($orders as $order) {
            $orderData = $order->toArray();
            $orderData['price'] = $order->price();
            $orderData['delivery_time'] = $order->getDeliveryTimeWithBuffer();
            $ordersWithDetails[] = $orderData;
        }

        return response()->json([
            'success' => true,
            'message' => 'Orders fetched successfully',
            'data' => $ordersWithDetails,
            'status' => 200
        ], 200);
    }
}
