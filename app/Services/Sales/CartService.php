<?php

namespace App\Services\Sales;

use App\DTOs\CartItemData;
use App\Enums\CartItemType;
use App\Exceptions\Domain\ItemUnavailableException;
use App\Models\Meal;
use App\Models\Offer;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CartService
{
    public function add(CartItemData $item): array
    {
        $cart = $this->contents();
        $bucket = $item->type->bucket();
        $this->assertOrderable($item);

        $current = (int) ($cart[$bucket][$item->id]['quantity'] ?? 0);
        $quantity = min($current + $item->quantity, config('flavor.orders.max_item_quantity'));

        $cart[$bucket][$item->id] = [
            'quantity' => $quantity,
            'notes' => $item->notes ?? ($cart[$bucket][$item->id]['notes'] ?? null),
        ];

        return $this->persist($cart);
    }

    public function update(CartItemData $item): array
    {
        $cart = $this->contents();
        $bucket = $item->type->bucket();

        if (! isset($cart[$bucket][$item->id])) {
            return $cart;
        }

        $cart[$bucket][$item->id] = [
            'quantity' => min($item->quantity, config('flavor.orders.max_item_quantity')),
            'notes' => $item->notes,
        ];

        return $this->persist($cart);
    }

    public function remove(CartItemType $type, int $id): array
    {
        $cart = $this->contents();
        unset($cart[$type->bucket()][$id]);

        return $this->persist($cart);
    }

    public function clear(): void
    {
        Cache::forget($this->key());
    }

    public function contents(): array
    {
        return Cache::get($this->key(), ['meals' => [], 'offers' => []]);
    }

    public function isEmpty(): bool
    {
        $cart = $this->contents();

        return $cart['meals'] === [] && $cart['offers'] === [];
    }

    public function count(): int
    {
        $cart = $this->contents();

        return (int) collect($cart['meals'])->sum('quantity') + (int) collect($cart['offers'])->sum('quantity');
    }

    public function summary(): array
    {
        $cart = $this->contents();
        $lines = [];
        $mealsTotal = 0;
        $offersTotal = 0;

        $meals = Meal::query()
            ->with(['ingredients', 'picture', 'translations'])
            ->whereIn('id', array_keys($cart['meals']))
            ->get()
            ->keyBy('id');

        foreach ($cart['meals'] as $id => $line) {
            $meal = $meals->get((int) $id);

            if ($meal === null) {
                continue;
            }

            $subtotal = $meal->price * $line['quantity'];
            $mealsTotal += $subtotal;

            $lines[] = [
                'type' => CartItemType::Meal,
                'id' => $meal->id,
                'label' => $meal->t('name'),
                'image' => $meal->image_url,
                'unit_price' => $meal->price,
                'quantity' => (int) $line['quantity'],
                'subtotal' => $subtotal,
                'notes' => $line['notes'] ?? null,
                'orderable' => $meal->is_orderable,
                'max' => max(1, $meal->max_portions),
            ];
        }

        $offers = Offer::query()
            ->with(['meals.ingredients', 'meals.picture', 'translations'])
            ->whereIn('id', array_keys($cart['offers']))
            ->get()
            ->keyBy('id');

        foreach ($cart['offers'] as $id => $line) {
            $offer = $offers->get((int) $id);

            if ($offer === null) {
                continue;
            }

            $subtotal = $offer->final_price * $line['quantity'];
            $offersTotal += $subtotal;

            $lines[] = [
                'type' => CartItemType::Offer,
                'id' => $offer->id,
                'label' => $offer->t('title'),
                'image' => $offer->meals->first()?->image_url ?? asset('assets/img/meals/placeholder.svg'),
                'unit_price' => $offer->final_price,
                'quantity' => (int) $line['quantity'],
                'subtotal' => $subtotal,
                'notes' => $line['notes'] ?? null,
                'orderable' => $offer->is_orderable,
                'max' => config('flavor.orders.max_item_quantity'),
            ];
        }

        return [
            'lines' => $lines,
            'meals' => $meals,
            'offers' => $offers,
            'meals_total' => $mealsTotal,
            'offers_total' => $offersTotal,
            'total' => $mealsTotal + $offersTotal,
            'count' => $this->count(),
        ];
    }

    public function fillFromOrder(Order $order): array
    {
        $order->loadMissing(['meals', 'offers']);
        $cart = ['meals' => [], 'offers' => []];

        foreach ($order->meals as $meal) {
            $cart['meals'][$meal->id] = [
                'quantity' => (int) $meal->pivot->quantity,
                'notes' => $meal->pivot->notes,
            ];
        }

        foreach ($order->offers as $offer) {
            $cart['offers'][$offer->id] = [
                'quantity' => (int) $offer->pivot->quantity,
                'notes' => $offer->pivot->notes,
            ];
        }

        return $this->persist($cart);
    }

    protected function assertOrderable(CartItemData $item): void
    {
        if ($item->type === CartItemType::Meal) {
            $meal = Meal::query()->with('ingredients')->findOrFail($item->id);

            if (! $meal->is_orderable) {
                throw ItemUnavailableException::meal($meal->t('name'));
            }

            return;
        }

        $offer = Offer::query()->with('meals.ingredients')->findOrFail($item->id);

        if (! $offer->is_orderable) {
            throw ItemUnavailableException::offer($offer->t('title'));
        }
    }

    protected function persist(array $cart): array
    {
        Cache::put($this->key(), $cart, now()->addDays(7));

        return $cart;
    }

    protected function key(): string
    {
        return config('flavor.orders.cart_session_prefix') . '.' . Auth::id();
    }
}
