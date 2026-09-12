<?php

namespace App\Notifications\Sales;

use App\Notifications\FlavorNotification;

class NewOrderReceived extends FlavorNotification
{
    protected function subjectKey(): string
    {
        return 'orders.new_for_staff';
    }

    protected function channels(): array
    {
        return ['database'];
    }

    protected function icon(): string
    {
        return 'receipt';
    }

    protected function tone(): string
    {
        return 'info';
    }
}
