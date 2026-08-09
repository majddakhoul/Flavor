<?php

namespace App\Http\Controllers\Web\Manage;

use App\DTOs\CategoryData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manage\CategoryRequest;
use App\Models\Category;
use App\Services\Catalog\CategoryService;
use App\Support\QueryOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(private readonly CategoryService $categoryService)
    {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Category::class);

        return view('manage.categories.index', [
            'categories' => $this->categoryService->paginate(QueryOptions::fromRequest($request)),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Category::class);

        return view('manage.categories.create', $this->formData());
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        $this->authorize('create', Category::class);

        $this->categoryService->create(CategoryData::fromArray($request->validated()));

        return $this->done('manage.categories.index', __('flash.categories.created'));
    }

    public function edit(Category $category): View
    {
        $this->authorize('update', $category);

        return view('manage.categories.edit', $this->formData() + ['category' => $category]);
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $this->authorize('update', $category);

        $this->categoryService->update($category, CategoryData::fromArray($request->validated()));

        return $this->done('manage.categories.index', __('flash.categories.updated'));
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->authorize('delete', $category);

        $this->categoryService->delete($category);

        return $this->done('manage.categories.index', __('flash.categories.deleted'));
    }

    protected function formData(): array
    {
        return ['parents' => $this->categoryService->options()];
    }
}
