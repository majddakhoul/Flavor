<?php

namespace App\Http\Controllers\Api\Manage;

use App\Http\Controllers\Api\Controller;
use App\Services\Dashboard\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardService $dashboard)
    {
    }

    public function __invoke(): JsonResponse
    {
        return $this->ok($this->dashboard->overview());
    }
}
