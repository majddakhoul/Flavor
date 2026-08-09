<?php

namespace App\Http\Controllers\Api\OrdersControllers;

use App\Http\Controllers\Controller;
use App\Models\Order;

class GetAllOrdersWithTrashController extends Controller
{
    public function get_all_orders()
    {
        $orders = Order::withTrashed()
            ->with(['meals', 'offers', 'location'])
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
