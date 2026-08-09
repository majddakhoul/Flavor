<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LowStockDetected
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly \App\Models\Ingredient $ingredient,
    ) {
    }
}
