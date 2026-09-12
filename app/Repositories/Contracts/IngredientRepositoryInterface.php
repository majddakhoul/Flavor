<?php

namespace App\Repositories\Contracts;

use App\Models\Ingredient;
use App\Support\QueryOptions;

interface IngredientRepositoryInterface extends RepositoryInterface
{
    public function lowStock(int $limit = 10): \Illuminate\Database\Eloquent\Collection;

    public function stockValue(): int;

    public function lockMany(array $ids): \Illuminate\Database\Eloquent\Collection;

    public function mostConsumed(int $limit = 10): \Illuminate\Support\Collection;
}
