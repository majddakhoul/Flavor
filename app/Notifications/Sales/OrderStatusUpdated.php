<?php

namespace App\Notifications\Sales;

use App\Mail\OrderStatusMail;
use App\Notifications\FlavorNotification;
use Illuminate\Mail\Mailable;

class OrderStatusUpdated extends FlavorNotification
{
    protected function subjectKey(): string
    {
        return 'orders.status';
    }

    protected function mailable(string $locale): ?Mailable
    {
        return new OrderStatusMail($this->payload, $locale);
    }

    protected function icon(): string
    {
        return 'receipt';
    }

    protected function tone(): string
    {
        return $this->payload['tone'] ?? 'info';
    }
}
