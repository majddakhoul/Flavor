<?php

namespace App\Mail;

class ReservationStatusMail extends FlavorMailable
{
    protected function subjectKey(): string
    {
        return 'reservations.status';
    }
}
