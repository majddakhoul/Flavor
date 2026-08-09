<?php

namespace App\Http\Controllers\Api\CategoriesControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CategoriesRequests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Support\Facades\Log;

class UpdateCategoryController extends Controller
{
    public function update_category(UpdateCategoryRequest $request)
    {
        try {

            $category = Category::find($request->category_id);

            if ($request->category_id == $request->parent_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category cannot be own parent.',
                    'data' => null,
                    'status' => 404
                ], 404);
            }
            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found.',
                    'data' => null,
                    'status' => 404
                ], 404);
            }


            $category->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Category update successfully.',
                'data' => $category,
                'status' => 200
            ]);


        } catch (\Exception $e) {
            Log::error('Error updating category: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Category update failed.',
                'data' => null,
                'status' => 500
            ], 500);
        }
    }
}
