<?php

namespace App\Http\Controllers\Api\CategoriesControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Category;
class ShowِAllCategoriesController extends Controller
{
    public function showِ_all_categories(Request $request)
    {
        try {
            $query = Category::query();

            if ($request->has('parent_id')) {
                $query->where('parent_id', $request->parent_id);
            }

            if ($request->has('with_relations')) {
                $query->with(['parent', 'children']);
            }

            $categories = $query->get();

            return response()->json([
                'success' => true,
                'message' => 'Categories retrieved successfully.',
                'data' => $categories,
                'status' => 200
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving categories: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Categories retrieval failed.',
                'data' => null,
                'status' => 500
            ], 500);
        }
    }
}
