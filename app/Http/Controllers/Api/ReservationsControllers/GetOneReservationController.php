<?php

namespace App\Http\Controllers\Api\ReservationsControllers;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Support\Facades\Log;
use Exception;

class GetOneReservationController extends Controller
{
    public function get_one_reservation($reservation_id)
    {
        try {
            // تحميل العلاقة مع الحقول الإضافية في pivot
            $reservation = Reservation::with(['tables' => function ($query) {
                $query->select('tables.*', 'reservation_table.start_time', 'reservation_table.end_time');
            }])->find($reservation_id);

            if (!$reservation) {
                return response()->json([
                    'data' => null,
                    'success' => false,
                    'message' => 'Reservation not found',
                    'status' => 404
                ], 404);
            }

            // إعادة هيكلة البيانات لتضمين pivot
            $responseData = [
                'id' => $reservation->id,
                'reservation_code' => $reservation->reservation_code,
                'party_size' => $reservation->party_size,
                'status' => $reservation->status,
                'date' => $reservation->date,
                'special_requests' => $reservation->special_requests,
                'customer_id' => $reservation->customer_id,
                'employee_id' => $reservation->employee_id,
                'created_at' => $reservation->created_at,
                'updated_at' => $reservation->updated_at,
                'tables' => $reservation->tables->map(function ($table) {
                    return [
                        'id' => $table->id,
                        'table_number' => $table->table_number,
                        'capacity' => $table->capacity,
                        'status' => $table->status,
                        'pivot' => [
                            'start_time' => $table->pivot->start_time,
                            'end_time' => $table->pivot->end_time,
                            'created_at' => $table->pivot->created_at,
                            'updated_at' => $table->pivot->updated_at
                        ]
                    ];
                })
            ];

            return response()->json([
                'data' => $responseData,
                'success' => true,
                'message' => null,
                'status' => 200
            ], 200);

        } catch (Exception $e) {
            Log::error('Failed to fetch reservation: ' . $e->getMessage());

            return response()->json([
                'data' => null,
                'success' => false,
                'message' => 'Failed to retrieve reservation',
                'status' => 500
            ], 500);
        }
    }
}
