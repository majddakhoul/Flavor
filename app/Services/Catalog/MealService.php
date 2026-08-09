<?php

namespace App\Services\Catalog;

use App\DTOs\MealData;
use App\Models\Meal;
use App\Repositories\Contracts\MealRepositoryInterface;
use App\Services\Media\PictureService;
use App\Services\Support\CacheService;
use App\Support\QueryOptions;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class MealService
{
    private const TAGS = ['menu', 'catalog', 'dashboard'];

    public function __construct(
        private readonly MealRepositoryInterface $meals,
        private readonly PictureService $pictures,
        private readonly CacheService $cache,
    ) {
    }

    public function paginate(QueryOptions $options): LengthAwarePaginator
    {
        return $this->meals->paginate($options->withRelations(['category', 'picture', 'ingredients', 'translations']));
    }

    public function menu(QueryOptions $options): LengthAwarePaginator
    {
        return $this->meals->menu($options);
    }

    public function featured(int $limit = 6): Collection
    {
        return $this->cache->remember(
            'menu',
            'meals:featured:' . $limit,
            fn () => $this->meals->featured($limit),
            self::TAGS
        );
    }

    public function find(int $id): Meal
    {
        return $this->meals->findOrFail($id, ['category', 'picture', 'ingredients', 'ratings', 'translations', 'offers']);
    }

    public function create(MealData $data, ?UploadedFile $image = null): Meal
    {
        $meal = DB::transaction(function () use ($data, $image) {
            $meal = $this->meals->create($data->toArray());
            $meal->syncTranslations($data->translations);

            if ($image !== null) {
                $this->pictures->storeForMeal($meal, $image);
            }

            return $meal;
        });

        $this->cache->flush(self::TAGS);

        return $meal->refresh();
    }

    public function update(Meal $meal, MealData $data, ?UploadedFile $image = null): Meal
    {
        DB::transaction(function () use ($meal, $data, $image) {
            $this->meals->update($meal, $data->toArray());
            $meal->syncTranslations($data->translations);

            if ($image !== null) {
                $this->pictures->storeForMeal($meal, $image);
            }
        });

        $this->cache->flush(self::TAGS);

        return $meal->refresh();
    }

    public function delete(Meal $meal): void
    {
        DB::transaction(function () use ($meal) {
            $this->pictures->detachFromMeal($meal);
            $meal->ingredients()->detach();
            $meal->offers()->detach();
            $this->meals->delete($meal);
        });

        $this->cache->flush(self::TAGS);
    }

    public function syncIngredients(Meal $meal, array $ingredients): Meal
    {
        $payload = [];

        foreach ($ingredients as $ingredientId => $quantity) {
            if ((float) $quantity <= 0) {
                continue;
            }

            $payload[(int) $ingredientId] = ['quantity' => (float) $quantity];
        }

        $meal->ingredients()->sync($payload);
        $this->cache->flush(self::TAGS);

        return $meal->refresh();
    }

    public function detachIngredient(Meal $meal, int $ingredientId): Meal
    {
        $meal->ingredients()->detach($ingredientId);
        $this->cache->flush(self::TAGS);

        return $meal->refresh();
    }
}
