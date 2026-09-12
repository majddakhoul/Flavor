<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Notifications\Sales\NewOrderReceived;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NotifyStaffOfNewOrder implements ShouldQueue
{
    public function __construct(private readonly UserRepositoryInterface $users)
    {
    }

    public function handle(OrderPlaced $event): void
    {
        $order = $event->order;
        $recipients = $this->users->activeStaffWithAbility('sales');

        if ($recipients->isEmpty()) {
            return;
        }

        Notification::send($recipients, new NewOrderReceived([
            'subject_data' => ['reference' => $order->reference],
            'action' => ['url' => route('manage.orders.show', $order)],
        ]));
    }
}
