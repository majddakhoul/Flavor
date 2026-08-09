<?php

namespace App\Http\Requests\Account;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:32'],
            'last_name' => ['required', 'string', 'max:32'],
            'phone' => ['required', 'string', 'min:10', 'max:15', Rule::unique('users', 'phone')->ignore($this->user()->id), 'regex:/^(09\\d{8}|009639\\d{8})$/'],
            'gender' => ['required', Rule::in(['Male', 'Female'])],
            'location_id' => ['nullable', 'exists:locations,id'],
            'allergies' => ['nullable', Rule::in(array_column(\App\Enums\Allergy::cases(), 'value'))],
            'favorite_categories' => ['nullable', 'string', 'max:32'],
        ];
    }
}
