<?php

namespace App\Http\Requests\Api\UsersRequests\CrudRequests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateAccountForUserRequest extends FormRequest
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
            'first_name' => 'sometimes|max:32|string',
            'last_name' => 'sometimes|max:32|string',
            'phone' => [
                'sometimes',
                'string',
                'min:10',
                'max:15',
                'regex:/^(09\d{8}|009639\d{8})$/',
            ],
            'gender' => ['sometimes', Rule::in(['Male', 'Female'])],
            'birth_date' => 'sometimes|date',
            'location_id' => 'sometimes|exists:locations,id'
        ];
    }
    public function messages()
    {

        return [
            'phone.regex' => 'Phone must be 09xxxxxxxx (10 digits) or 009639xxxxxxx (14 digits)',
        ];

    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
