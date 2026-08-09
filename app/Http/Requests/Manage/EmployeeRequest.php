<?php

namespace App\Http\Requests\Manage;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
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
            'email' => ['required', 'string', 'email', 'max:64', Rule::unique('users', 'email')->ignore($this->route('employee')?->user_id)],
            'phone' => ['required', 'string', 'min:10', 'max:15', Rule::unique('users', 'phone')->ignore($this->route('employee')?->user_id), 'regex:/^(09\\d{8}|009639\\d{8})$/'],
            'gender' => ['required', Rule::in(['Male', 'Female'])],
            'location_id' => ['nullable', 'exists:locations,id'],
            'national_id' => ['required', 'string', 'min:6', 'max:20', Rule::unique('employees', 'national_id')->ignore($this->route('employee'))],
            'position' => ['required', Rule::in(['Manager', 'Chef', 'Waiter', 'Security', 'Delivery'])],
            'salary' => ['required', 'integer', 'min:0'],
            'bonus' => ['nullable', 'integer', 'min:0'],
            'hire_date' => ['required', 'date'],
            'birth_date' => ['required', 'date', 'before:today'],
            'notes' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'max:32'],
        ];
    }

    public function messages(): array
    {
        return ['phone.regex' => __('validation.custom.phone.regex')];
    }
}
