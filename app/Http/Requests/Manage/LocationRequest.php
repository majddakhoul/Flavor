<?php

namespace App\Http\Requests\Manage;

use Illuminate\Foundation\Http\FormRequest;

class LocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'country' => ['required', 'string', 'max:64'],
            'region' => ['required', 'string', 'max:64'],
            'state' => ['required', 'string', 'max:64'],
            'city' => ['required', 'string', 'max:64'],
            'street' => ['nullable', 'string', 'max:64'],
            'delivery_time' => ['required', 'date_format:H:i'],
        ];
    }
}
