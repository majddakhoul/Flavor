<?php

namespace App\Http\Requests\Api\EmployeesRequests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateEmployeeInfoFromManagerRequest extends FormRequest
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

            'employee_id' => 'required|exists:employees,id',
            'salary' => 'sometimes|numeric|digits_between:1,20',
            'bonus' => 'sometimes|numeric|digits_between:1,11',
            'notes' => 'sometimes',
            'position' => ['sometimes', Rule::in(['Manager','Chef','Waiter','Security' , 'Delivery'])],
            ''

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
