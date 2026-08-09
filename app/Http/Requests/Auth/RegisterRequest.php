<?php

namespace App\Http\Requests\Auth;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'email' => ['required', 'string', 'email', 'max:64', 'unique:users,email'],
            'phone' => ['required', 'string', 'min:10', 'max:15', 'unique:users,phone', 'regex:/^(09\\d{8}|009639\\d{8})$/'],
            'gender' => ['required', Rule::in(['Male', 'Female'])],
            'location_id' => ['nullable', 'exists:locations,id'],
            'password' => ['required', 'string', 'min:8', 'max:32', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return ['phone.regex' => __('validation.custom.phone.regex')];
    }
}
