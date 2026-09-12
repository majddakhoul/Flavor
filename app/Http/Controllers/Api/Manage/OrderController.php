<?php

namespace App\Http\Controllers\Api\Manage;

use App\Enums\OrderStatus;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Manage\OrderStatusRequest;
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
        $this->authorize('viewAny', Order::class);

        return $this->paginated(
            $this->orders->paginate(QueryOptions::fromRequest($request, ['customer.user', 'meals', 'offers'])),
            OrderResource::class
        );
    }

    public function statistics(): JsonResponse
    {
        $this->authorize('viewAny', Order::class);

        return $this->ok($this->orders->statistics());
    }

    public function trashed(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Order::class);

        return $this->paginated($this->orders->trashed(QueryOptions::fromRequest($request)), OrderResource::class);
    }

    public function show(Order $order): JsonResponse
    {
        $this->authorize('view', $order);

        return $this->ok(new OrderResource($order->load([
            'meals.ingredients', 'offers.meals.ingredients', 'customer.user', 'employee.user', 'location', 'reservation.tables',
        ])));
    }

    public function status(OrderStatusRequest $request, Order $order): JsonResponse
    {
        $this->authorize('transition', $order);

        $order = $this->orders->transition($order, OrderStatus::from($request->validated()['status']));

        return $this->ok(new OrderResource($order), __('flash.orders.status_updated'));
    }

    public function restore(int $order): JsonResponse
    {
        $model = Order::onlyTrashed()->findOrFail($order);
        $this->authorize('restore', $model);

        $model = $this->orders->restore($model);

        return $this->ok(new OrderResource($model), __('flash.orders.restored'));
    }

    public function destroy(int $order): JsonResponse
    {
        $model = Order::withTrashed()->findOrFail($order);
        $this->authorize('delete', $model);

        $this->orders->purge($model);

        return $this->noContent(__('flash.orders.deleted'));
    }
}
