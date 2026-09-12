<?php

namespace App\Listeners;

use App\Events\EmployeeHired;
use App\Notifications\People\NewHireAnnounced;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NotifyManagersOfNewHire implements ShouldQueue
{
    public function __construct(private readonly UserRepositoryInterface $users)
    {
    }

    public function handle(EmployeeHired $event): void
    {
        $recipients = $this->users->activeManagers();

        if ($recipients->isEmpty()) {
            return;
        }

        Notification::send($recipients, new NewHireAnnounced([
            'subject_data' => [
                'name' => $event->employee->user->full_name,
                'position' => $event->employee->position->label(),
            ],
            'action' => ['url' => route('manage.employees.edit', $event->employee)],
        ]));
    }
}
