<?php

namespace App\Http\Controllers\Web\Account;

use App\DTOs\CartItemData;
use App\Enums\CartItemType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Account\CartItemRequest;
use App\Services\Sales\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cart)
    {
    }

    public function index(): View
    {
        return view('account.cart.index', ['cart' => $this->cart->summary()]);
    }

    public function store(CartItemRequest $request): RedirectResponse
    {
        $this->cart->add(CartItemData::fromArray($request->validated()));

        return $this->flash(back(), __('flash.cart.added'));
    }

    public function update(CartItemRequest $request): RedirectResponse
    {
        $this->cart->update(CartItemData::fromArray($request->validated()));

        return $this->flash(back(), __('flash.cart.updated'));
    }

    public function destroy(string $type, int $id): RedirectResponse
    {
        $this->cart->remove(CartItemType::from($type), $id);

        return $this->flash(back(), __('flash.cart.removed'));
    }

    public function clear(): RedirectResponse
    {
        $this->cart->clear();

        return $this->flash(back(), __('flash.cart.cleared'));
    }
}
