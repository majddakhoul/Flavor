<?php

namespace App\Listeners;

use App\Events\ReservationCreated;
use App\Mail\ReservationTicketMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendReservationTicket implements ShouldQueue
{
    public function handle(ReservationCreated $event): void
    {
        $reservation = $event->reservation;
        $recipient = $reservation->customer?->user;

        if ($recipient === null) {
            return;
        }

        Mail::to($recipient->email)->send(new ReservationTicketMail([
            'subject_data' => ['code' => $reservation->reservation_code],
            'highlight' => $reservation->reservation_code,
            'rows' => [
                ['label' => __('domain.party_size'), 'value' => (string) $reservation->party_size],
                ['label' => __('domain.tables'), 'value' => $reservation->tables->pluck('table_number')->implode(', ')],
                ['label' => __('domain.from'), 'value' => $reservation->starts_at?->translatedFormat('d M Y H:i') ?? '-'],
                ['label' => __('domain.to'), 'value' => $reservation->ends_at?->translatedFormat('H:i') ?? '-'],
            ],
            'action' => ['label' => __('domain.view_reservation'), 'url' => route('account.reservations.show', $reservation)],
        ], app()->getLocale()));
    }
}
