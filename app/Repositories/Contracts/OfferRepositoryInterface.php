<?php

namespace App\Repositories\Contracts;

use App\Models\Offer;
use App\Support\QueryOptions;

interface OfferRepositoryInterface extends RepositoryInterface
{
    public function running(int $limit = 4): \Illuminate\Database\Eloquent\Collection;
}
