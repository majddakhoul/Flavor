<?php

namespace App\Http\Controllers\Web\Manage;

use App\Http\Controllers\Controller;
use App\Services\Catalog\IngredientService;
use App\Services\Dashboard\DashboardService;
use App\Services\People\EmployeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboard,
        private readonly IngredientService $ingredients,
        private readonly EmployeeService $employees,
    ) {
    }

    public function finance(Request $request): View
    {
        $this->authorize('view-reports');

        $from = $request->filled('from') ? Carbon::parse($request->input('from')) : now()->startOfMonth();
        $to = $request->filled('to') ? Carbon::parse($request->input('to')) : now()->endOfMonth();

        return view('manage.reports.finance', [
            'report' => $this->dashboard->financeReport($from, $to),
            'monthly' => $this->dashboard->monthlyRevenue(),
            'payroll' => $this->employees->payroll(),
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function inventory(): View
    {
        $this->authorize('view-reports');

        return view('manage.reports.inventory', [
            'snapshot' => $this->ingredients->inventorySnapshot(),
        ]);
    }

    public function menu(): View
    {
        $this->authorize('view-reports');

        return view('manage.reports.menu', [
            'performance' => $this->dashboard->menuPerformance(),
        ]);
    }
}
