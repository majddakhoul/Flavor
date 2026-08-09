<?php

namespace App\Repositories\Eloquent;

use App\Models\Table;
use App\Repositories\Contracts\TableRepositoryInterface;
use App\Support\QueryOptions;
use Illuminate\Database\Eloquent\Model;

class TableRepository extends BaseRepository implements TableRepositoryInterface
{
    protected function model(): Model
    {
        return new Table();
    }

    public function availableBetween(string $start, string $end, int $partySize = 1): \Illuminate\Database\Eloquent\Collection
    {
        return $this->query()
            ->active()
            ->fitting($partySize)
            ->freeBetween($start, $end)
            ->orderBy('capacity')
            ->get();
    }

    public function occupancyToday(): array
    {
        $total = $this->query()->active()->count();
        $busy = $this->query()
            ->active()
            ->whereHas('reservations', function ($query) {
                $query->whereDate('reservations.date', now()->toDateString())
                    ->where('reservations.status', '!=', \App\Enums\ReservationStatus::Cancelled->value);
            })
            ->count();

        return [
            'total' => $total,
            'busy' => $busy,
            'free' => max(0, $total - $busy),
            'rate' => $total > 0 ? (int) round($busy / $total * 100) : 0,
        ];
    }
}
