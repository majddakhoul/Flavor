<?php

namespace App\Http\Controllers\Api\ReservationsControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ReservationsRequests\putReservationStatusRequest;
use App\Models\Reservation;
use Illuminate\Support\Facades\Log;

class PutReservationStatusController extends Controller
{
    public function edit_status(putReservationStatusRequest $request, $id)
    {
        try {
            $reservation = Reservation::find($id);
            
            if (!$reservation) {
                return response()->json([
                    'data' => null,
                    'success' => false,
                    'message' => 'Reservation not found',
                    'status' => 404
                ], 404);
            }

            $reservation->status = $request->status;
            $reservation->save();

            return response()->json([
                'data' => null,
                'success' => true,
                'message' => 'Reservation status updated successfully',
                'status' => 200
            ], 200);

        } catch (\Exception $e) {
            Log::error('Reservation status update failed: ' . $e->getMessage());
            
            return response()->json([
                'data' => null,
                'success' => false,
                'message' => 'Failed to update reservation status',
                'status' => 500
            ], 500);
        }
    }
}