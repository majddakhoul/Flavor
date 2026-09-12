<?php

namespace App\Services\Catalog;

use App\DTOs\IngredientData;
use App\Exceptions\Domain\DomainException;
use App\Models\Ingredient;
use App\Repositories\Contracts\IngredientRepositoryInterface;
use App\Services\Inventory\StockService;
use App\Services\Support\CacheService;
use App\Support\QueryOptions;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class IngredientService
{
    private const TAGS = ['inventory', 'menu', 'dashboard'];

    public function __construct(
        private readonly IngredientRepositoryInterface $ingredients,
        private readonly StockService $stock,
        private readonly CacheService $cache,
    ) {
    }

    public function paginate(QueryOptions $options): LengthAwarePaginator
    {
        return $this->ingredients->paginate($options->withRelations(['translations']));
    }

    public function create(IngredientData $data): Ingredient
    {
        $ingredient = DB::transaction(function () use ($data) {
            $ingredient = $this->ingredients->create($data->toArray());
            $ingredient->syncTranslations($data->translations);

            return $ingredient;
        });

        $this->cache->flush(self::TAGS);

        return $ingredient;
    }

    public function update(Ingredient $ingredient, IngredientData $data): Ingredient
    {
        DB::transaction(function () use ($ingredient, $data) {
            $this->ingredients->update($ingredient, $data->toArray());
            $ingredient->syncTranslations($data->translations);
        });

        $this->cache->flush(self::TAGS);

        return $ingredient->refresh();
    }

    public function adjustStock(Ingredient $ingredient, int $quantity): Ingredient
    {
        return $this->stock->adjust($ingredient, $quantity);
    }

    public function delete(Ingredient $ingredient): void
    {
        if ($ingredient->meals()->exists()) {
            throw new DomainException(__('errors.ingredient_in_use'), 422);
        }

        $this->ingredients->delete($ingredient);
        $this->cache->flush(self::TAGS);
    }

    public function inventorySnapshot(): array
    {
        return $this->cache->remember('statistics', 'inventory:snapshot', fn () => [
            'value' => $this->ingredients->stockValue(),
            'low' => $this->ingredients->query()->lowStock()->count(),
            'active' => $this->ingredients->query()->active()->count(),
            'total' => $this->ingredients->query()->count(),
            'watchlist' => $this->ingredients->lowStock(8),
        ], self::TAGS);
    }

    public function mostConsumed(int $limit = 10): \Illuminate\Support\Collection
    {
        return $this->cache->remember(
            'statistics',
            'ingredients:most-consumed:' . $limit,
            fn () => $this->ingredients->mostConsumed($limit),
            self::TAGS
        );
    }
}
