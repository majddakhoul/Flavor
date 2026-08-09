<?php

namespace App\Http\Controllers\Api\OrdersControllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TodayOrdersController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $orders = Order::with(['meals', 'offers', 'customer.user', 'location'])
            ->whereDate('created_at', $today)
            ->where('status', '!=', 'Completed')
            ->orderBy('created_at', 'desc')
            ->get();

        $formattedOrders = [];
        foreach ($orders as $order) {
            $formattedOrders[] = [
                'id' => $order->id,
                'customer_name' => optional($order->customer)->user->name ?? 'Walk-in',
                'meals' => $this->formatItems($order->meals),
                'offers' => $this->formatItems($order->offers),
                'total' => $order->total,
                'status' => $order->status,
                'created_at' => $order->created_at->format('H:i A'),
                'delivery_time' => $order->getDeliveryTimeWithBuffer()
            ];
        }

        return response()->json([
            'success' => true,
            'message' => "Today's orders retrieved successfully",
            'status' => 200,
            'data' => $formattedOrders
        ]);
    }

    private function formatItems($items)
    {
        return $items->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name ?? $item->title,
                'quantity' => $item->pivot->quantity
            ];
        })->values();
    }
}
