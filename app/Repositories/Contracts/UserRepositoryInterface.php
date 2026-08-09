<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use App\Support\QueryOptions;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function findByEmail(string $email): ?User;

    public function staffPaginated(QueryOptions $options): \Illuminate\Contracts\Pagination\LengthAwarePaginator;
}
