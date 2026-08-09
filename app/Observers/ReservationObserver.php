<?php

namespace App\Observers;

use App\Models\Reservation;
use App\Models\Customer;

class ReservationObserver
{
    public function updated(Reservation $reservation)
    {
        if ($reservation->isDirty('status') && 
            $reservation->status === 'Cancelled' &&
            $reservation->customer) {
            
            $reservation->customer->checkAndBanForCancellations();
        }
    }
}