<?php

namespace App\Jobs;

use App\Enums\MealAvailability;
use App\Models\Meal;
use App\Services\Support\CacheService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncMenuAvailability implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public function __construct()
    {
        $this->onQueue(config('flavor.queues.maintenance'));
    }

    public function handle(CacheService $cache): void
    {
        Meal::query()
            ->with('ingredients')
            ->chunkById(100, function ($meals) {
                foreach ($meals as $meal) {
                    $target = $meal->in_stock ? MealAvailability::Available : MealAvailability::Unavailable;

                    if ($meal->availability !== $target) {
                        $meal->update(['availability' => $target->value]);
                    }
                }
            });

        $cache->flush(['menu', 'catalog', 'dashboard']);
    }
}
