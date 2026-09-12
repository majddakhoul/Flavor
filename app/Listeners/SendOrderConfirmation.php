<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Notifications\Sales\OrderConfirmed;
use App\Support\Money;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOrderConfirmation implements ShouldQueue
{
    public function handle(OrderPlaced $event): void
    {
        $order = $event->order;
        $recipient = $order->customer?->user;

        if ($recipient === null) {
            return;
        }

        $recipient->notify(new OrderConfirmed([
            'subject_data' => ['reference' => $order->reference],
            'highlight' => $order->reference,
            'rows' => [
                ['label' => __('domain.order_type'), 'value' => $order->order_type->label()],
                ['label' => __('domain.items'), 'value' => (string) $order->items_count],
                ['label' => __('domain.total'), 'value' => Money::format($order->total)],
                ['label' => __('domain.placed_on'), 'value' => $order->created_at->translatedFormat('d M Y H:i')],
            ],
            'action' => ['label' => __('domain.view_order'), 'url' => route('account.orders.show', $order)],
        ]));
    }
}
