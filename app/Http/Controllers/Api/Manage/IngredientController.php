<?php

namespace App\Http\Controllers\Api\Manage;

use App\DTOs\IngredientData;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Manage\IngredientRequest;
use App\Http\Resources\IngredientResource;
use App\Models\Ingredient;
use App\Services\Catalog\IngredientService;
use App\Support\QueryOptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    public function __construct(private readonly IngredientService $ingredients)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Ingredient::class);

        return $this->paginated($this->ingredients->paginate(QueryOptions::fromRequest($request)), IngredientResource::class);
    }

    public function store(IngredientRequest $request): JsonResponse
    {
        $this->authorize('create', Ingredient::class);

        $ingredient = $this->ingredients->create(IngredientData::fromArray($request->validated()));

        return $this->created(new IngredientResource($ingredient), __('flash.ingredients.created'));
    }

    public function show(Ingredient $ingredient): JsonResponse
    {
        $this->authorize('view', $ingredient);

        return $this->ok(new IngredientResource($ingredient));
    }

    public function update(IngredientRequest $request, Ingredient $ingredient): JsonResponse
    {
        $this->authorize('update', $ingredient);

        $ingredient = $this->ingredients->update($ingredient, IngredientData::fromArray($request->validated()));

        return $this->ok(new IngredientResource($ingredient), __('flash.ingredients.updated'));
    }

    public function destroy(Ingredient $ingredient): JsonResponse
    {
        $this->authorize('delete', $ingredient);

        $this->ingredients->delete($ingredient);

        return $this->noContent(__('flash.ingredients.deleted'));
    }

    public function adjustStock(Request $request, Ingredient $ingredient): JsonResponse
    {
        $this->authorize('update', $ingredient);

        $ingredient = $this->ingredients->adjustStock($ingredient, (int) $request->input('quantity', 0));

        return $this->ok(new IngredientResource($ingredient), __('flash.ingredients.updated'));
    }
}
