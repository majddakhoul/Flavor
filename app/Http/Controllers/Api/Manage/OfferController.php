<?php

namespace App\Http\Controllers\Api\Manage;

use App\DTOs\OfferData;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Manage\OfferRequest;
use App\Http\Resources\OfferResource;
use App\Models\Offer;
use App\Services\Catalog\OfferService;
use App\Support\QueryOptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function __construct(private readonly OfferService $offers)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Offer::class);

        return $this->paginated($this->offers->paginate(QueryOptions::fromRequest($request)), OfferResource::class);
    }

    public function store(OfferRequest $request): JsonResponse
    {
        $this->authorize('create', Offer::class);

        $offer = $this->offers->create(OfferData::fromArray($request->validated()));

        return $this->created(new OfferResource($offer), __('flash.offers.created'));
    }

    public function show(Offer $offer): JsonResponse
    {
        $this->authorize('view', $offer);

        return $this->ok(new OfferResource($offer->load(['meals', 'translations'])));
    }

    public function update(OfferRequest $request, Offer $offer): JsonResponse
    {
        $this->authorize('update', $offer);

        $offer = $this->offers->update($offer, OfferData::fromArray($request->validated()));

        return $this->ok(new OfferResource($offer), __('flash.offers.updated'));
    }

    public function destroy(Offer $offer): JsonResponse
    {
        $this->authorize('delete', $offer);

        $this->offers->delete($offer);

        return $this->noContent(__('flash.offers.deleted'));
    }

    public function toggle(Offer $offer): JsonResponse
    {
        $this->authorize('update', $offer);

        $offer = $this->offers->toggle($offer);

        return $this->ok(new OfferResource($offer), __('flash.offers.updated'));
    }

    public function detachMeal(Offer $offer, int $meal): JsonResponse
    {
        $this->authorize('update', $offer);

        $offer = $this->offers->detachMeal($offer, $meal);

        return $this->ok(new OfferResource($offer), __('flash.offers.updated'));
    }
}
