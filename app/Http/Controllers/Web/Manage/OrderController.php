<?php

namespace App\Http\Controllers\Web\Manage;

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manage\OrderStatusRequest;
use App\Models\Order;
use App\Services\Sales\OrderService;
use App\Support\QueryOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orders)
    {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Order::class);

        return view('manage.orders.index', [
            'orders' => $this->orders->paginate(QueryOptions::fromRequest($request)),
            'statistics' => $this->orders->statistics(),
            'statuses' => OrderStatus::options(),
            'types' => OrderType::options(),
        ]);
    }

    public function trashed(Request $request): View
    {
        $this->authorize('viewAny', Order::class);

        return view('manage.orders.trashed', [
            'orders' => $this->orders->trashed(QueryOptions::fromRequest($request)),
        ]);
    }

    public function show(Order $order): View
    {
        $this->authorize('view', $order);

        return view('manage.orders.show', [
            'order' => $order->load([
                'meals.ingredients', 'offers.meals.ingredients', 'customer.user', 'employee.user', 'location', 'reservation.tables',
            ]),
            'statuses' => OrderStatus::options(),
        ]);
    }

    public function status(OrderStatusRequest $request, Order $order): RedirectResponse
    {
        $this->authorize('transition', $order);

        $this->orders->transition($order, OrderStatus::from($request->validated()['status']));

        return $this->done('manage.orders.show', __('flash.orders.status_updated'), $order);
    }

    public function restore(int $order): RedirectResponse
    {
        $model = Order::onlyTrashed()->findOrFail($order);
        $this->authorize('restore', $model);

        $this->orders->restore($model);

        return $this->done('manage.orders.index', __('flash.orders.restored'));
    }

    public function destroy(int $order): RedirectResponse
    {
        $model = Order::withTrashed()->findOrFail($order);
        $this->authorize('delete', $model);

        $this->orders->purge($model);

        return $this->done('manage.orders.index', __('flash.orders.deleted'));
    }
}
