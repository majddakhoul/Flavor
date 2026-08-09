<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Mail\OrderConfirmationMail;
use App\Support\Money;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmation implements ShouldQueue
{
    public function handle(OrderPlaced $event): void
    {
        $order = $event->order;
        $recipient = $order->customer?->user;

        if ($recipient === null) {
            return;
        }

        Mail::to($recipient->email)->send(new OrderConfirmationMail([
            'subject_data' => ['reference' => $order->reference],
            'highlight' => $order->reference,
            'rows' => [
                ['label' => __('domain.order_type'), 'value' => $order->order_type->label()],
                ['label' => __('domain.items'), 'value' => (string) $order->items_count],
                ['label' => __('domain.total'), 'value' => Money::format($order->total)],
                ['label' => __('domain.placed_on'), 'value' => $order->created_at->translatedFormat('d M Y H:i')],
            ],
            'action' => ['label' => __('domain.view_order'), 'url' => route('account.orders.show', $order)],
        ], app()->getLocale()));
    }
}
