<?php

namespace App\Jobs;

use App\Services\Dashboard\DashboardService;
use App\Services\Support\CacheService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BuildDailySnapshot implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct()
    {
        $this->onQueue(config('flavor.queues.reports'));
    }

    public function handle(DashboardService $dashboard, CacheService $cache): void
    {
        $cache->flush(['dashboard']);
        $dashboard->overview();
        $dashboard->financeReport(now()->subDay()->startOfDay(), now()->subDay()->endOfDay());
    }
}
