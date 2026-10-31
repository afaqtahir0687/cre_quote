<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuoteRequest extends FormRequest
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
            'quote_number' => $this->isMethod('post') ? 'nullable|string' : 'required|string|unique:quotes,quote_number,' . ($this->quote->id ?? ''),
            'customer_id' => 'required|exists:customers,id',
            'customer_company_id' => 'nullable|exists:customer_companies,id',
            'employee_id' => 'nullable|exists:employees,id',
            'show_customer_name' => 'nullable|boolean',
            'quote_date' => 'required|date',
            'due_date' => 'nullable|date',
            'reference' => 'nullable|string',
            'status' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.item_text' => 'nullable|string',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.tax_percentage' => 'nullable|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.total' => 'required|numeric',
            'items.*.existing_images' => 'nullable|string',
            'items.*.removed_images' => 'nullable|string',
            'items.*.image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:20480',
            'items.*.images.*' => 'image|mimes:jpeg,jpg,png,gif|max:20480',
            'subtotal' => 'required|numeric',
            'total_tax' => 'required|numeric',
            'total_discount' => 'required|numeric',
            'shipping' => 'nullable|numeric|min:0',
            'grand_total' => 'required|numeric',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,jpg,png,gif|max:20480',
        ];
    }
}
