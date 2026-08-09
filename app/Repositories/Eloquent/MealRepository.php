<?php

namespace App\Repositories\Eloquent;

use App\Models\Meal;
use App\Repositories\Contracts\MealRepositoryInterface;
use App\Support\QueryOptions;
use Illuminate\Database\Eloquent\Model;

class MealRepository extends BaseRepository implements MealRepositoryInterface
{
    protected function model(): Model
    {
        return new Meal();
    }

    public function menu(QueryOptions $options): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->query()
            ->available()
            ->with(['category', 'picture', 'ingredients', 'translations'])
            ->applyOptions($options)
            ->paginate($options->perPage)
            ->withQueryString();
    }

    public function featured(int $limit = 6): \Illuminate\Database\Eloquent\Collection
    {
        return $this->query()
            ->available()
            ->with(['category', 'picture', 'ingredients', 'translations'])
            ->withAvg('ratings as rating_avg', 'number_stars')
            ->orderByDesc('rating_avg')
            ->limit($limit)
            ->get();
    }

    public function topSelling(int $limit = 5): \Illuminate\Support\Collection
    {
        return \Illuminate\Support\Facades\DB::table('meal_order')
            ->join('meals', 'meals.id', '=', 'meal_order.meal_id')
            ->join('orders', 'orders.id', '=', 'meal_order.order_id')
            ->whereNull('orders.deleted_at')
            ->selectRaw('meals.id, meals.name, SUM(meal_order.quantity) as sold')
            ->groupBy('meals.id', 'meals.name')
            ->orderByDesc('sold')
            ->limit($limit)
            ->get();
    }
}
