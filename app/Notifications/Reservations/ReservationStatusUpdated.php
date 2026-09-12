<?php

namespace App\Notifications\Reservations;

use App\Mail\ReservationStatusMail;
use App\Notifications\FlavorNotification;
use Illuminate\Mail\Mailable;

class ReservationStatusUpdated extends FlavorNotification
{
    protected function subjectKey(): string
    {
        return 'reservations.status';
    }

    protected function mailable(string $locale): ?Mailable
    {
        return new ReservationStatusMail($this->payload, $locale);
    }

    protected function icon(): string
    {
        return 'calendar';
    }

    protected function tone(): string
    {
        return $this->payload['tone'] ?? 'info';
    }
}
