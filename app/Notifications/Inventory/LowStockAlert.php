<?php

namespace App\Notifications\Inventory;

use App\Mail\LowStockMail;
use App\Notifications\FlavorNotification;
use Illuminate\Mail\Mailable;

class LowStockAlert extends FlavorNotification
{
    protected function subjectKey(): string
    {
        return 'inventory.low_stock';
    }

    protected function mailable(string $locale): ?Mailable
    {
        return new LowStockMail($this->payload, $locale);
    }

    protected function icon(): string
    {
        return 'box';
    }

    protected function tone(): string
    {
        return 'warning';
    }
}
