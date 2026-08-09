<?php

namespace App\Repositories\Contracts;

use App\Models\Maintenance;
use App\Support\QueryOptions;

interface MaintenanceRepositoryInterface extends RepositoryInterface
{
    public function totals(): array;
}
