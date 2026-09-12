<?php

namespace App\Http\Controllers\Api\Manage;

use App\DTOs\LocationData;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Manage\LocationRequest;
use App\Http\Resources\LocationResource;
use App\Models\Location;
use App\Services\Catalog\LocationService;
use App\Support\QueryOptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function __construct(private readonly LocationService $locations)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Location::class);

        return $this->paginated($this->locations->paginate(QueryOptions::fromRequest($request)), LocationResource::class);
    }

    public function store(LocationRequest $request): JsonResponse
    {
        $this->authorize('create', Location::class);

        $location = $this->locations->create(LocationData::fromArray($request->validated()));

        return $this->created(new LocationResource($location), __('flash.locations.created'));
    }

    public function update(LocationRequest $request, Location $location): JsonResponse
    {
        $this->authorize('update', $location);

        $location = $this->locations->update($location, LocationData::fromArray($request->validated()));

        return $this->ok(new LocationResource($location), __('flash.locations.updated'));
    }

    public function destroy(Location $location): JsonResponse
    {
        $this->authorize('delete', $location);

        $this->locations->delete($location);

        return $this->noContent(__('flash.locations.deleted'));
    }
}
