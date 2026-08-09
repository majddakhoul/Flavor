<?php

namespace App\Repositories\Contracts;

use App\Models\Customer;
use App\Support\QueryOptions;

interface CustomerRepositoryInterface extends RepositoryInterface
{
    public function forUser(int $userId): ?Customer;

    public function bannedCount(): int;
}
