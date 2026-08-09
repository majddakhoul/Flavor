<?php

namespace App\Repositories\Eloquent;

use App\Models\Location;
use App\Repositories\Contracts\LocationRepositoryInterface;
use App\Support\QueryOptions;
use Illuminate\Database\Eloquent\Model;

class LocationRepository extends BaseRepository implements LocationRepositoryInterface
{
    protected function model(): Model
    {
        return new Location();
    }

    public function optionsLabelled(): array
    {
        return $this->query()
            ->orderBy('city')
            ->get()
            ->mapWithKeys(fn (Location $location) => [$location->id => $location->label])
            ->all();
    }
}
