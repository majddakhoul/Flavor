<?php

namespace App\Http\Requests\Api\TablesRequests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class putTableRequest extends FormRequest
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
            "table_number" => "sometimes|integer",
            "capacity" => "sometimes|integer",
            "location" => "sometimes",
            "price_per_hour" => "sometimes|integer|gte:0",
            "description" => "sometimes|string|max:30",
            "is_active" => "sometimes|boolean"
        ];
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
