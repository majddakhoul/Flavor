<?php

namespace App\Services\People;

use App\DTOs\EmployeeData;
use App\Enums\EmployeePosition;
use App\Enums\UserType;
use App\Events\EmployeeHired;
use App\Models\Employee;
use App\Models\User;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\Support\CacheService;
use App\Support\QueryOptions;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EmployeeService
{
    private const TAGS = ['people', 'dashboard'];

    public function __construct(
        private readonly EmployeeRepositoryInterface $employees,
        private readonly UserRepositoryInterface $users,
        private readonly CacheService $cache,
    ) {
    }

    public function paginate(QueryOptions $options): LengthAwarePaginator
    {
        return $this->employees->paginate($options->withRelations(['user.location']));
    }

    public function create(EmployeeData $data): Employee
    {
        $password = $data->password ?? Str::password(12);

        $employee = DB::transaction(function () use ($data, $password) {
            $user = $this->users->create([
                'first_name' => $data->first_name,
                'last_name' => $data->last_name,
                'email' => $data->email,
                'phone' => $data->phone,
                'gender' => $data->gender,
                'password' => $password,
                'location_id' => $data->location_id,
                'status' => true,
                'user_type' => $data->position === EmployeePosition::Manager->value
                    ? UserType::Manager->value
                    : UserType::Employee->value,
                'email_verified_at' => now(),
            ]);

            return $this->employees->create($data->toArray() + ['user_id' => $user->id]);
        });

        $this->cache->flush(self::TAGS);

        event(new EmployeeHired($employee->load('user'), $password));

        return $employee;
    }

    public function update(Employee $employee, EmployeeData $data): Employee
    {
        DB::transaction(function () use ($employee, $data) {
            $this->employees->update($employee, $data->toArray());

            $employee->user->update(array_filter([
                'first_name' => $data->first_name,
                'last_name' => $data->last_name,
                'email' => $data->email,
                'phone' => $data->phone,
                'gender' => $data->gender,
                'location_id' => $data->location_id,
                'user_type' => $data->position === EmployeePosition::Manager->value
                    ? UserType::Manager->value
                    : UserType::Employee->value,
            ], static fn ($value) => $value !== null));
        });

        $this->cache->flush(self::TAGS);

        return $employee->refresh();
    }

    public function delete(Employee $employee): void
    {
        DB::transaction(function () use ($employee) {
            $user = $employee->user;
            $this->employees->delete($employee);
            $user?->delete();
        });

        $this->cache->flush(self::TAGS);
    }

    public function payroll(): array
    {
        return $this->cache->remember('statistics', 'employees:payroll', fn () => [
            'totals' => $this->employees->payrollTotals(),
            'headcount_by_position' => $this->employees->headcountByPosition(),
        ], self::TAGS);
    }
}
