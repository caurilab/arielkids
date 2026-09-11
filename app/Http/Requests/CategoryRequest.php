<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->is_admin === true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $categoryId = $this->route('category')?->id;

        return [
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:160', Rule::unique('categories', 'slug')->ignore($categoryId)],
            'position' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }

    public function validatedData(): array
    {
        $data = $this->validated();

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['is_active'] = $this->boolean('is_active');
        $data['position'] = $data['position'] ?? 0;

        return $data;
    }
}
