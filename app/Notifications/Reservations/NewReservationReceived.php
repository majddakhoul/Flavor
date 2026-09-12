<?php

namespace App\Notifications\Reservations;

use App\Notifications\FlavorNotification;

class NewReservationReceived extends FlavorNotification
{
    protected function subjectKey(): string
    {
        return 'reservations.new_for_staff';
    }

    protected function channels(): array
    {
        return ['database'];
    }

    protected function icon(): string
    {
        return 'calendar';
    }

    protected function tone(): string
    {
        return 'info';
    }
}
