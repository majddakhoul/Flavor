<?php

namespace App\Http\Controllers\Api\OrdersControllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class DeleteOrderController extends Controller
{
    public function delete_order($orderId)
    {
        try {
            $order = Order::findOrFail($orderId);

            $order->meals()->detach();
            $order->offers()->detach();

            $order->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Order deleted successfully.',
                'status' => 200,
                'data' => null,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
                'status' => 404,
                'data' => null,
            ], 404);
        } catch (\Exception $e) {
            Log::error('Order deletion failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Order deletion failed.',
                'status' => 500,
                'data' => null,
            ], 500);
        }
    }
}
