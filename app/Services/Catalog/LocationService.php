<?php

namespace App\Services\Catalog;

use App\DTOs\LocationData;
use App\Exceptions\Domain\DomainException;
use App\Models\Location;
use App\Repositories\Contracts\LocationRepositoryInterface;
use App\Services\Support\CacheService;
use App\Support\QueryOptions;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LocationService
{
    private const TAGS = ['lookups'];

    public function __construct(
        private readonly LocationRepositoryInterface $locations,
        private readonly CacheService $cache,
    ) {
    }

    public function paginate(QueryOptions $options): LengthAwarePaginator
    {
        return $this->locations->paginate($options);
    }

    public function options(): array
    {
        return $this->cache->remember('lookups', 'locations:options', fn () => $this->locations->optionsLabelled(), self::TAGS);
    }

    public function create(LocationData $data): Location
    {
        $location = $this->locations->create($data->toArray());
        $this->cache->flush(self::TAGS);

        return $location;
    }

    public function update(Location $location, LocationData $data): Location
    {
        $this->locations->update($location, $data->toArray());
        $this->cache->flush(self::TAGS);

        return $location->refresh();
    }

    public function delete(Location $location): void
    {
        if ($location->users()->exists() || $location->orders()->exists()) {
            throw new DomainException(__('errors.location_in_use'), 422);
        }

        $this->locations->delete($location);
        $this->cache->flush(self::TAGS);
    }
}
