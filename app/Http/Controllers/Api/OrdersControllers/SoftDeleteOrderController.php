<?php

namespace App\Http\Controllers\Api\OrdersControllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class SoftDeleteOrderController extends Controller
{
    public function delete_order($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
                'data' => null,
                'status' => 404
            ], 404);
        }

        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully.',
            'data' => null,
            'status' => 200
        ], 200);
    }
}
