<?php

namespace App\Http\Controllers\Api\CategoriesControllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Models\Category;
class DeleteCategoryController extends Controller
{
    public function delete_category($CategoryId)
    {
        try {

            $category = Category::withCount('children')->find($CategoryId);

            if (!$category) {
                return response()->json([
                    'data' => null,
                    'success' => false,
                    'message' => 'Category not found.',
                    'status' => 404
                ], 404);
            }

            if ($category->children_count > 0) {
                return response()->json([
                    'data' => null,
                    'success' => false,
                    'message' => 'Can not delete with subcategories.',
                    'status' => 400
                ], 400);
            }

            if ($category->meals()->exists()) {
                return response()->json([
                    'data' => null,
                    'success' => false,
                    'message' => 'Can not delete with meals.',
                    'status' => 400
                ], 400);
            }

            $category->delete();

            return response()->json([
                'data' => null,
                'success' => true,
                'message' => 'Category deleted successfully.',
                'status' => 200
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting category: ' . $e->getMessage());

            return response()->json([
                'data' => null,
                'success' => false,
                'message' => 'Category delete failed.',
                'status' => 500
            ], 500);
        }
    }
}
