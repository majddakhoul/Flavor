<?php

namespace App\Http\Controllers\Api\Manage;

use App\DTOs\CategoryData;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Manage\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\Catalog\CategoryService;
use App\Support\QueryOptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(private readonly CategoryService $categories)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Category::class);

        return $this->paginated($this->categories->paginate(QueryOptions::fromRequest($request)), CategoryResource::class);
    }

    public function store(CategoryRequest $request): JsonResponse
    {
        $this->authorize('create', Category::class);

        $category = $this->categories->create(CategoryData::fromArray($request->validated()));

        return $this->created(new CategoryResource($category), __('flash.categories.created'));
    }

    public function show(Category $category): JsonResponse
    {
        $this->authorize('view', $category);

        return $this->ok(new CategoryResource($category->load('children')));
    }

    public function update(CategoryRequest $request, Category $category): JsonResponse
    {
        $this->authorize('update', $category);

        $category = $this->categories->update($category, CategoryData::fromArray($request->validated()));

        return $this->ok(new CategoryResource($category), __('flash.categories.updated'));
    }

    public function destroy(Category $category): JsonResponse
    {
        $this->authorize('delete', $category);

        $this->categories->delete($category);

        return $this->noContent(__('flash.categories.deleted'));
    }
}
