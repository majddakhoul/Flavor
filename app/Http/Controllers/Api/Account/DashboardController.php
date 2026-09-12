<?php

namespace App\Http\Controllers\Api\Account;

use App\Enums\OrderStatus;
use App\Http\Controllers\Api\Controller;
use App\Http\Resources\OfferResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ReservationResource;
use App\Services\Catalog\OfferService;
use App\Services\Sales\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
        private readonly OfferService $offers,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $customer = $request->user()->customer;

        return $this->ok([
            'cart_count' => $this->cart->count(),
            'open_orders' => $customer?->orders()->whereIn('status', [
                OrderStatus::Pending->value,
                OrderStatus::Confirmed->value,
            ])->count() ?? 0,
            'offers' => OfferResource::collection($this->offers->running(2)),
            'orders' => OrderResource::collection(
                $customer?->orders()->with(['meals.ingredients', 'offers.meals.ingredients'])->latest()->limit(4)->get() ?? []
            ),
            'reservations' => ReservationResource::collection(
                $customer?->reservations()->with('tables')->active()->upcoming()->orderBy('date')->limit(4)->get() ?? []
            ),
        ]);
    }
}
