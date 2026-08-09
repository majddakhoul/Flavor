<?php

namespace App\Console\Commands;

use App\Services\People\CustomerService;
use Illuminate\Console\Command;

class LiftExpiredBansCommand extends Command
{
    protected $signature = 'flavor:lift-bans';

    protected $description = 'Lift customer reservation bans that have served their term';

    public function handle(CustomerService $customers): int
    {
        $lifted = $customers->liftExpiredBans();

        $this->info(sprintf('%d customer ban(s) lifted.', $lifted));

        return self::SUCCESS;
    }
}
