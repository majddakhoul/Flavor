<?php

namespace App\Support\Traits;

use App\Support\QueryOptions;
use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    public function scopeApplyOptions(Builder $query, QueryOptions $options): Builder
    {
        return $query
            ->applySearch($options->search)
            ->applyFilters($options->filters)
            ->applySort($options->sortBy, $options->sortDirection)
            ->with($options->with);
    }

    public function scopeApplySearch(Builder $query, ?string $term): Builder
    {
        $columns = $this->searchableColumns();

        if ($term === null || $term === '' || $columns === []) {
            return $query;
        }

        $escaped = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $term) . '%';

        return $query->where(function (Builder $builder) use ($columns, $escaped) {
            foreach ($columns as $column) {
                if (str_contains($column, '.')) {
                    [$relation, $relationColumn] = explode('.', $column, 2);
                    $builder->orWhereHas($relation, fn (Builder $related) => $related->where($relationColumn, 'like', $escaped));

                    continue;
                }

                $builder->orWhere($column, 'like', $escaped);
            }
        });
    }

    public function scopeApplyFilters(Builder $query, array $filters): Builder
    {
        $allowed = $this->filterableColumns();

        foreach ($filters as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            $method = 'filter' . str_replace(' ', '', ucwords(str_replace('_', ' ', (string) $key)));

            if (method_exists($this, $method)) {
                $this->{$method}($query, $value);

                continue;
            }

            if (! in_array($key, $allowed, true)) {
                continue;
            }

            is_array($value)
                ? $query->whereIn($key, $value)
                : $query->where($key, $value);
        }

        return $query;
    }

    public function scopeApplySort(Builder $query, ?string $column, string $direction = 'desc'): Builder
    {
        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';
        $allowed = $this->sortableColumns();

        if ($column !== null && in_array($column, $allowed, true)) {
            return $query->orderBy($column, $direction);
        }

        return $query->orderBy($this->getQualifiedKeyName(), 'desc');
    }

    public function searchableColumns(): array
    {
        return property_exists($this, 'searchable') ? $this->searchable : [];
    }

    public function filterableColumns(): array
    {
        return property_exists($this, 'filterable') ? $this->filterable : [];
    }

    public function sortableColumns(): array
    {
        return property_exists($this, 'sortable') ? $this->sortable : [];
    }
}
