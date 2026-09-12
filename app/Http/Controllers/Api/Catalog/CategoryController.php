<?php

namespace App\Http\Controllers\Api\Catalog;

use App\Http\Controllers\Api\Controller;
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
        $paginator = $this->categories->paginate(QueryOptions::fromRequest($request, ['parent']));

        return $this->paginated($paginator, CategoryResource::class);
    }

    public function tree(): JsonResponse
    {
        return $this->collection($this->categories->tree(), CategoryResource::class);
    }

    public function show(Category $category): JsonResponse
    {
        return $this->ok(new CategoryResource($category->load('children')));
    }
}
