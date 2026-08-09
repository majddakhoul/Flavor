<?php

namespace App\Http\Controllers\Web\Manage;

use App\DTOs\MaintenanceData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manage\MaintenanceRequest;
use App\Models\Maintenance;
use App\Services\People\MaintenanceService;
use App\Support\QueryOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Repositories\Contracts\EmployeeRepositoryInterface;

class MaintenanceController extends Controller
{
    public function __construct(private readonly MaintenanceService $maintenanceService, private readonly EmployeeRepositoryInterface $employees)
    {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Maintenance::class);

        return view('manage.maintenances.index', [
            'maintenances' => $this->maintenanceService->paginate(QueryOptions::fromRequest($request)),
            'totals' => $this->maintenanceService->totals(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Maintenance::class);

        return view('manage.maintenances.create', $this->formData());
    }

    public function store(MaintenanceRequest $request): RedirectResponse
    {
        $this->authorize('create', Maintenance::class);

        $this->maintenanceService->create(MaintenanceData::fromArray($request->validated()));

        return $this->done('manage.maintenances.index', __('flash.maintenances.created'));
    }

    public function edit(Maintenance $maintenance): View
    {
        $this->authorize('update', $maintenance);

        return view('manage.maintenances.edit', $this->formData() + ['maintenance' => $maintenance]);
    }

    public function update(MaintenanceRequest $request, Maintenance $maintenance): RedirectResponse
    {
        $this->authorize('update', $maintenance);

        $this->maintenanceService->update($maintenance, MaintenanceData::fromArray($request->validated()));

        return $this->done('manage.maintenances.index', __('flash.maintenances.updated'));
    }

    public function destroy(Maintenance $maintenance): RedirectResponse
    {
        $this->authorize('delete', $maintenance);

        $this->maintenanceService->delete($maintenance);

        return $this->done('manage.maintenances.index', __('flash.maintenances.deleted'));
    }

    protected function formData(): array
    {
        return ['employees' => $this->employees->query()->with('user')->get()];
    }
}
