<?php

namespace App\Http\Controllers\Api\Catalog;

use App\Http\Controllers\Api\Controller;
use App\Http\Resources\OfferResource;
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
        $paginator = $this->offers->paginate(QueryOptions::fromRequest(
            $request,
            ['meals.ingredients', 'translations'],
            (int) config('flavor.pagination.menu')
        ));

        return $this->paginated($paginator, OfferResource::class);
    }

    public function running(Request $request): JsonResponse
    {
        return $this->collection($this->offers->running((int) $request->input('limit', 4)), OfferResource::class);
    }

    public function topSelling(Request $request): JsonResponse
    {
        return $this->ok($this->offers->topSelling((int) $request->input('limit', 5)));
    }

    public function show(int $offer): JsonResponse
    {
        return $this->ok(new OfferResource($this->offers->find($offer)));
    }
}
