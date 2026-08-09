<?php

namespace App\Http\Controllers\Web\Manage;

use App\DTOs\LocationData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manage\LocationRequest;
use App\Models\Location;
use App\Services\Catalog\LocationService;
use App\Support\QueryOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function __construct(private readonly LocationService $locationService)
    {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Location::class);

        return view('manage.locations.index', [
            'locations' => $this->locationService->paginate(QueryOptions::fromRequest($request)),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Location::class);

        return view('manage.locations.create', $this->formData());
    }

    public function store(LocationRequest $request): RedirectResponse
    {
        $this->authorize('create', Location::class);

        $this->locationService->create(LocationData::fromArray($request->validated()));

        return $this->done('manage.locations.index', __('flash.locations.created'));
    }

    public function edit(Location $location): View
    {
        $this->authorize('update', $location);

        return view('manage.locations.edit', $this->formData() + ['location' => $location]);
    }

    public function update(LocationRequest $request, Location $location): RedirectResponse
    {
        $this->authorize('update', $location);

        $this->locationService->update($location, LocationData::fromArray($request->validated()));

        return $this->done('manage.locations.index', __('flash.locations.updated'));
    }

    public function destroy(Location $location): RedirectResponse
    {
        $this->authorize('delete', $location);

        $this->locationService->delete($location);

        return $this->done('manage.locations.index', __('flash.locations.deleted'));
    }

    protected function formData(): array
    {
        return [];
    }
}
