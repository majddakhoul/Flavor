<?php

namespace App\Repositories\Eloquent;

use App\Models\Ingredient;
use App\Repositories\Contracts\IngredientRepositoryInterface;
use App\Support\QueryOptions;
use Illuminate\Database\Eloquent\Model;

class IngredientRepository extends BaseRepository implements IngredientRepositoryInterface
{
    protected function model(): Model
    {
        return new Ingredient();
    }

    public function lowStock(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return $this->query()->lowStock()->orderBy('stock_quantity')->limit($limit)->get();
    }

    public function stockValue(): int
    {
        return (int) $this->query()->selectRaw('COALESCE(SUM(stock_quantity * unit_cost), 0) as total')->value('total');
    }

    public function lockMany(array $ids): \Illuminate\Database\Eloquent\Collection
    {
        return $this->query()->whereIn('id', $ids)->lockForUpdate()->get();
    }

    public function mostConsumed(int $limit = 10): \Illuminate\Support\Collection
    {
        return \Illuminate\Support\Facades\DB::table('ingredient_meal')
            ->join('ingredients', 'ingredients.id', '=', 'ingredient_meal.ingredient_id')
            ->join('meal_order', 'meal_order.meal_id', '=', 'ingredient_meal.meal_id')
            ->join('orders', 'orders.id', '=', 'meal_order.order_id')
            ->whereNull('orders.deleted_at')
            ->selectRaw('ingredients.id, ingredients.name, ingredients.unit, SUM(ingredient_meal.quantity * meal_order.quantity) as consumed')
            ->groupBy('ingredients.id', 'ingredients.name', 'ingredients.unit')
            ->orderByDesc('consumed')
            ->limit($limit)
            ->get();
    }
}
