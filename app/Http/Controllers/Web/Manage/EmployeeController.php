<?php

namespace App\Http\Controllers\Web\Manage;

use App\DTOs\EmployeeData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manage\EmployeeRequest;
use App\Models\Employee;
use App\Services\People\EmployeeService;
use App\Support\QueryOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\Catalog\LocationService;

class EmployeeController extends Controller
{
    public function __construct(private readonly EmployeeService $employeeService, private readonly LocationService $locations)
    {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Employee::class);

        return view('manage.employees.index', [
            'employees' => $this->employeeService->paginate(QueryOptions::fromRequest($request)),
            'payroll' => $this->employeeService->payroll(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Employee::class);

        return view('manage.employees.create', $this->formData());
    }

    public function store(EmployeeRequest $request): RedirectResponse
    {
        $this->authorize('create', Employee::class);

        $this->employeeService->create(EmployeeData::fromArray($request->validated()));

        return $this->done('manage.employees.index', __('flash.employees.created'));
    }

    public function edit(Employee $employee): View
    {
        $this->authorize('update', $employee);

        return view('manage.employees.edit', $this->formData() + ['employee' => $employee]);
    }

    public function update(EmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $this->authorize('update', $employee);

        $this->employeeService->update($employee, EmployeeData::fromArray($request->validated()));

        return $this->done('manage.employees.index', __('flash.employees.updated'));
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $this->authorize('delete', $employee);

        $this->employeeService->delete($employee);

        return $this->done('manage.employees.index', __('flash.employees.deleted'));
    }

    protected function formData(): array
    {
        return ['positions' => \App\Enums\EmployeePosition::options(), 'locations' => $this->locations->options(), 'genders' => \App\Enums\Gender::options()];
    }
}
