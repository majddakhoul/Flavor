<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use App\Notifications\Sales\OrderStatusUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOrderStatusUpdate implements ShouldQueue
{
    public function handle(OrderStatusChanged $event): void
    {
        $recipient = $event->order->customer?->user;

        if ($recipient === null) {
            return;
        }

        $recipient->notify(new OrderStatusUpdated([
            'subject_data' => ['reference' => $event->order->reference, 'status' => $event->to->label()],
            'highlight' => $event->to->label(),
            'tone' => $event->to->tone(),
            'rows' => [
                ['label' => __('domain.previous_status'), 'value' => $event->from->label()],
                ['label' => __('domain.current_status'), 'value' => $event->to->label()],
                ['label' => __('domain.reference'), 'value' => $event->order->reference],
            ],
            'action' => ['label' => __('domain.view_order'), 'url' => route('account.orders.show', $event->order)],
        ]));
    }
}
