<?php

namespace App\Http\Requests\Api\CustomersRequests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class UpdateInfoCustomerRequest extends FormRequest
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

            'favorite_categories' => 'sometimes|string',
            'allergies' => ['sometimes', Rule::in([
                'Cows Milk Allergy','Egg Allergy','Peanut Allergy','Tree Nut Allergy','Fish Allergy',
                'hellfish Allergy','Wheat Allergy','Soy Allergy','Seed Allergies','Red Meat Allergy',
                'Fruit Allergies','Vegetable Allergies','Spice Allergies'
                ])],
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
