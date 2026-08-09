<?php

namespace App\Services\People;

use App\DTOs\MaintenanceData;
use App\Models\Maintenance;
use App\Repositories\Contracts\MaintenanceRepositoryInterface;
use App\Services\Support\CacheService;
use App\Support\QueryOptions;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MaintenanceService
{
    private const TAGS = ['maintenance', 'dashboard'];

    public function __construct(
        private readonly MaintenanceRepositoryInterface $maintenances,
        private readonly CacheService $cache,
    ) {
    }

    public function paginate(QueryOptions $options): LengthAwarePaginator
    {
        return $this->maintenances->paginate($options->withRelations(['employee.user']));
    }

    public function create(MaintenanceData $data): Maintenance
    {
        $maintenance = $this->maintenances->create($this->withTotal($data));
        $this->cache->flush(self::TAGS);

        return $maintenance;
    }

    public function update(Maintenance $maintenance, MaintenanceData $data): Maintenance
    {
        $this->maintenances->update($maintenance, $this->withTotal($data));
        $this->cache->flush(self::TAGS);

        return $maintenance->refresh();
    }

    public function delete(Maintenance $maintenance): void
    {
        $this->maintenances->delete($maintenance);
        $this->cache->flush(self::TAGS);
    }

    public function totals(): array
    {
        return $this->cache->remember('statistics', 'maintenance:totals', fn () => $this->maintenances->totals(), self::TAGS);
    }

    protected function withTotal(MaintenanceData $data): array
    {
        $attributes = $data->toArray();
        $discount = (int) ($data->discount ?? 0);
        $attributes['total_price'] = (int) round($data->price - ($data->price * $discount / 100));

        return $attributes;
    }
}
