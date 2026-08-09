<?php

namespace App\Mail;

class ReservationTicketMail extends FlavorMailable
{
    protected function subjectKey(): string
    {
        return 'reservations.ticket';
    }
}
