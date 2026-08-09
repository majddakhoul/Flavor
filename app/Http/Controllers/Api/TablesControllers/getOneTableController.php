<?php
namespace App\Http\Controllers\Api\TablesControllers;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Carbon\Carbon;

class getOneTableController extends Controller
{
    public function get_one_table($table_id)
    {
        $table = Table::with('reservations')->find($table_id);

        if (!$table) {
            return response()->json([
                'data' => null,
                'success' => false,
                'message' => 'Table not found',
                'status' => 404
            ], 404);
        }

        // Filter reservations after fetching
        $table->reservations = $table->reservations->filter(function($reservation) {
            return $reservation->date > Carbon::now();
        })->values();

        return response()->json([
            'data' => $table,
            'success' => true,
            'message' => 'Table retrieved successfully',
            'status' => 200
        ], 200);
    }
}