<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100',
            'category_id' => 'nullable|exists:categories,id',
            'unit_id' => 'nullable|exists:units,id',
            'description' => 'nullable|string',
            'purchase_price' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'initial_stock' => 'nullable|integer|min:0',
            'stock' => 'nullable|integer|min:0',
            'type' => 'required|in:product,service',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp,gif|max:10240',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,jpg,png,webp,gif|max:10240',
            'removed_images' => 'nullable|string',
        ];
    }
}
