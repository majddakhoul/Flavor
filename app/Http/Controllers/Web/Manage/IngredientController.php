<?php

namespace App\Http\Controllers\Web\Manage;

use App\DTOs\IngredientData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manage\AdjustStockRequest;
use App\Http\Requests\Manage\IngredientRequest;
use App\Models\Ingredient;
use App\Services\Catalog\IngredientService;
use App\Support\QueryOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IngredientController extends Controller
{
    public function __construct(private readonly IngredientService $ingredientService)
    {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Ingredient::class);

        return view('manage.ingredients.index', [
            'ingredients' => $this->ingredientService->paginate(QueryOptions::fromRequest($request)),
            'snapshot' => $this->ingredientService->inventorySnapshot(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Ingredient::class);

        return view('manage.ingredients.create', $this->formData());
    }

    public function store(IngredientRequest $request): RedirectResponse
    {
        $this->authorize('create', Ingredient::class);

        $this->ingredientService->create(IngredientData::fromArray($request->validated()));

        return $this->done('manage.ingredients.index', __('flash.ingredients.created'));
    }

    public function edit(Ingredient $ingredient): View
    {
        $this->authorize('update', $ingredient);

        return view('manage.ingredients.edit', $this->formData() + ['ingredient' => $ingredient]);
    }

    public function update(IngredientRequest $request, Ingredient $ingredient): RedirectResponse
    {
        $this->authorize('update', $ingredient);

        $this->ingredientService->update($ingredient, IngredientData::fromArray($request->validated()));

        return $this->done('manage.ingredients.index', __('flash.ingredients.updated'));
    }

    public function destroy(Ingredient $ingredient): RedirectResponse
    {
        $this->authorize('delete', $ingredient);

        $this->ingredientService->delete($ingredient);

        return $this->done('manage.ingredients.index', __('flash.ingredients.deleted'));
    }

    public function adjust(AdjustStockRequest $request, Ingredient $ingredient): RedirectResponse
    {
        $this->authorize('update', $ingredient);

        $this->ingredientService->adjustStock($ingredient, (int) $request->validated()['stock_quantity']);

        return $this->done('manage.ingredients.index', __('flash.ingredients.stock_adjusted'));
    }

    protected function formData(): array
    {
        return [];
    }
}
