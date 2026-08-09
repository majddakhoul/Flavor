<?php

namespace App\Services\Catalog;

use App\DTOs\OfferData;
use App\Models\Offer;
use App\Repositories\Contracts\OfferRepositoryInterface;
use App\Services\Support\CacheService;
use App\Support\QueryOptions;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class OfferService
{
    private const TAGS = ['menu', 'catalog', 'dashboard'];

    public function __construct(
        private readonly OfferRepositoryInterface $offers,
        private readonly CacheService $cache,
    ) {
    }

    public function paginate(QueryOptions $options): LengthAwarePaginator
    {
        return $this->offers->paginate($options->withRelations(['meals.ingredients', 'translations']));
    }

    public function running(int $limit = 4): Collection
    {
        return $this->cache->remember(
            'menu',
            'offers:running:' . $limit,
            fn () => $this->offers->running($limit),
            self::TAGS
        );
    }

    public function find(int $id): Offer
    {
        return $this->offers->findOrFail($id, ['meals.ingredients', 'meals.picture', 'ratings', 'translations']);
    }

    public function create(OfferData $data): Offer
    {
        $offer = DB::transaction(function () use ($data) {
            $offer = $this->offers->create($data->toArray());
            $offer->syncTranslations($data->translations);
            $this->applyMeals($offer, $data->meals);

            return $offer;
        });

        $this->cache->flush(self::TAGS);

        return $offer->refresh();
    }

    public function update(Offer $offer, OfferData $data): Offer
    {
        DB::transaction(function () use ($offer, $data) {
            $this->offers->update($offer, $data->toArray());
            $offer->syncTranslations($data->translations);
            $this->applyMeals($offer, $data->meals);
        });

        $this->cache->flush(self::TAGS);

        return $offer->refresh();
    }

    public function delete(Offer $offer): void
    {
        DB::transaction(function () use ($offer) {
            $offer->meals()->detach();
            $this->offers->delete($offer);
        });

        $this->cache->flush(self::TAGS);
    }

    public function toggle(Offer $offer): Offer
    {
        $this->offers->update($offer, ['is_active' => ! $offer->is_active]);
        $this->cache->flush(self::TAGS);

        return $offer->refresh();
    }

    public function detachMeal(Offer $offer, int $mealId): Offer
    {
        $offer->meals()->detach($mealId);
        $this->cache->flush(self::TAGS);

        return $offer->refresh();
    }

    protected function applyMeals(Offer $offer, array $meals): void
    {
        if ($meals === []) {
            return;
        }

        $payload = [];

        foreach ($meals as $mealId => $quantity) {
            if ((int) $quantity <= 0) {
                continue;
            }

            $payload[(int) $mealId] = ['quantity' => (int) $quantity];
        }

        $offer->meals()->sync($payload);
    }
}
