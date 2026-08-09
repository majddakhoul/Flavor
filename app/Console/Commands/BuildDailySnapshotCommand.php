<?php

namespace App\Console\Commands;

use App\Jobs\BuildDailySnapshot;
use Illuminate\Console\Command;

class BuildDailySnapshotCommand extends Command
{
    protected $signature = 'flavor:daily-snapshot';

    protected $description = 'Warm the dashboard caches and build yesterday financial snapshot';

    public function handle(): int
    {
        BuildDailySnapshot::dispatch();

        $this->info('Daily snapshot queued.');

        return self::SUCCESS;
    }
}
