<?php

namespace App\Http\Requests\Manage;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class MealRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:35'],
            'prep_time' => ['required', 'date_format:H:i'],
            'description' => ['nullable', 'string', 'max:255'],
            'percentage' => ['required', 'integer', 'min:0', 'max:100'],
            'is_vegetarian' => ['nullable', 'boolean'],
            'availability' => ['required', Rule::in(['available', 'unavailable'])],
            'category_id' => ['required', 'exists:categories,id'],
            'image' => ['nullable', 'image', 'max:4096'],
            'translations' => ['nullable', 'array'],
            'translations.*' => ['nullable', 'array'],
            'translations.*.*' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_vegetarian' => $this->boolean('is_vegetarian')]);
    }
}
