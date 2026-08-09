<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReservationStatusChanged
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly \App\Models\Reservation $reservation,
        public readonly \App\Enums\ReservationStatus $from,
        public readonly \App\Enums\ReservationStatus $to,
    ) {
    }
}
