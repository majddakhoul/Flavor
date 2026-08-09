<?php

namespace App\Http\Controllers\Web\Manage;

use App\DTOs\MealData;
use App\Enums\MealAvailability;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manage\MealIngredientsRequest;
use App\Http\Requests\Manage\MealRequest;
use App\Models\Meal;
use App\Repositories\Contracts\IngredientRepositoryInterface;
use App\Services\Catalog\CategoryService;
use App\Services\Catalog\MealService;
use App\Services\Media\PictureService;
use App\Support\QueryOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MealController extends Controller
{
    public function __construct(
        private readonly MealService $meals,
        private readonly CategoryService $categories,
        private readonly IngredientRepositoryInterface $ingredients,
        private readonly PictureService $pictures,
    ) {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Meal::class);

        return view('manage.meals.index', [
            'meals' => $this->meals->paginate(QueryOptions::fromRequest($request)),
            'categories' => $this->categories->options(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Meal::class);

        return view('manage.meals.create', $this->formData());
    }

    public function store(MealRequest $request): RedirectResponse
    {
        $this->authorize('create', Meal::class);

        $meal = $this->meals->create(
            MealData::fromArray($request->validated()),
            $request->file('image')
        );

        return $this->done('manage.meals.edit', __('flash.meals.created'), $meal);
    }

    public function edit(Meal $meal): View
    {
        $this->authorize('update', $meal);

        return view('manage.meals.edit', $this->formData() + [
            'meal' => $meal->load(['ingredients', 'picture', 'translations']),
        ]);
    }

    public function update(MealRequest $request, Meal $meal): RedirectResponse
    {
        $this->authorize('update', $meal);

        $this->meals->update($meal, MealData::fromArray($request->validated()), $request->file('image'));

        return $this->done('manage.meals.edit', __('flash.meals.updated'), $meal);
    }

    public function destroy(Meal $meal): RedirectResponse
    {
        $this->authorize('delete', $meal);

        $this->meals->delete($meal);

        return $this->done('manage.meals.index', __('flash.meals.deleted'));
    }

    public function syncIngredients(MealIngredientsRequest $request, Meal $meal): RedirectResponse
    {
        $this->authorize('update', $meal);

        $this->meals->syncIngredients($meal, $request->validated()['ingredients']);

        return $this->done('manage.meals.edit', __('flash.meals.recipe_updated'), $meal);
    }

    public function detachIngredient(Meal $meal, int $ingredient): RedirectResponse
    {
        $this->authorize('update', $meal);

        $this->meals->detachIngredient($meal, $ingredient);

        return $this->done('manage.meals.edit', __('flash.meals.ingredient_removed'), $meal);
    }

    public function destroyPicture(Meal $meal): RedirectResponse
    {
        $this->authorize('update', $meal);

        $this->pictures->detachFromMeal($meal);

        return $this->done('manage.meals.edit', __('flash.meals.photo_removed'), $meal);
    }

    protected function formData(): array
    {
        return [
            'categories' => $this->categories->options(),
            'ingredients' => $this->ingredients->query()->active()->orderBy('name')->get(),
            'availabilities' => MealAvailability::options(),
        ];
    }
}
