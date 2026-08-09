<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReservationCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly \App\Models\Reservation $reservation,
    ) {
    }
}
