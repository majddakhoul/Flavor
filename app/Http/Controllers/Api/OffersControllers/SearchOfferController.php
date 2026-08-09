<?php

namespace App\Http\Controllers\Api\OffersControllers;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class SearchOfferController extends Controller
{
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
                'status' => 422
            ], 422);
        }

        try {
            $offers = Offer::where('title', 'LIKE', '%'.$request->search.'%')
                          ->orWhere('description', 'LIKE', '%'.$request->search.'%')
                          ->get();

            if ($offers->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No offers found',
                    'data' => null,
                    'status' => 200
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => 'Offers retrieved successfully',
                'data' => $offers,
                'status' => 200
            ], 200);

        } catch (\Exception $e) {
            Log::error('Offer search failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to search offers',
                'data' => null,
                'status' => 500
            ], 500);
        }
    }
}