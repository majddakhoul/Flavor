<?php

namespace App\Http\Controllers\Web\Account;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Services\Catalog\OfferService;
use App\Services\Sales\CartService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
        private readonly OfferService $offers,
    ) {
    }

    public function __invoke(Request $request): View
    {
        $customer = $request->user()->customer;

        return view('account.dashboard', [
            'customer' => $customer,
            'cartCount' => $this->cart->count(),
            'offers' => $this->offers->running(2),
            'orders' => $customer?->orders()
                ->with(['meals.ingredients', 'offers.meals.ingredients'])
                ->latest()
                ->limit(4)
                ->get(),
            'reservations' => $customer?->reservations()
                ->with('tables')
                ->active()
                ->upcoming()
                ->orderBy('date')
                ->limit(4)
                ->get(),
            'openOrders' => $customer?->orders()->whereIn('status', [
                OrderStatus::Pending->value,
                OrderStatus::Confirmed->value,
            ])->count() ?? 0,
        ]);
    }
}
