<?php

namespace App\Listeners;

use App\Events\ReservationCreated;
use App\Notifications\Reservations\NewReservationReceived;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NotifyStaffOfNewReservation implements ShouldQueue
{
    public function __construct(private readonly UserRepositoryInterface $users)
    {
    }

    public function handle(ReservationCreated $event): void
    {
        $reservation = $event->reservation;
        $recipients = $this->users->activeStaffWithAbility('floor');

        if ($recipients->isEmpty()) {
            return;
        }

        Notification::send($recipients, new NewReservationReceived([
            'subject_data' => ['code' => $reservation->reservation_code],
            'action' => ['url' => route('manage.reservations.show', $reservation)],
        ]));
    }
}
