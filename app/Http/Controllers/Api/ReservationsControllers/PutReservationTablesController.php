<?php

namespace App\Http\Controllers\Api\ReservationsControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ReservationsRequests\putReservationTablesRequest;
use App\Models\Reservation;
use Illuminate\Support\Facades\Log;

class PutReservationTablesController extends Controller
{
    public function updateTables(putReservationTablesRequest $request, $reservation_id)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'status' => 401,
                    'message' => 'Unauthenticated.',
                    'data' => null,
                ], 401);
            }

            $reservation = Reservation::find($reservation_id);
            if (!$reservation) {
                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'message' => 'Reservation not found.',
                    'data' => null,
                ], 404);
            }

            // Authorization: Only same customer or employee/manager can update
            if ($user->user_type === 'Customer' && $user->customer?->id !== $reservation->customer_id) {
                return response()->json([
                    'success' => false,
                    'status' => 403,
                    'message' => 'Unauthorized action for customer.',
                    'data' => null,
                ], 403);
            }

            if (in_array($user->user_type, ['Employee', 'Manager']) && $user->employee?->id !== $reservation->employee_id) {
                return response()->json([
                    'success' => false,
                    'status' => 403,
                    'message' => 'Unauthorized action for employee/manager.',
                    'data' => null,
                ], 403);
            }

            // Prepare pivot data for sync
            $pivotData = [];
            foreach ($request->table_ids as $tableId) {
                $pivotData[$tableId] = [
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                ];
            }

            // Sync tables with new ones + pivot data
            $reservation->tables()->sync($pivotData);

            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => 'Reservation tables updated successfully.',
                'data' => [
                    'reservation' => $reservation->fresh()->load('tables'),
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to update reservation tables: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => 'Failed to update reservation tables.',
                'data' => null,
            ], 500);
        }
    }
    
        public function detachAllTables($reservation_id)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'status' => 401,
                    'message' => 'Unauthenticated.',
                    'data' => null,
                ], 401);
            }

            $reservation = Reservation::find($reservation_id);
            if (!$reservation) {
                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'message' => 'Reservation not found.',
                    'data' => null,
                ], 404);
            }

            // Authorization: Only same customer or employee/manager can detach tables
            if ($user->user_type === 'Customer' && $user->customer?->id !== $reservation->customer_id) {
                return response()->json([
                    'success' => false,
                    'status' => 403,
                    'message' => 'Unauthorized action for customer.',
                    'data' => null,
                ], 403);
            }

            if (in_array($user->user_type, ['Employee', 'Manager']) && $user->employee?->id !== $reservation->employee_id) {
                return response()->json([
                    'success' => false,
                    'status' => 403,
                    'message' => 'Unauthorized action for employee/manager.',
                    'data' => null,
                ], 403);
            }

            // Detach all tables from the reservation
            $reservation->tables()->detach();

            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => 'All tables detached from the reservation successfully.',
                'data' => null,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to detach tables from reservation: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => 'Failed to detach tables from reservation.',
                'data' => null,
            ], 500);
        }
    }
}
