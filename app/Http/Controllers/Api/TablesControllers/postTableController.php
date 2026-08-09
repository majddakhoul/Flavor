<?php

namespace App\Http\Controllers\Api\TablesControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TablesRequests\postTableRequest;
use App\Models\Table;
use Illuminate\Support\Facades\Log;

class postTableController extends Controller
{
    public function add_table(postTableRequest $postTableRequest)
    {
        try {
            $table = Table::create($postTableRequest->validated());

            return response()->json([
                'data' => $table,  // Changed from null to return created table
                'success' => true,
                'message' => 'Table created successfully',
                'status' => 201,
            ], 201);

        } catch (\Exception $e) {
            Log::error('Table creation failed: ' . $e->getMessage());
            
            return response()->json([
                'data' => null,
                'success' => false,
                'message' => 'Failed to create table',
                'status' => 500,
            ], 500);
        }
    }
}