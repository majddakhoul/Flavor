<?php

namespace App\Services\Inventory;

use App\Events\LowStockDetected;
use App\Exceptions\Domain\InsufficientStockException;
use App\Models\Ingredient;
use App\Models\Meal;
use App\Models\Offer;
use App\Models\Order;
use App\Repositories\Contracts\IngredientRepositoryInterface;
use App\Services\Support\CacheService;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function __construct(
        private readonly IngredientRepositoryInterface $ingredients,
        private readonly CacheService $cache,
    ) {
    }

    public function requirementsForMeal(Meal $meal, int $quantity): array
    {
        $meal->loadMissing('ingredients');
        $requirements = [];

        foreach ($meal->ingredients as $ingredient) {
            $requirements[$ingredient->id] = ($requirements[$ingredient->id] ?? 0)
                + ($ingredient->pivot->quantity * $quantity);
        }

        return $requirements;
    }

    public function requirementsForOffer(Offer $offer, int $quantity): array
    {
        $offer->loadMissing('meals.ingredients');
        $requirements = [];

        foreach ($offer->meals as $meal) {
            $mealQuantity = (int) ($meal->pivot->quantity ?? 1) * $quantity;

            foreach ($this->requirementsForMeal($meal, $mealQuantity) as $ingredientId => $needed) {
                $requirements[$ingredientId] = ($requirements[$ingredientId] ?? 0) + $needed;
            }
        }

        return $requirements;
    }

    public function requirementsForOrder(Order $order): array
    {
        $order->loadMissing(['meals.ingredients', 'offers.meals.ingredients']);
        $requirements = [];

        foreach ($order->meals as $meal) {
            foreach ($this->requirementsForMeal($meal, (int) $meal->pivot->quantity) as $ingredientId => $needed) {
                $requirements[$ingredientId] = ($requirements[$ingredientId] ?? 0) + $needed;
            }
        }

        foreach ($order->offers as $offer) {
            foreach ($this->requirementsForOffer($offer, (int) $offer->pivot->quantity) as $ingredientId => $needed) {
                $requirements[$ingredientId] = ($requirements[$ingredientId] ?? 0) + $needed;
            }
        }

        return $requirements;
    }

    public function merge(array ...$requirementSets): array
    {
        $merged = [];

        foreach ($requirementSets as $requirements) {
            foreach ($requirements as $ingredientId => $quantity) {
                $merged[$ingredientId] = ($merged[$ingredientId] ?? 0) + $quantity;
            }
        }

        return $merged;
    }

    public function consume(array $requirements, string $context = ''): void
    {
        if ($requirements === []) {
            return;
        }

        DB::transaction(function () use ($requirements, $context) {
            $locked = $this->ingredients->lockMany(array_keys($requirements));

            foreach ($locked as $ingredient) {
                $needed = $requirements[$ingredient->id];

                if ($ingredient->stock_quantity < $needed) {
                    throw InsufficientStockException::forIngredient(
                        $ingredient->name,
                        $context,
                        $needed,
                        $ingredient->stock_quantity
                    );
                }
            }

            foreach ($locked as $ingredient) {
                $ingredient->decrement('stock_quantity', $requirements[$ingredient->id]);
            }

            $this->announceLowStock($locked->pluck('id')->all());
        });

        $this->flushInventoryCaches();
    }

    public function restore(array $requirements): void
    {
        if ($requirements === []) {
            return;
        }

        DB::transaction(function () use ($requirements) {
            $locked = $this->ingredients->lockMany(array_keys($requirements));

            foreach ($locked as $ingredient) {
                $ingredient->increment('stock_quantity', $requirements[$ingredient->id]);
            }
        });

        $this->flushInventoryCaches();
    }

    public function assertAvailable(array $requirements, string $context = ''): void
    {
        foreach ($requirements as $ingredientId => $needed) {
            $ingredient = $this->ingredients->find((int) $ingredientId);

            if ($ingredient === null || $ingredient->stock_quantity < $needed) {
                throw InsufficientStockException::forIngredient(
                    $ingredient?->name ?? (string) $ingredientId,
                    $context,
                    $needed,
                    $ingredient?->stock_quantity ?? 0
                );
            }
        }
    }

    public function adjust(Ingredient $ingredient, int $quantity): Ingredient
    {
        return DB::transaction(function () use ($ingredient, $quantity) {
            $locked = $this->ingredients->lockById($ingredient->id);
            $locked->update(['stock_quantity' => max(0, $quantity)]);

            $this->announceLowStock([$locked->id]);
            $this->flushInventoryCaches();

            return $locked->refresh();
        });
    }

    protected function announceLowStock(array $ingredientIds): void
    {
        $threshold = (int) config('flavor.inventory.low_stock_threshold');

        Ingredient::query()
            ->whereIn('id', $ingredientIds)
            ->where('stock_quantity', '<=', $threshold)
            ->get()
            ->each(fn (Ingredient $ingredient) => event(new LowStockDetected($ingredient)));
    }

    protected function flushInventoryCaches(): void
    {
        $this->cache->flush(['inventory', 'menu', 'dashboard']);
    }
}
