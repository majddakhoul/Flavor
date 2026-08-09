<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\RepositoryInterface;
use App\Support\QueryOptions;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements RepositoryInterface
{
    abstract protected function model(): Model;

    public function query(): Builder
    {
        return $this->model()->newQuery();
    }

    public function paginate(QueryOptions $options): LengthAwarePaginator
    {
        return $this->query()
            ->applyOptions($options)
            ->paginate($options->perPage)
            ->withQueryString();
    }

    public function collect(QueryOptions $options): Collection
    {
        return $this->query()->applyOptions($options)->get();
    }

    public function find(int $id, array $with = []): ?Model
    {
        return $this->query()->with($with)->find($id);
    }

    public function findOrFail(int $id, array $with = []): Model
    {
        return $this->query()->with($with)->findOrFail($id);
    }

    public function lockById(int $id): Model
    {
        return $this->query()->whereKey($id)->lockForUpdate()->firstOrFail();
    }

    public function create(array $attributes): Model
    {
        return $this->model()->newInstance()->create($attributes);
    }

    public function update(Model $model, array $attributes): Model
    {
        $model->fill($attributes)->save();

        return $model->refresh();
    }

    public function delete(Model $model): bool
    {
        return (bool) $model->delete();
    }

    public function count(array $filters = []): int
    {
        return $this->query()->applyFilters($filters)->count();
    }

    public function pluckOptions(string $labelColumn = 'name', string $valueColumn = 'id'): array
    {
        return $this->query()->orderBy($labelColumn)->pluck($labelColumn, $valueColumn)->all();
    }

    protected function newQueryWithRelations(array $with): Builder
    {
        return $this->query()->with($with);
    }
}
