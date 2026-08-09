<?php

namespace App\Repositories\Contracts;

use App\Models\Meal;
use App\Support\QueryOptions;

interface MealRepositoryInterface extends RepositoryInterface
{
    public function menu(QueryOptions $options): \Illuminate\Contracts\Pagination\LengthAwarePaginator;

    public function featured(int $limit = 6): \Illuminate\Database\Eloquent\Collection;

    public function topSelling(int $limit = 5): \Illuminate\Support\Collection;
}
