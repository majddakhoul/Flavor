<?php

namespace App\Http\Requests\Api\EmployeesRequests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PostOneEmployeeRequest extends FormRequest
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
            'birth_date' => 'required|date',
            'location_id' => 'required|exists:locations,id',
            'gender' => ['required', Rule::in(['Male', 'Female'])],
            'national_id' => 'required|unique:employees,national_id|numeric|min:11',
            'salary' => 'required|numeric|digits_between:1,20',
            'bonus' => 'sometimes|numeric|digits_between:1,10',
            'hire_date' => 'required|date',
            'position' => ['required', Rule::in(['Manager','Chef','Waiter','Security' , 'Delivery'])],
            'notes' => 'sometimes'

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
