<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use App\Support\QueryOptions;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function findByEmail(string $email): ?User;

    public function staffPaginated(QueryOptions $options): \Illuminate\Contracts\Pagination\LengthAwarePaginator;

    public function activeManagers(): \Illuminate\Database\Eloquent\Collection;

    public function activeStaffWithAbility(string $ability): \Illuminate\Database\Eloquent\Collection;
}
