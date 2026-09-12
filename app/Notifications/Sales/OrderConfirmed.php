<?php

namespace App\Notifications\Sales;

use App\Mail\OrderConfirmationMail;
use App\Notifications\FlavorNotification;
use Illuminate\Mail\Mailable;

class OrderConfirmed extends FlavorNotification
{
    protected function subjectKey(): string
    {
        return 'orders.confirmation';
    }

    protected function mailable(string $locale): ?Mailable
    {
        return new OrderConfirmationMail($this->payload, $locale);
    }

    protected function icon(): string
    {
        return 'receipt';
    }

    protected function tone(): string
    {
        return 'success';
    }
}
