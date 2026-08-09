<?php

namespace App\Listeners;

use App\Events\ReservationStatusChanged;
use App\Mail\ReservationStatusMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendReservationStatusUpdate implements ShouldQueue
{
    public function handle(ReservationStatusChanged $event): void
    {
        $recipient = $event->reservation->customer?->user;

        if ($recipient === null) {
            return;
        }

        Mail::to($recipient->email)->send(new ReservationStatusMail([
            'subject_data' => ['code' => $event->reservation->reservation_code, 'status' => $event->to->label()],
            'highlight' => $event->to->label(),
            'rows' => [
                ['label' => __('domain.reservation_code'), 'value' => $event->reservation->reservation_code],
                ['label' => __('domain.previous_status'), 'value' => $event->from->label()],
                ['label' => __('domain.current_status'), 'value' => $event->to->label()],
            ],
            'action' => ['label' => __('domain.view_reservation'), 'url' => route('account.reservations.show', $event->reservation)],
        ], app()->getLocale()));
    }
}
