<?php

namespace App\Http\Requests\Api\LocationsRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PostLocationRequest extends FormRequest
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
            'country' => 'required|max:64|string',
            'state' => 'required|max:64|string',
            'city' => 'required|max:64|string',
            'region' => 'required|max:64|string',
            'street' => 'sometimes|max:64',
            'delivery_time' => 'required|date_format:H:i'
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
