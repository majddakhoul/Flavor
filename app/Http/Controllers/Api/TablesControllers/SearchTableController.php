<?php

namespace App\Http\Controllers\Api\TablesControllers;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class SearchTableController extends Controller
{
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'table_number' => 'required|string|min:3|max:3',
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
            $tables = Table::where('table_number', $request->table_number)->get();

            if ($tables->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No tables found',
                    'data' => null,
                    'status' => 200
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => 'Tables retrieved successfully',
                'data' => $tables,
                'status' => 200
            ], 200);

        } catch (\Exception $e) {
            Log::error('Table search failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to search tables',
                'data' => null,
                'status' => 500
            ], 500);
        }
    }
}
