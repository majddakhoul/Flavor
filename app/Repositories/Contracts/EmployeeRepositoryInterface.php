<?php

namespace App\Repositories\Contracts;

use App\Models\Employee;
use App\Support\QueryOptions;

interface EmployeeRepositoryInterface extends RepositoryInterface
{
    public function forUser(int $userId): ?Employee;

    public function payrollTotals(): array;

    public function headcountByPosition(): array;
}
