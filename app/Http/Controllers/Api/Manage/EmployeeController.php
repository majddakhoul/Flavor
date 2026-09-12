<?php

namespace App\Http\Controllers\Api\Manage;

use App\DTOs\EmployeeData;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Manage\EmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Services\People\EmployeeService;
use App\Support\QueryOptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function __construct(private readonly EmployeeService $employees)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Employee::class);

        return $this->paginated(
            $this->employees->paginate(QueryOptions::fromRequest($request, ['user'])),
            EmployeeResource::class
        );
    }

    public function store(EmployeeRequest $request): JsonResponse
    {
        $this->authorize('create', Employee::class);

        $employee = $this->employees->create(EmployeeData::fromArray($request->validated()));

        return $this->created(new EmployeeResource($employee->load('user')), __('flash.employees.created'));
    }

    public function show(Employee $employee): JsonResponse
    {
        $this->authorize('view', $employee);

        return $this->ok(new EmployeeResource($employee->load('user')));
    }

    public function update(EmployeeRequest $request, Employee $employee): JsonResponse
    {
        $this->authorize('update', $employee);

        $employee = $this->employees->update($employee, EmployeeData::fromArray($request->validated()));

        return $this->ok(new EmployeeResource($employee->load('user')), __('flash.employees.updated'));
    }

    public function destroy(Employee $employee): JsonResponse
    {
        $this->authorize('delete', $employee);

        $this->employees->delete($employee);

        return $this->noContent(__('flash.employees.deleted'));
    }

    public function payroll(): JsonResponse
    {
        $this->authorize('viewAny', Employee::class);

        return $this->ok($this->employees->payroll());
    }
}
