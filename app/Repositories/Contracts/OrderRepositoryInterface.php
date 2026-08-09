<?php

namespace App\Repositories\Contracts;

use App\Models\Order;
use App\Support\QueryOptions;

interface OrderRepositoryInterface extends RepositoryInterface
{
    public function forCustomer(int $customerId, QueryOptions $options): \Illuminate\Contracts\Pagination\LengthAwarePaginator;

    public function countByStatus(): array;

    public function countByType(): array;

    public function revenueBetween(\Illuminate\Support\Carbon $from, \Illuminate\Support\Carbon $to): int;

    public function dailyVolume(int $days = 14): \Illuminate\Support\Collection;

    public function latest(int $limit = 6): \Illuminate\Database\Eloquent\Collection;

    public function trashedPaginated(QueryOptions $options): \Illuminate\Contracts\Pagination\LengthAwarePaginator;
}
