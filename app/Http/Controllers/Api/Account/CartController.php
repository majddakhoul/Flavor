<?php

namespace App\Http\Controllers\Api\Account;

use App\DTOs\CartItemData;
use App\Enums\CartItemType;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Account\CartItemRequest;
use App\Services\Sales\CartService;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cart)
    {
    }

    public function index(): JsonResponse
    {
        return $this->ok($this->cartPayload());
    }

    public function store(CartItemRequest $request): JsonResponse
    {
        $this->cart->add(CartItemData::fromArray($request->validated()));

        return $this->ok($this->cartPayload(), __('flash.cart.added'));
    }

    public function update(CartItemRequest $request): JsonResponse
    {
        $this->cart->update(CartItemData::fromArray($request->validated()));

        return $this->ok($this->cartPayload(), __('flash.cart.updated'));
    }

    public function destroy(string $type, int $id): JsonResponse
    {
        $this->cart->remove(CartItemType::from($type), $id);

        return $this->ok($this->cartPayload(), __('flash.cart.removed'));
    }

    public function clear(): JsonResponse
    {
        $this->cart->clear();

        return $this->noContent(__('flash.cart.cleared'));
    }

    protected function cartPayload(): array
    {
        $summary = $this->cart->summary();

        return [
            'lines' => $summary['lines'],
            'meals_total' => $summary['meals_total'],
            'offers_total' => $summary['offers_total'],
            'total' => $summary['total'],
            'count' => $summary['count'],
        ];
    }
}
