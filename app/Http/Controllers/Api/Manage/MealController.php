<?php

namespace App\Http\Controllers\Api\Manage;

use App\DTOs\MealData;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Manage\MealIngredientsRequest;
use App\Http\Requests\Manage\MealRequest;
use App\Http\Resources\MealResource;
use App\Models\Meal;
use App\Services\Catalog\MealService;
use App\Services\Media\PictureService;
use App\Support\QueryOptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MealController extends Controller
{
    public function __construct(
        private readonly MealService $meals,
        private readonly PictureService $pictures,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Meal::class);

        return $this->paginated($this->meals->paginate(QueryOptions::fromRequest($request)), MealResource::class);
    }

    public function store(MealRequest $request): JsonResponse
    {
        $this->authorize('create', Meal::class);

        $meal = $this->meals->create(MealData::fromArray($request->validated()), $request->file('image'));

        return $this->created(new MealResource($meal), __('flash.meals.created'));
    }

    public function show(Meal $meal): JsonResponse
    {
        $this->authorize('view', $meal);

        return $this->ok(new MealResource($meal->load(['ingredients', 'picture', 'category', 'translations'])));
    }

    public function update(MealRequest $request, Meal $meal): JsonResponse
    {
        $this->authorize('update', $meal);

        $meal = $this->meals->update($meal, MealData::fromArray($request->validated()), $request->file('image'));

        return $this->ok(new MealResource($meal), __('flash.meals.updated'));
    }

    public function destroy(Meal $meal): JsonResponse
    {
        $this->authorize('delete', $meal);

        $this->meals->delete($meal);

        return $this->noContent(__('flash.meals.deleted'));
    }

    public function syncIngredients(MealIngredientsRequest $request, Meal $meal): JsonResponse
    {
        $this->authorize('update', $meal);

        $meal = $this->meals->syncIngredients($meal, $request->validated()['ingredients']);

        return $this->ok(new MealResource($meal), __('flash.meals.recipe_updated'));
    }

    public function detachIngredient(Meal $meal, int $ingredient): JsonResponse
    {
        $this->authorize('update', $meal);

        $meal = $this->meals->detachIngredient($meal, $ingredient);

        return $this->ok(new MealResource($meal), __('flash.meals.ingredient_removed'));
    }

    public function destroyPicture(Meal $meal): JsonResponse
    {
        $this->authorize('update', $meal);

        $this->pictures->detachFromMeal($meal);

        return $this->noContent(__('flash.meals.photo_removed'));
    }
}
