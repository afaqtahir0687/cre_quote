<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Customer;

class CustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare inputs before validation: normalize phone with country code.
     */
    protected function prepareForValidation(): void
    {
        $countryCode = $this->input('country_code', '+966');
        $rawPhone = $this->input('phone');

        if ($rawPhone !== null && trim($rawPhone) !== '') {
            $normalized = Customer::normalizePhone($rawPhone, $countryCode);
            $this->merge([
                'phone' => $normalized,
            ]);
        }
    }

    public function rules(): array
    {
        $customerId = $this->route('customer')
            ? (is_object($this->route('customer')) ? $this->route('customer')->id : $this->route('customer'))
            : $this->id;

        return [
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'country_code' => 'nullable|string|max:10',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('customers', 'email')->ignore($customerId)->whereNull('deleted_at'),
            ],
            'phone' => [
                'required',
                'string',
                'max:30',
                Rule::unique('customers', 'phone')->ignore($customerId)->whereNull('deleted_at'),
            ],
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'zip' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'tax_id' => 'nullable|string|max:50',
            'vat_no' => 'nullable|string|max:50',
            'cr_no' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'Phone number is required to uniquely identify the customer.',
            'phone.unique' => 'A customer with this phone number already exists in the system.',
            'email.unique' => 'A customer with this email address already exists in the system.',
        ];
    }
}
