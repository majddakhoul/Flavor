<?php

namespace App\Http\Controllers\Api\ReservationsControllers;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Support\Facades\Log;

class GetAllCustomerReservationsController extends Controller
{
    public function get_all_reservations()
    {
        try {
            $user = auth()->user();

            if (!$user || !$user->customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only customers can view their reservations',
                    'data' => null,
                    'status' => 403
                ], 403);
            }

            $reservations = Reservation::with('tables')
                ->where('customer_id', $user->customer->id)
                ->get();

            $reservations->each(function ($reservation) {
                $reservation->tables->each->makeHidden('pivot');
            });

            if ($reservations->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'There is nothing to display',
                    'data' => null,
                    'status' => 200
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => null,
                'data' => $reservations,
                'status' => 200
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to fetch reservations: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve reservations',
                'data' => null,
                'status' => 500
            ], 500);
        }
    }
}