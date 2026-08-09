<?php

namespace App\Http\Controllers\Api\ReservationsControllers;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Validator;

class SearchReservationController extends Controller
{
    public function search(Request $request)
{
    $validator = Validator::make($request->all(), [
        'reservation_code' => 'required|string|max:50',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation error',
            'errors' => $validator->errors(),
            'status' => 422
        ], 422);
    }

    $reservations = Reservation::where('reservation_code', 'LIKE', "%{$request->reservation_code}%")->get();

    return response()->json([
        'success' => true,
        'message' => 'Reservations retrieved successfully',
        'data' => $reservations,
        'status' => 200
    ]);
}
}
