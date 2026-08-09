<?php

namespace App\Repositories\Contracts;

use App\Support\QueryOptions;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
{
    public function query(): Builder;

    public function paginate(QueryOptions $options): LengthAwarePaginator;

    public function collect(QueryOptions $options): Collection;

    public function find(int $id, array $with = []): ?Model;

    public function findOrFail(int $id, array $with = []): Model;

    public function lockById(int $id): Model;

    public function create(array $attributes): Model;

    public function update(Model $model, array $attributes): Model;

    public function delete(Model $model): bool;

    public function count(array $filters = []): int;

    public function pluckOptions(string $labelColumn = 'name', string $valueColumn = 'id'): array;
}
