<?php

namespace App\Repositories\Eloquent;

use App\Models\Employee;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Support\QueryOptions;
use Illuminate\Database\Eloquent\Model;

class EmployeeRepository extends BaseRepository implements EmployeeRepositoryInterface
{
    protected function model(): Model
    {
        return new Employee();
    }

    public function forUser(int $userId): ?Employee
    {
        return $this->query()->with('user')->where('user_id', $userId)->first();
    }

    public function payrollTotals(): array
    {
        $row = $this->query()
            ->selectRaw('COALESCE(SUM(salary), 0) as salaries, COALESCE(SUM(bonus), 0) as bonuses, COUNT(*) as headcount')
            ->first();

        return [
            'salaries' => (int) $row->salaries,
            'bonuses' => (int) $row->bonuses,
            'headcount' => (int) $row->headcount,
            'total' => (int) $row->salaries + (int) $row->bonuses,
        ];
    }

    public function headcountByPosition(): array
    {
        return $this->query()
            ->selectRaw('position, COUNT(*) as aggregate')
            ->groupBy('position')
            ->pluck('aggregate', 'position')
            ->all();
    }
}
