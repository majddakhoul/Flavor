<?php

namespace App\Repositories\Eloquent;

use App\Models\Offer;
use App\Repositories\Contracts\OfferRepositoryInterface;
use App\Support\QueryOptions;
use Illuminate\Database\Eloquent\Model;

class OfferRepository extends BaseRepository implements OfferRepositoryInterface
{
    protected function model(): Model
    {
        return new Offer();
    }

    public function running(int $limit = 4): \Illuminate\Database\Eloquent\Collection
    {
        return $this->query()
            ->running()
            ->with(['meals.ingredients', 'meals.picture', 'translations'])
            ->limit($limit)
            ->get();
    }
}
