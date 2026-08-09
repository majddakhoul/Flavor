<?php

namespace App\Http\Controllers\Api\ReservationsControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ReservationsRequests\putReservationRequest;
use App\Models\Reservation;
use Illuminate\Support\Facades\Log;

class PutReservationController extends Controller
{
    public function edit_reservation(putReservationRequest $request, $reservation_id)
    {
        try {
            $validatedData = $request->validated();
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
                    'message' => 'Reservation not found',
                    'data' => null,
                ], 404);
            }

            // Authorization: only same customer or employee/manager can edit
            if (
                ($user->user_type === 'Customer' && $reservation->customer_id !== $user->customer?->id) ||
                ($user->user_type !== 'Customer' && $user->user_type !== 'Employee' && $user->user_type !== 'Manager')
            ) {
                return response()->json([
                    'success' => false,
                    'status' => 403,
                    'message' => 'You are not authorized to edit this reservation.',
                    'data' => null,
                ], 403);
            }

            if ($user->user_type === 'Customer' && $user->customer?->isBanned()) {
                return response()->json([
                    'success' => false,
                    'status' => 403,
                    'message' => 'Account is banned.',
                    'data' => null,
                ], 403);
            }

            // Update reservation fields
            $reservation->party_size = $validatedData['party_size'];
            $reservation->special_requests = $validatedData['special_requests'] ?? $reservation->special_requests;
            $reservation->status = $validatedData['status'] ?? $reservation->status;

            // Update pivot table times without changing the tables
            foreach ($reservation->tables as $table) {
                $reservation->tables()->updateExistingPivot($table->id, [
                    'start_time' => $validatedData['start_time'],
                    'end_time' => $validatedData['end_time'],
                ]);
            }

            // Update user relations and type
            if ($user->user_type === 'Employee' || $user->user_type === 'Manager') {
                $reservation->type = 'Local';
                $reservation->employee_id = $user->employee?->id;
                $reservation->customer_id = null;
            } elseif ($user->user_type === 'Customer') {
                $reservation->type = 'Application';
                $reservation->customer_id = $user->customer?->id;
                $reservation->employee_id = null;
            } else {
                return response()->json([
                    'success' => false,
                    'status' => 403,
                    'message' => 'Unauthorized user type.',
                    'data' => null,
                ], 403);
            }

            $reservation->save();

            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => 'The reservation has been updated successfully.',
                'data' => [
                    'reservation' => $reservation,
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Reservation update failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => 'Reservation update failed',
                'data' => null,
            ], 500);
        }
    }
}
