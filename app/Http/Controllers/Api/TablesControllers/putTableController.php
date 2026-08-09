<?php

namespace App\Http\Controllers\Api\TablesControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TablesRequests\putTableRequest;
use App\Models\Table;
use Illuminate\Support\Facades\Log;

class putTableController extends Controller
{
    public function edit_table(putTableRequest $putTableRequest, $table_id)
    {
        try {
            $table = Table::find($table_id);

            if (!$table) {
                return response()->json([
                    'data' => null,
                    'success' => false,
                    'message' => 'Table not found',
                    'status' => 404
                ], 404);
            }

            $table->update($putTableRequest->validated());

            return response()->json([
                'data' => $table,
                'success' => true,
                'message' => 'Table updated successfully',
                'status' => 200
            ], 200);

        } catch (\Exception $e) {
            Log::error('Table update failed: ' . $e->getMessage());

            return response()->json([
                'data' => null,
                'success' => false,
                'message' => 'Failed to update table',
                'status' => 500
            ], 500);
        }
    }
}
