<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $tableNumbers;

    public function __construct($reservation, array $tableNumbers)
    {
        $this->reservation = $reservation;
        $this->tableNumbers = $tableNumbers;
    }

    public function build()
    {
        return $this->subject('Your Reservation Details')
                    ->view('emails.reservation-code')
                    ->with([
                        'reservation' => $this->reservation,
                        'tableNumbers' => $this->tableNumbers,
                    ]);
    }
}