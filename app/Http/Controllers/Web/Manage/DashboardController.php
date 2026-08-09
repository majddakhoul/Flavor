<?php

namespace App\Http\Controllers\Web\Manage;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardService $dashboard)
    {
    }

    public function __invoke(): View
    {
        $this->authorize('view-dashboard');

        $overview = $this->dashboard->overview();

        return view('manage.dashboard', [
            'kpis' => $overview['kpis'],
            'charts' => $overview['charts'],
            'lists' => $overview['lists'],
        ]);
    }
}
