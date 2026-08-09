<?php

namespace App\Http\Controllers\Api\CategoriesControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CategoriesRequests\PostCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class PostCategoryController extends Controller
{
    public function post_category(PostCategoryRequest $postCategoryRequest)
    {

        $validateData = $postCategoryRequest->validated();

        $NewCategory['name'] = $postCategoryRequest->name;
        $NewCategory['description'] = $validateData['description'] ?? $postCategoryRequest->description;
        $NewCategory['parent_id'] = $validateData['parent_id'] ?? $postCategoryRequest->parent_id;

        $Category = Category::create($NewCategory);

        if ($Category) {

            $Response = [
                'success' => true,
                'message' => 'Category created successfully.',
                'data' => $Category,
                'status' => 201
            ];
            return response()->json($Response, 201);
        } else {

            $Response = [
                'success' => false,
                'message' => 'Category create failed.',
                'data' => null,
                'status' => 400
            ];
            return response()->json($Response, 400);
        }
    }
}
