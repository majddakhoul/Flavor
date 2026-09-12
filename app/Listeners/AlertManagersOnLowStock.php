<?php

namespace App\Listeners;

use App\Events\LowStockDetected;
use App\Notifications\Inventory\LowStockAlert;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Cache;

class AlertManagersOnLowStock implements ShouldQueue
{
    public function __construct(private readonly UserRepositoryInterface $users)
    {
    }

    public function handle(LowStockDetected $event): void
    {
        $ingredient = $event->ingredient;
        $latchKey = 'flavor:low-stock-latch:' . $ingredient->id;

        if (Cache::has($latchKey)) {
            return;
        }

        Cache::put($latchKey, true, now()->addHours(12));

        $recipients = $this->users->activeStaffWithAbility('inventory');

        if ($recipients->isEmpty()) {
            return;
        }

        Notification::send($recipients, new LowStockAlert([
            'subject_data' => ['ingredient' => $ingredient->name],
            'highlight' => $ingredient->stock_quantity . ' ' . $ingredient->unit,
            'rows' => [
                ['label' => __('domain.ingredient'), 'value' => $ingredient->name],
                ['label' => __('domain.remaining'), 'value' => $ingredient->stock_quantity . ' ' . $ingredient->unit],
                ['label' => __('domain.threshold'), 'value' => (string) config('flavor.inventory.low_stock_threshold')],
            ],
            'action' => ['label' => __('domain.open_inventory'), 'url' => route('manage.ingredients.index')],
        ]));
    }
}
