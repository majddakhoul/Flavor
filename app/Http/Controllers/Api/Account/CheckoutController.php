<?php

namespace App\Http\Controllers\Api\Account;

use App\DTOs\CheckoutData;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Account\CheckoutRequest;
use App\Http\Resources\OrderResource;
use App\Services\Sales\CheckoutService;
use Illuminate\Http\JsonResponse;

class CheckoutController extends Controller
{
    public function __construct(private readonly CheckoutService $checkout)
    {
    }

    public function store(CheckoutRequest $request): JsonResponse
    {
        $order = $this->checkout->place($request->user(), CheckoutData::fromArray($request->validated()));

        return $this->created(
            new OrderResource($order->load(['meals', 'offers', 'location'])),
            __('flash.orders.placed', ['reference' => $order->reference])
        );
    }
}
