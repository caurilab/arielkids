<?php

namespace App\Http\Requests;

use App\Enums\Audience;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->is_admin === true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:190'],
            'slug' => ['nullable', 'string', 'max:190', Rule::unique('products', 'slug')->ignore($productId)],
            'description' => ['nullable', 'string', 'max:5000'],
            'price' => ['required', 'integer', 'min:0', 'max:100000000'],
            'audience' => ['required', Rule::enum(Audience::class)],
            'sku' => ['nullable', 'string', 'max:60'],
            'stock' => ['required', 'integer', 'min:0', 'max:100000'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'images' => ['nullable', 'array', 'max:8'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'delete_media' => ['nullable', 'array'],
            'delete_media.*' => ['integer', 'exists:media,id'],
        ];
    }

    /** Données validées prêtes pour Product::create()/update(). */
    public function validatedData(): array
    {
        $data = $this->safe()->except(['images', 'delete_media']);

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['is_active'] = $this->boolean('is_active');
        $data['is_featured'] = $this->boolean('is_featured');

        return $data;
    }
}
