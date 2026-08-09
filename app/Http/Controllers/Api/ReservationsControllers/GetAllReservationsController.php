<?php

namespace App\Http\Controllers\Api\ReservationsControllers;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Support\Facades\Log;

class GetAllReservationsController extends Controller
{
    public function get_all_reservations()
    {
        try {
            $reservations = Reservation::with('tables')->get();

            $reservations->each(function ($reservation) {
                $reservation->tables->each->makeHidden('pivot');
            });
            
            if ($reservations->isNotEmpty()) {
                return response()->json([
                    'data' => $reservations,
                    'success' => true,
                    'message' => null,
                    'status' => 200
                ], 200);
            }

            return response()->json([
                'data' => null,
                'success' => true,
                'message' => 'There is nothing to display',
                'status' => 200
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to fetch reservations: ' . $e->getMessage());
            
            return response()->json([
                'data' => null,
                'success' => false,
                'message' => 'Failed to retrieve reservations',
                'status' => 500
            ], 500);
        }
    }
}