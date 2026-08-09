<?php

namespace App\Http\Controllers\Api\TablesControllers;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Support\Facades\Log;

class deleteTableController extends Controller
{
    public function delete_table($table_id) 
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

            $table->reservations()->detach();
            $table->delete();

            return response()->json([
                'data' => null,
                'success' => true,
                'message' => 'Table deleted successfully',
                'status' => 200
            ], 200);

        } catch (\Exception $e) {
            Log::error('Table deletion failed: ' . $e->getMessage());
            
            return response()->json([
                'data' => null,
                'success' => false,
                'message' => 'Failed to delete table',
                'status' => 500
            ], 500);
        }
    }
}