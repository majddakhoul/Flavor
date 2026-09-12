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

    public function topSelling(int $limit = 5): \Illuminate\Support\Collection
    {
        return \Illuminate\Support\Facades\DB::table('offer_order')
            ->join('offers', 'offers.id', '=', 'offer_order.offer_id')
            ->join('orders', 'orders.id', '=', 'offer_order.order_id')
            ->whereNull('orders.deleted_at')
            ->selectRaw('offers.id, offers.title, SUM(offer_order.quantity) as sold')
            ->groupBy('offers.id', 'offers.title')
            ->orderByDesc('sold')
            ->limit($limit)
            ->get();
    }
}
