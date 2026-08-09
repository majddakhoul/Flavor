<?php

namespace App\Http\Requests\Api\OrdersRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PostOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
public function rules(): array
{
    return [
        'notes' => 'nullable|string',
        'order_type' => 'required|in:Reservation,Delivery,Takeaway',
        'location_id' => 'nullable|exists:locations,id',
        
        'reservation_id' => 'nullable|exists:reservations,id',
        
        'meals.*.id' => 'required|exists:meals,id',
        'meals.*.quantity' => 'required|integer|min:1',
        
        'offers.*.id' => 'required|exists:offers,id',
        'offers.*.quantity' => 'required|integer|min:1',
    ];
}

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $type = $this->input('order_type');
            $user = auth()->user();

            $hasMeals = $this->has('meals') && count($this->input('meals')) > 0;
            $hasOffers = $this->has('offers') && count($this->input('offers')) > 0;

            if (!$hasMeals && !$hasOffers) {
                $validator->errors()->add('meals', 'Order must include at least one meal or offer.');
            }

            if ($type === 'Reservation') {
                if (!$this->reservation_id) {
                    $validator->errors()->add('reservation_id', 'Reservation ID is required for reservation orders.');
                }
                if (!$user->employee) {
                    $validator->errors()->add('employee', 'Only employees can make reservation orders.');
                }
            }

            if ($type === 'Delivery') {
                if (!$user->customer) {
                    $validator->errors()->add('customer', 'Only customers can make delivery orders.');
                }
            }

            if ($type === 'Takeaway') {
                if (!$user->employee) {
                    $validator->errors()->add('employee', 'Only employees can make takeaway orders.');
                }
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
                'status' => 422
            ], 422)
        );

    }

}
