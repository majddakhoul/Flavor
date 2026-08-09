<?php

namespace App\Services\Catalog;

use App\DTOs\CategoryData;
use App\Exceptions\Domain\DomainException;
use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Services\Support\CacheService;
use App\Support\QueryOptions;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    private const TAGS = ['menu', 'catalog'];

    public function __construct(
        private readonly CategoryRepositoryInterface $categories,
        private readonly CacheService $cache,
    ) {
    }

    public function paginate(QueryOptions $options): LengthAwarePaginator
    {
        return $this->categories->paginate($options->withRelations(['parent', 'children', 'translations']));
    }

    public function tree(): Collection
    {
        return $this->cache->remember('catalog', 'categories:tree', fn () => $this->categories->tree(), self::TAGS);
    }

    public function options(): array
    {
        return $this->cache->remember('catalog', 'categories:options', fn () => $this->categories->optionsWithDepth(), self::TAGS);
    }

    public function create(CategoryData $data): Category
    {
        $category = DB::transaction(function () use ($data) {
            $category = $this->categories->create($data->toArray());
            $category->syncTranslations($data->translations);

            return $category;
        });

        $this->cache->flush(self::TAGS);

        return $category;
    }

    public function update(Category $category, CategoryData $data): Category
    {
        if ($data->parent_id === $category->id) {
            throw new DomainException(__('errors.category_self_parent'), 422);
        }

        DB::transaction(function () use ($category, $data) {
            $this->categories->update($category, $data->toArray());
            $category->syncTranslations($data->translations);
        });

        $this->cache->flush(self::TAGS);

        return $category->refresh();
    }

    public function delete(Category $category): void
    {
        if ($category->meals()->exists()) {
            throw new DomainException(__('errors.category_has_meals'), 422);
        }

        $this->categories->delete($category);
        $this->cache->flush(self::TAGS);
    }
}
