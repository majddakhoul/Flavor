<?php

namespace App\Http\Requests\Api\OrdersRequests;
use Illuminate\Contracts\Validation\Validator;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PutOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'notes' => 'nullable|string',
            'order_type' => 'sometimes|in:Reservation,Delivery,Takeaway',
            'status' => 'sometimes|in:Pending,Confirmed,Cancelled',

            'reservation_id' => 'nullable|exists:reservations,id',

            'meals.*.id' => 'sometimes|exists:meals,id',
            'meals.*.quantity' => 'sometimes|integer|min:1',

            'offers.*.id' => 'sometimes|exists:offers,id',
            'offers.*.quantity' => 'sometimes|integer|min:1',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $type = $this->input('order_type');

            $hasMeals = $this->has('meals') && count($this->input('meals')) > 0;
            $hasOffers = $this->has('offers') && count($this->input('offers')) > 0;

            if (!$hasMeals && !$hasOffers) {
                $validator->errors()->add('meals', 'Order must include at least one meal or offer.');
            }

            if ($type === 'Reservation' && !$this->reservation_id) {
                $validator->errors()->add('reservation_id', 'Reservation ID is required for reservation orders.');
            }


        });
    }
    protected function failedValidation(Validator $validator)
    {

        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'data' => $validator->errors(),
                'status' =>422
            ], 422)
        );

    }
}
