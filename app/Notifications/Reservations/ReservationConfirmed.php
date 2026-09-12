<?php

namespace App\Notifications\Reservations;

use App\Mail\ReservationTicketMail;
use App\Notifications\FlavorNotification;
use Illuminate\Mail\Mailable;

class ReservationConfirmed extends FlavorNotification
{
    protected function subjectKey(): string
    {
        return 'reservations.ticket';
    }

    protected function mailable(string $locale): ?Mailable
    {
        return new ReservationTicketMail($this->payload, $locale);
    }

    protected function icon(): string
    {
        return 'calendar';
    }

    protected function tone(): string
    {
        return 'success';
    }
}
