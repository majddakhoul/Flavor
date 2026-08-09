<?php

namespace App\Repositories\Contracts;

use App\Models\Reservation;
use App\Support\QueryOptions;

interface ReservationRepositoryInterface extends RepositoryInterface
{
    public function forCustomer(int $customerId, QueryOptions $options): \Illuminate\Contracts\Pagination\LengthAwarePaginator;

    public function findByCode(string $code): ?Reservation;

    public function countByStatus(): array;

    public function upcoming(int $limit = 5): \Illuminate\Database\Eloquent\Collection;
}
