<?php

namespace App\Http\Controllers\Web\Account;

use App\Http\Controllers\Controller;
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
        return view('account.orders.index', [
            'orders' => $this->orders->paginateForCustomer(
                $request->user()->customer->id,
                QueryOptions::fromRequest($request)
            ),
        ]);
    }

    public function show(Order $order): View
    {
        $this->authorize('view', $order);

        return view('account.orders.show', [
            'order' => $order->load(['meals.ingredients', 'offers.meals.ingredients', 'location', 'reservation.tables']),
        ]);
    }

    public function cancel(Order $order): RedirectResponse
    {
        $this->authorize('cancel', $order);

        $this->orders->cancel($order);

        return $this->done('account.orders.index', __('flash.orders.cancelled'));
    }

    public function revert(Order $order): RedirectResponse
    {
        $this->authorize('revert', $order);

        $this->orders->revertToCart($order);

        return $this->done('account.cart.index', __('flash.orders.reverted'));
    }

    public function restore(int $orderId): RedirectResponse
    {
        $order = Order::onlyTrashed()->findOrFail($orderId);
        $this->authorize('restore', $order);

        $this->orders->restore($order);

        return $this->done('account.orders.index', __('flash.orders.restored'));
    }
}
