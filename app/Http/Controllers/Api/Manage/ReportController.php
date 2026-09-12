<?php

namespace App\Http\Controllers\Api\Manage;

use App\Http\Controllers\Api\Controller;
use App\Services\Catalog\IngredientService;
use App\Services\Dashboard\DashboardService;
use App\Services\People\EmployeeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboard,
        private readonly IngredientService $ingredients,
        private readonly EmployeeService $employees,
    ) {
    }

    public function finance(Request $request): JsonResponse
    {
        $this->authorize('view-reports');

        $from = $request->filled('from') ? Carbon::parse($request->input('from')) : now()->startOfMonth();
        $to = $request->filled('to') ? Carbon::parse($request->input('to')) : now()->endOfMonth();

        return $this->ok([
            'report' => $this->dashboard->financeReport($from, $to),
            'monthly' => $this->dashboard->monthlyRevenue(),
            'payroll' => $this->employees->payroll(),
        ]);
    }

    public function inventory(): JsonResponse
    {
        $this->authorize('view-reports');

        return $this->ok($this->ingredients->inventorySnapshot());
    }

    public function menu(): JsonResponse
    {
        $this->authorize('view-reports');

        return $this->ok($this->dashboard->menuPerformance());
    }

    public function mostConsumedIngredients(Request $request): JsonResponse
    {
        $this->authorize('view-reports');

        return $this->ok($this->ingredients->mostConsumed((int) $request->input('limit', 10)));
    }
}
