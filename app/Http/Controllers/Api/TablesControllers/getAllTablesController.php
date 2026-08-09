<?php

namespace App\Http\Controllers\Api\TablesControllers;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Support\Facades\Log;

class getAllTablesController extends Controller
{
    public function get_all_tables()  
    {
        try {
            $tables = Table::all();

            if ($tables->isNotEmpty()) {
                return response()->json([
                    'data' => $tables,
                    'success' => true,
                    'message' => null,
                    'status' => 200
                ], 200);
            }

            return response()->json([
                'data' => null,
                'success' => true,
                'message' => 'There are no tables to display',
                'status' => 200
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to fetch tables: ' . $e->getMessage());
            
            return response()->json([
                'data' => null,
                'success' => false,
                'message' => 'Failed to retrieve tables',
                'status' => 500
            ], 500);
        }
    }
}