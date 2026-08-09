<?php

namespace App\Jobs;

use App\Services\People\CustomerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LiftExpiredBans implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct()
    {
        $this->onQueue(config('flavor.queues.maintenance'));
    }

    public function handle(CustomerService $customers): void
    {
        $customers->liftExpiredBans();
    }
}
