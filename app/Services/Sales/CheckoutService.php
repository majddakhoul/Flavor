<?php

namespace App\Services\Sales;

use App\DTOs\CheckoutData;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\UserType;
use App\Events\OrderPlaced;
use App\Exceptions\Domain\CartEmptyException;
use App\Exceptions\Domain\DomainException;
use App\Exceptions\Domain\ItemUnavailableException;
use App\Models\Meal;
use App\Models\Offer;
use App\Models\Order;
use App\Models\User;
use App\Services\Inventory\StockService;
use App\Services\Support\CacheService;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    public function __construct(
        private readonly CartService $cart,
        private readonly StockService $stock,
        private readonly CacheService $cache,
    ) {
    }

    public function place(User $user, CheckoutData $data): Order
    {
        if ($this->cart->isEmpty()) {
            throw CartEmptyException::make();
        }

        $summary = $this->cart->summary();
        $this->assertLinesOrderable($summary['lines']);

        $requirements = $this->requirements($summary);
        $attributes = $this->attributesFor($user, $data);

        $order = DB::transaction(function () use ($summary, $requirements, $attributes) {
            $this->stock->consume($requirements, __('domain.checkout_context'));

            $order = Order::create($attributes);

            foreach ($summary['lines'] as $line) {
                $relation = $line['type']->bucket();
                $order->{$relation}()->attach($line['id'], [
                    'quantity' => $line['quantity'],
                    'notes' => $line['notes'],
                ]);
            }

            return $order;
        });

        $this->cart->clear();
        $this->cache->flush(['orders', 'dashboard', 'inventory']);

        event(new OrderPlaced($order->fresh(['meals.ingredients', 'offers.meals.ingredients', 'customer.user'])));

        return $order;
    }

    protected function requirements(array $summary): array
    {
        $sets = [];

        foreach ($summary['lines'] as $line) {
            $sets[] = $line['type']->bucket() === 'meals'
                ? $this->stock->requirementsForMeal($summary['meals']->get($line['id']), $line['quantity'])
                : $this->stock->requirementsForOffer($summary['offers']->get($line['id']), $line['quantity']);
        }

        return $this->stock->merge(...$sets);
    }

    protected function assertLinesOrderable(array $lines): void
    {
        foreach ($lines as $line) {
            if ($line['orderable']) {
                continue;
            }

            throw $line['type']->bucket() === 'meals'
                ? ItemUnavailableException::meal($line['label'])
                : ItemUnavailableException::offer($line['label']);
        }
    }

    protected function attributesFor(User $user, CheckoutData $data): array
    {
        $attributes = [
            'notes' => $data->notes,
            'status' => OrderStatus::Pending->value,
            'dated_at' => now()->toDateString(),
            'reservation_id' => $data->reservation_id,
        ];

        if ($user->user_type === UserType::Customer) {
            $customer = $user->customer;

            if ($customer === null) {
                throw new DomainException(__('errors.customer_profile_missing'), 422);
            }

            $locationId = $data->location_id ?? $user->location_id;

            if ($locationId === null) {
                throw new DomainException(__('errors.delivery_location_required'), 422);
            }

            return $attributes + [
                'order_type' => OrderType::Delivery->value,
                'customer_id' => $customer->id,
                'location_id' => $locationId,
            ];
        }

        $employee = $user->employee;

        if ($employee === null) {
            throw new DomainException(__('errors.employee_profile_missing'), 422);
        }

        return $attributes + [
            'order_type' => $data->reservation_id !== null ? OrderType::Reservation->value : OrderType::Takeaway->value,
            'employee_id' => $employee->id,
            'location_id' => null,
        ];
    }
}
