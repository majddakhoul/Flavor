<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeHired
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly \App\Models\Employee $employee,
        public readonly string $password,
    ) {
    }
}
