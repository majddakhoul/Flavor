<?php

namespace App\Http\Requests\Manage;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class TableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'table_number' => ['required', 'string', 'max:3', Rule::unique('tables', 'table_number')->ignore($this->route('table'))],
            'capacity' => ['required', 'integer', 'min:1', 'max:40'],
            'location' => ['required', Rule::in(['Indoor', 'Outdoor', 'VIP', 'Roof'])],
            'is_active' => ['nullable', 'boolean'],
            'price_per_hour' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:255'],
            'translations' => ['nullable', 'array'],
            'translations.*' => ['nullable', 'array'],
            'translations.*.*' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active')]);
    }
}
