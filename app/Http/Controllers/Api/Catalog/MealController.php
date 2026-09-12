<?php

namespace App\Http\Controllers\Api\Catalog;

use App\Http\Controllers\Api\Controller;
use App\Http\Resources\MealResource;
use App\Services\Catalog\MealService;
use App\Support\QueryOptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MealController extends Controller
{
    public function __construct(private readonly MealService $meals)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->meals->menu(QueryOptions::fromRequest(
            $request,
            ['category', 'picture', 'ingredients', 'translations'],
            (int) config('flavor.pagination.menu')
        ));

        return $this->paginated($paginator, MealResource::class);
    }

    public function featured(Request $request): JsonResponse
    {
        return $this->collection($this->meals->featured((int) $request->input('limit', 6)), MealResource::class);
    }

    public function topSelling(Request $request): JsonResponse
    {
        return $this->ok($this->meals->topSelling((int) $request->input('limit', 5)));
    }

    public function show(int $meal): JsonResponse
    {
        return $this->ok(new MealResource($this->meals->find($meal)));
    }
}
