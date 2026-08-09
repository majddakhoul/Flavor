<?php

namespace App\Http\Requests\Manage;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class OrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['Pending', 'Confirmed', 'Completed', 'Cancelled'])],
        ];
    }
}
