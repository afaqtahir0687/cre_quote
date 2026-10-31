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
     * Prepare inputs before validation: normalize phone with country code,
     * and ensure backward compatibility for single company submissions.
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

        // Backward compatibility: If companies array is missing or empty, build it from legacy inputs
        $companies = $this->input('companies');
        if (!is_array($companies) || empty($companies)) {
            $compName = $this->filled('company_name') ? $this->input('company_name') : ($this->input('name') ? $this->input('name') . ' Branch' : 'Main Branch');
            $compCountryCode = $this->input('company_country_code', '+966');
            $compPhone = $this->input('company_phone');
            if ($compPhone !== null && trim($compPhone) !== '') {
                $compPhone = Customer::normalizePhone($compPhone, $compCountryCode);
            }
            $this->merge([
                'companies' => [
                    [
                        'id'           => null,
                        'company_name' => $compName,
                        'phone'        => $compPhone,
                        'country_code' => $compCountryCode,
                        'vat_no'       => $this->input('vat_no'),
                        'cr_no'        => $this->input('cr_no'),
                        'address'      => $this->input('address'),
                        'city'         => $this->input('city'),
                        'zip'          => $this->input('zip'),
                        'country'      => $this->input('country', 'Saudi Arabia'),
                    ]
                ],
                'default_company_index' => 0,
            ]);
        } else {
            // Normalize company phone numbers if provided with country_code
            $updatedCompanies = [];
            foreach ($companies as $idx => $comp) {
                if (is_array($comp)) {
                    $cPhone = $comp['phone'] ?? null;
                    $cCode = $comp['country_code'] ?? '+966';
                    if ($cPhone !== null && trim($cPhone) !== '') {
                        $comp['phone'] = Customer::normalizePhone($cPhone, $cCode);
                    }
                    $updatedCompanies[$idx] = $comp;
                }
            }
            $this->merge(['companies' => $updatedCompanies]);
        }
    }

    public function rules(): array
    {
        // Backward compatibility fallback in case prepareForValidation was not triggered
        if (!$this->has('companies') || !is_array($this->input('companies')) || empty($this->input('companies'))) {
            $compName = $this->filled('company_name') ? $this->input('company_name') : ($this->input('name') ? $this->input('name') . ' Branch' : 'Main Branch');
            $this->merge([
                'companies' => [
                    [
                        'id'           => null,
                        'company_name' => $compName,
                        'phone'        => $this->input('company_phone'),
                        'vat_no'       => $this->input('vat_no'),
                        'cr_no'        => $this->input('cr_no'),
                        'address'      => $this->input('address'),
                        'city'         => $this->input('city'),
                        'zip'          => $this->input('zip'),
                        'country'      => $this->input('country', 'Saudi Arabia'),
                    ]
                ],
                'default_company_index' => 0,
            ]);
        }

        $customerId = $this->route('customer')
            ? (is_object($this->route('customer')) ? $this->route('customer')->id : $this->route('customer'))
            : $this->id;

        return [
            'name'                   => 'required|string|max:255',
            'country_code'           => 'nullable|string|max:10',
            'email'                  => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('customers', 'email')->ignore($customerId)->whereNull('deleted_at'),
            ],
            'phone'                  => [
                'required',
                'string',
                'max:30',
                Rule::unique('customers', 'phone')->ignore($customerId)->whereNull('deleted_at'),
            ],
            'is_active'              => 'boolean',
            
            // Multi-Company Validation
            'companies'              => 'required|array|min:1',
            'companies.*.id'         => 'nullable|exists:customer_companies,id',
            'companies.*.company_name' => 'required|string|max:255',
            'companies.*.phone'      => 'nullable|string|max:50',
            'companies.*.country_code' => 'nullable|string|max:10',
            'companies.*.vat_no'     => 'nullable|string|max:50',
            'companies.*.cr_no'      => 'nullable|string|max:50',
            'companies.*.address'    => 'nullable|string',
            'companies.*.city'       => 'nullable|string|max:100',
            'companies.*.zip'        => 'nullable|string|max:20',
            'companies.*.country'    => 'nullable|string|max:100',
            'default_company_index'  => 'nullable|integer',

            // Legacy input fields (still accepted for backward compatibility)
            'company_name'           => 'nullable|string|max:255',
            'company_phone'          => 'nullable|string|max:50',
            'company_country_code'   => 'nullable|string|max:10',
            'address'                => 'nullable|string',
            'city'                   => 'nullable|string|max:100',
            'zip'                    => 'nullable|string|max:20',
            'country'                => 'nullable|string|max:100',
            'tax_id'                 => 'nullable|string|max:50',
            'vat_no'                 => 'nullable|string|max:50',
            'cr_no'                  => 'nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'Phone number is required to uniquely identify the customer.',
            'phone.unique' => 'A customer with this phone number already exists in the system.',
            'email.unique' => 'A customer with this email address already exists in the system.',
            'companies.required' => 'At least one company or branch is required for this customer.',
            'companies.min' => 'At least one company or branch is required for this customer.',
            'companies.*.company_name.required' => 'Company Name is required for each branch.',
        ];
    }
}
