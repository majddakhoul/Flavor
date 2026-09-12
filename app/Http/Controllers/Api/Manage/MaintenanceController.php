<?php

namespace App\Http\Controllers\Api\Manage;

use App\DTOs\MaintenanceData;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Manage\MaintenanceRequest;
use App\Http\Resources\MaintenanceResource;
use App\Models\Maintenance;
use App\Services\People\MaintenanceService;
use App\Support\QueryOptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function __construct(private readonly MaintenanceService $maintenanceService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Maintenance::class);

        return $this->paginated(
            $this->maintenanceService->paginate(QueryOptions::fromRequest($request, ['employee.user'])),
            MaintenanceResource::class
        );
    }

    public function totals(): JsonResponse
    {
        $this->authorize('viewAny', Maintenance::class);

        return $this->ok($this->maintenanceService->totals());
    }

    public function store(MaintenanceRequest $request): JsonResponse
    {
        $this->authorize('create', Maintenance::class);

        $maintenance = $this->maintenanceService->create(MaintenanceData::fromArray($request->validated()));

        return $this->created(new MaintenanceResource($maintenance), __('flash.maintenances.created'));
    }

    public function show(Maintenance $maintenance): JsonResponse
    {
        $this->authorize('view', $maintenance);

        return $this->ok(new MaintenanceResource($maintenance->load('employee.user')));
    }

    public function update(MaintenanceRequest $request, Maintenance $maintenance): JsonResponse
    {
        $this->authorize('update', $maintenance);

        $maintenance = $this->maintenanceService->update($maintenance, MaintenanceData::fromArray($request->validated()));

        return $this->ok(new MaintenanceResource($maintenance), __('flash.maintenances.updated'));
    }

    public function destroy(Maintenance $maintenance): JsonResponse
    {
        $this->authorize('delete', $maintenance);

        $this->maintenanceService->delete($maintenance);

        return $this->noContent(__('flash.maintenances.deleted'));
    }
}
