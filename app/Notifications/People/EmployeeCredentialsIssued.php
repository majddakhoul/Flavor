<?php

namespace App\Notifications\People;

use App\Mail\EmployeeCredentialsMail;
use App\Notifications\FlavorNotification;
use Illuminate\Mail\Mailable;

class EmployeeCredentialsIssued extends FlavorNotification
{
    protected function subjectKey(): string
    {
        return 'people.credentials';
    }

    protected function mailable(string $locale): ?Mailable
    {
        return new EmployeeCredentialsMail($this->payload, $locale);
    }

    protected function icon(): string
    {
        return 'user';
    }

    protected function tone(): string
    {
        return 'success';
    }
}
