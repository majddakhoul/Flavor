<?php

namespace App\Http\Controllers\Api\Catalog;

use App\Http\Controllers\Api\Controller;
use App\Http\Resources\LocationResource;
use App\Repositories\Contracts\LocationRepositoryInterface;
use App\Support\QueryOptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function __construct(private readonly LocationRepositoryInterface $locations)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->locations->paginate(QueryOptions::fromRequest($request));

        return $this->paginated($paginator, LocationResource::class);
    }
}
