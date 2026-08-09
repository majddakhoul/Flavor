<?php

namespace App\Support;

use Illuminate\Http\Request;

final class QueryOptions
{
    public const MAX_PER_PAGE = 100;

    public function __construct(
        public readonly ?string $search = null,
        public readonly array $filters = [],
        public readonly ?string $sortBy = null,
        public readonly string $sortDirection = 'desc',
        public readonly int $perPage = 15,
        public readonly array $with = [],
    ) {
    }

    public static function fromRequest(Request $request, array $with = [], int $defaultPerPage = 15): self
    {
        $filters = array_filter(
            (array) $request->input('filters', []),
            static fn ($value) => $value !== null && $value !== ''
        );

        return new self(
            search: self::sanitizeSearch($request->input('search')),
            filters: $filters,
            sortBy: $request->filled('sort_by') ? (string) $request->input('sort_by') : null,
            sortDirection: strtolower((string) $request->input('sort_dir')) === 'asc' ? 'asc' : 'desc',
            perPage: self::clampPerPage((int) $request->input('per_page', $defaultPerPage)),
            with: $with,
        );
    }

    public function withRelations(array $relations): self
    {
        return new self(
            $this->search,
            $this->filters,
            $this->sortBy,
            $this->sortDirection,
            $this->perPage,
            array_values(array_unique(array_merge($this->with, $relations))),
        );
    }

    public function filter(string $key, mixed $default = null): mixed
    {
        return $this->filters[$key] ?? $default;
    }

    public function hasSearch(): bool
    {
        return $this->search !== null && $this->search !== '';
    }

    public function cacheSignature(): string
    {
        return md5(json_encode([
            $this->search,
            $this->filters,
            $this->sortBy,
            $this->sortDirection,
            $this->perPage,
            $this->with,
            request()->integer('page', 1),
            app()->getLocale(),
        ]));
    }

    private static function clampPerPage(int $perPage): int
    {
        return max(1, min($perPage, self::MAX_PER_PAGE));
    }

    private static function sanitizeSearch(mixed $term): ?string
    {
        if (! is_string($term)) {
            return null;
        }

        $term = trim($term);

        return $term === '' ? null : mb_substr($term, 0, 120);
    }
}
