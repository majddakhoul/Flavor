<?php

namespace App\Services\Reservations;

use App\Enums\ReservationStatus;
use App\Exceptions\Domain\TableUnavailableException;
use App\Models\Table;
use App\Repositories\Contracts\TableRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TableAvailabilityService
{
    public function __construct(private readonly TableRepositoryInterface $tables)
    {
    }

    public function availableBetween(string $start, string $end, int $partySize = 1): Collection
    {
        return $this->tables->availableBetween($start, $end, $partySize);
    }

    public function conflictingTableIds(array $tableIds, string $start, string $end, ?int $ignoreReservationId = null): array
    {
        if ($tableIds === []) {
            return [];
        }

        return DB::table('reservation_table')
            ->join('reservations', 'reservation_table.reservation_id', '=', 'reservations.id')
            ->whereIn('reservation_table.table_id', $tableIds)
            ->where('reservations.status', '!=', ReservationStatus::Cancelled->value)
            ->when($ignoreReservationId, fn ($query) => $query->where('reservations.id', '!=', $ignoreReservationId))
            ->where('reservation_table.start_time', '<', $end)
            ->where('reservation_table.end_time', '>', $start)
            ->lockForUpdate()
            ->pluck('reservation_table.table_id')
            ->unique()
            ->values()
            ->all();
    }

    public function assertAvailable(array $tableIds, string $start, string $end, ?int $ignoreReservationId = null): Collection
    {
        $tables = Table::query()->whereIn('id', $tableIds)->lockForUpdate()->get();

        if ($tables->count() !== count(array_unique($tableIds))) {
            throw TableUnavailableException::forTables([__('domain.unknown_table')]);
        }

        $conflicts = $this->conflictingTableIds($tableIds, $start, $end, $ignoreReservationId);

        if ($conflicts !== []) {
            throw TableUnavailableException::forTables(
                $tables->whereIn('id', $conflicts)->pluck('table_number')->all()
            );
        }

        return $tables;
    }

    public function assertCapacity(Collection $tables, int $partySize): void
    {
        $capacity = (int) $tables->sum('capacity');

        if ($capacity < $partySize) {
            throw TableUnavailableException::capacity($capacity, $partySize);
        }
    }

    public function occupancy(): array
    {
        return $this->tables->occupancyToday();
    }
}
