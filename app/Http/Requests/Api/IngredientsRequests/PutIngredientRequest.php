<?php

namespace App\Http\Requests\Api\IngredientsRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;


class PutIngredientRequest extends FormRequest
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
            'name' => 'sometimes|string|max:16',
            'unit' => 'sometimes|string|max:4',
            'stock_quantity' => 'sometimes|numeric',
            'unit_cost' => 'sometimes|numeric',
            'is_active' => 'sometimes|boolean',
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
