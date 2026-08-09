<?php

namespace App\Console\Commands;

use App\Jobs\SyncMenuAvailability;
use Illuminate\Console\Command;

class SyncMenuAvailabilityCommand extends Command
{
    protected $signature = 'flavor:sync-availability {--now : Run in the current process instead of queueing}';

    protected $description = 'Recalculate meal availability from ingredient stock levels';

    public function handle(): int
    {
        $this->option('now')
            ? SyncMenuAvailability::dispatchSync()
            : SyncMenuAvailability::dispatch();

        $this->info('Menu availability sync scheduled.');

        return self::SUCCESS;
    }
}
