<?php

namespace App\Http\Requests\Api\UsersRequests\AuthRequests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterCustomerOrManagerRequest extends FormRequest
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
            'first_name' => 'required|max:32|string',
            'last_name' => 'required|max:32|string',
            'email' => 'required|max:64|email|unique:users,email',
            'phone' => [
                'required',
                'string',
                'min:10',
                'max:15',
                'regex:/^(09\d{8}|009639\d{8})$/',
                'unique:users,phone'
            ],
            'gender' => ['required', Rule::in(['Male', 'Female'])],
            'password' => 'required|min:8|max:32|confirmed'
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
                'data' => $validator->errors(),
                'status' =>422
            ], 422)
        );

    }

}
