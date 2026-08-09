<?php

namespace App\Http\Requests\Manage;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ReservationStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['Pending', 'Confirmed', 'Cancelled', 'Completed'])],
        ];
    }
}
