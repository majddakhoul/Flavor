<?php

namespace App\Repositories\Contracts;

use App\Models\Table;
use App\Support\QueryOptions;

interface TableRepositoryInterface extends RepositoryInterface
{
    public function availableBetween(string $start, string $end, int $partySize = 1): \Illuminate\Database\Eloquent\Collection;

    public function occupancyToday(): array;
}
