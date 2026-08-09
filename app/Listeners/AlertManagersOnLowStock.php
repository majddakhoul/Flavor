<?php

namespace App\Listeners;

use App\Enums\UserType;
use App\Events\LowStockDetected;
use App\Mail\LowStockMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class AlertManagersOnLowStock implements ShouldQueue
{
    public function handle(LowStockDetected $event): void
    {
        $ingredient = $event->ingredient;
        $latchKey = 'flavor:low-stock-latch:' . $ingredient->id;

        if (Cache::has($latchKey)) {
            return;
        }

        Cache::put($latchKey, true, now()->addHours(12));

        $recipients = User::query()
            ->where('user_type', UserType::Manager->value)
            ->where('status', true)
            ->pluck('email')
            ->all();

        if ($recipients === []) {
            return;
        }

        Mail::to($recipients)->send(new LowStockMail([
            'subject_data' => ['ingredient' => $ingredient->name],
            'highlight' => $ingredient->stock_quantity . ' ' . $ingredient->unit,
            'rows' => [
                ['label' => __('domain.ingredient'), 'value' => $ingredient->name],
                ['label' => __('domain.remaining'), 'value' => $ingredient->stock_quantity . ' ' . $ingredient->unit],
                ['label' => __('domain.threshold'), 'value' => (string) config('flavor.inventory.low_stock_threshold')],
            ],
            'action' => ['label' => __('domain.open_inventory'), 'url' => route('manage.ingredients.index')],
        ], config('app.fallback_locale')));
    }
}
