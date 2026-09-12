<?php

namespace App\Listeners;

use App\Events\EmployeeHired;
use App\Notifications\People\EmployeeCredentialsIssued;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendEmployeeCredentials implements ShouldQueue
{
    public function handle(EmployeeHired $event): void
    {
        $user = $event->employee->user;

        $user->notify(new EmployeeCredentialsIssued([
            'subject_data' => ['name' => $user->full_name],
            'highlight' => $event->password,
            'rows' => [
                ['label' => __('domain.email'), 'value' => $user->email],
                ['label' => __('domain.position'), 'value' => $event->employee->position->label()],
                ['label' => __('domain.hire_date'), 'value' => $event->employee->hire_date?->translatedFormat('d M Y') ?? '-'],
            ],
            'action' => ['label' => __('domain.sign_in'), 'url' => route('login')],
            'footnote' => __('mail.people.credentials.footnote'),
        ]));
    }
}
