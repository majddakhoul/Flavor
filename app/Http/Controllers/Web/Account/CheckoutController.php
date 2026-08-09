<?php

namespace App\Http\Controllers\Web\Account;

use App\DTOs\CheckoutData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Account\CheckoutRequest;
use App\Services\Catalog\LocationService;
use App\Services\Sales\CartService;
use App\Services\Sales\CheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
        private readonly CheckoutService $checkout,
        private readonly LocationService $locations,
    ) {
    }

    public function create(): View|RedirectResponse
    {
        if ($this->cart->isEmpty()) {
            return $this->flash(redirect()->route('menu.index'), __('errors.cart_empty'), 'warning');
        }

        return view('account.checkout', [
            'cart' => $this->cart->summary(),
            'locations' => $this->locations->options(),
        ]);
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        $order = $this->checkout->place($request->user(), CheckoutData::fromArray($request->validated()));

        return $this->done('account.orders.show', __('flash.orders.placed', ['reference' => $order->reference]), $order);
    }
}
