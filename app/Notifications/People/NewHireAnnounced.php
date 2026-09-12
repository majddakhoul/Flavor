<?php

namespace App\Notifications\People;

use App\Notifications\FlavorNotification;

class NewHireAnnounced extends FlavorNotification
{
    protected function subjectKey(): string
    {
        return 'people.new_hire';
    }

    protected function channels(): array
    {
        return ['database'];
    }

    protected function icon(): string
    {
        return 'user';
    }

    protected function tone(): string
    {
        return 'info';
    }
}
