<?php

namespace App\Http\Requests\Api\ReservationsRequests;

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class postReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'party_size' => 'required|integer|min:1',
            'special_requests' => 'nullable|string',
            'table_ids' => [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    $requestDate = \Carbon\Carbon::parse($this->start_time)->format('Y-m-d');

                    // تسجيل تفاصيل الطلب للتحقق
                    Log::debug('Reservation conflict check', [
                        'table_ids' => $value,
                        'start_time' => $this->start_time,
                        'end_time' => $this->end_time,
                        'requestDate' => $requestDate
                    ]);

                    $conflicts = DB::table('reservation_table')
                        ->join('reservations', 'reservation_table.reservation_id', '=', 'reservations.id')
                        ->whereIn('table_id', $value)
                        ->where('reservations.status', '!=', 'Cancelled')
                        ->whereDate('reservation_table.start_time', $requestDate) // التحقق من نفس التاريخ فقط
                        ->where(function ($query) {
                            $query->where(function ($q) {
                                $q->where('reservation_table.start_time', '<', $this->end_time)
                                  ->where('reservation_table.end_time', '>', $this->start_time);
                            });
                        })
                        ->exists();

                    // تسجيل نتيجة التحقق
                    Log::debug('Conflicts found: ' . ($conflicts ? 'Yes' : 'No'));

                    if ($conflicts) {
                        $fail('Some tables are already reserved for the requested time slot on '.$requestDate);
                    }
                }
            ],
            'table_ids.*' => 'exists:tables,id',
            'start_time' => 'required|date_format:Y-m-d H:i',
            'end_time' => 'required|date_format:Y-m-d H:i|after:start_time',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'data' => $validator->errors(),
                'status' => 422
            ], 422)
        );
    }
}
