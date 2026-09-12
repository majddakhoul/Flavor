<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\Api\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\Sales\OrderService;
use App\Support\QueryOptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orders)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->orders->paginateForCustomer(
            $request->user()->customer->id,
            QueryOptions::fromRequest($request, ['meals', 'offers', 'location'])
        );

        return $this->paginated($paginator, OrderResource::class);
    }

    public function show(Order $order): JsonResponse
    {
        $this->authorize('view', $order);

        return $this->ok(new OrderResource(
            $order->load(['meals.ingredients', 'offers.meals.ingredients', 'location', 'reservation.tables'])
        ));
    }

    public function cancel(Order $order): JsonResponse
    {
        $this->authorize('cancel', $order);

        $order = $this->orders->cancel($order);

        return $this->ok(new OrderResource($order), __('flash.orders.cancelled'));
    }

    public function revert(Order $order): JsonResponse
    {
        $this->authorize('revert', $order);

        $this->orders->revertToCart($order);

        return $this->noContent(__('flash.orders.reverted'));
    }

    public function restore(int $order): JsonResponse
    {
        $model = Order::onlyTrashed()->findOrFail($order);
        $this->authorize('restore', $model);

        $model = $this->orders->restore($model);

        return $this->ok(new OrderResource($model), __('flash.orders.restored'));
    }
}
