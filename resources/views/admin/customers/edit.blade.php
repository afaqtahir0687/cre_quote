@extends('admin.layouts.app')

@section('title', 'Edit Customer')

@section('content')
<style>
.company-card {
    transition: all 0.2s ease-in-out;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
}
.company-card.is-default-card {
    border: 2px solid #2563eb !important;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.08) !important;
}
.company-card.is-default-card .card-header {
    background-color: #f0f7ff !important;
    border-bottom: 1.5px solid #dbeafe !important;
}
.cursor-pointer {
    cursor: pointer;
}
</style>

<div class="d-flex align-items-center mb-4">
    <a href="{{ route('customers.index') }}" class="btn btn-light btn-sm rounded-circle shadow-sm me-3" style="width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center;" title="Back to Customers">
        <i class="fas fa-arrow-left text-muted"></i>
    </a>
    <div>
        <h3 class="fw-bold text-dark mb-0">Edit Customer</h3>
        <p class="text-muted small mb-0">Update customer profile and manage multiple branch or company details</p>
    </div>
</div>

<form action="{{ route('customers.update', $customer) }}" method="POST" id="customer-form" novalidate>
    @csrf
    @method('PUT')

    <!-- SECTION 1: Primary Contact Information -->
    <div class="card shadow-sm border-0 mb-4 rounded-3">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="fw-bold text-dark mb-0">
                <i class="fas fa-user-circle text-primary me-2"></i> Section 1: Primary Contact Information
            </h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-dark">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $customer->name) }}"  required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                @php
                    $parsedPhone = \App\Models\Customer::parsePhone($customer->phone ?? '');
                    $currentCode = old('country_code', $parsedPhone['country_code']);
                    $currentIso = $parsedPhone['iso'];
                    $currentNumber = old('phone', $parsedPhone['number']);
                @endphp
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-dark">Phone Number <span class="text-danger">*</span></label>
                    <x-country-phone-picker id="customer_phone" 
                                            name="phone" 
                                            codeName="country_code" 
                                            :value="$currentNumber" 
                                            :defaultCode="$currentCode" 
                                            :defaultIso="$currentIso" 
                                            :error="$errors->first('phone')" />
                    @error('phone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    <small class="text-muted d-block mt-1">Select country code and enter mobile number without leading 0.</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-dark">Email Address</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $customer->email) }}" >
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-dark">Status</label>
                    <select name="is_active" class="form-select">
                        <option value="1" {{ old('is_active', $customer->is_active) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active', $customer->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION 2: Companies & Branches (Dynamic Repeater) -->
    <div class="card shadow-sm border-0 mb-4 rounded-3">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="fas fa-building text-primary me-2"></i> Section 2: Companies & Branches
                </h5>
                <span class="badge bg-light text-primary border ms-2 px-2 py-1" id="companies-count-badge">
                    <i class="fas fa-layer-group me-1"></i>{{ count($companiesList) }} {{ count($companiesList) === 1 ? 'Company' : 'Companies' }}
                </span>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" id="add-company-btn">
                <i class="fas fa-plus me-2"></i> Add Another Company
            </button>
        </div>
        <div class="card-body p-4">
            @error('companies')
                <div class="alert alert-danger mb-3 py-2"><i class="fas fa-exclamation-triangle me-2"></i>{{ $message }}</div>
            @enderror

            @php
                if (old('companies') !== null) {
                    $companiesList = old('companies');
                    $defaultIndex = (int) old('default_company_index', 0);
                } else {
                    $companiesList = [];
                    $dbCompanies = $customer->companies()->orderBy('id')->get();
                    if ($dbCompanies->isNotEmpty()) {
                        foreach ($dbCompanies as $i => $dbComp) {
                            $companiesList[] = [
                                'id' => $dbComp->id,
                                'company_name' => $dbComp->company_name,
                                'phone' => $dbComp->phone,
                                'vat_no' => $dbComp->vat_no,
                                'cr_no' => $dbComp->cr_no,
                                'address' => $dbComp->address,
                                'city' => $dbComp->city,
                                'zip' => $dbComp->zip,
                                'country' => $dbComp->country,
                                'is_default' => (bool) $dbComp->is_default,
                            ];
                        }
                    } else {
                        $companiesList[] = [
                            'id' => null,
                            'company_name' => $customer->company_name ?? ($customer->name . ' Branch'),
                            'phone' => '',
                            'vat_no' => $customer->vat_no ?? '',
                            'cr_no' => $customer->cr_no ?? '',
                            'address' => $customer->address ?? '',
                            'city' => $customer->city ?? '',
                            'zip' => $customer->zip ?? '',
                            'country' => $customer->country ?? 'Saudi Arabia',
                            'is_default' => true,
                        ];
                    }

                    $defaultIndex = 0;
                    foreach ($companiesList as $idx => $compItem) {
                        if (!empty($compItem['is_default'])) {
                            $defaultIndex = $idx;
                            break;
                        }
                    }
                }
            @endphp

            <div id="companies-container" class="d-flex flex-column gap-3">
                @foreach($companiesList as $index => $comp)
                    @php $isDefault = ($index === $defaultIndex); @endphp
                    <div class="card company-card {{ $isDefault ? 'is-default-card' : '' }}" data-index="{{ $index }}">
                        <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center bg-light">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-building text-muted"></i>
                                <span class="fw-bold text-dark company-card-title">{{ !empty($comp['company_name']) ? $comp['company_name'] : 'Company #' . ($index + 1) }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="form-check form-check-inline m-0">
                                    <input class="form-check-input default-company-radio cursor-pointer" type="radio" 
                                           name="default_company_index" 
                                           id="default_company_{{ $index }}" 
                                           value="{{ $index }}" 
                                           {{ $isDefault ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold text-primary cursor-pointer small" for="default_company_{{ $index }}">
                                        <i class="fas fa-star text-warning me-1"></i>Primary / Default
                                    </label>
                                </div>
                                <button type="button" class="btn btn-outline-danger btn-sm btn-remove-company py-1 px-2" title="Remove Company" {{ count($companiesList) <= 1 ? 'disabled' : '' }}>
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <input type="hidden" name="companies[{{ $index }}][id]" value="{{ $comp['id'] ?? '' }}">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-dark">Company Name <span class="text-danger">*</span></label>
                                    <input type="text" name="companies[{{ $index }}][company_name]" 
                                           class="form-control form-control-sm company-name-input @error('companies.'.$index.'.company_name') is-invalid @enderror" 
                                           value="{{ $comp['company_name'] ?? '' }}" 
                                            required>
                                    @error('companies.'.$index.'.company_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-dark">Company Phone</label>
                                    @php
                                        $cPhoneParsed = \App\Models\Customer::parsePhone($comp['phone'] ?? '');
                                        $compPickerId = 'comp_phone_' . $index;
                                        $compCode = old('companies.'.$index.'.country_code', $comp['country_code'] ?? $cPhoneParsed['country_code']);
                                        $compIso = $cPhoneParsed['iso'];
                                        $compNumber = old('companies.'.$index.'.phone', $cPhoneParsed['number']);
                                    @endphp
                                    <x-country-phone-picker :id="$compPickerId" 
                                                            name="companies[{{ $index }}][phone]" 
                                                            codeName="companies[{{ $index }}][country_code]" 
                                                            :value="$compNumber" 
                                                            :defaultCode="$compCode" 
                                                            :defaultIso="$compIso" 
                                                            :required="false" 
                                                            placeholder="" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-dark">VAT Number</label>
                                    <input type="text" name="companies[{{ $index }}][vat_no]" 
                                           class="form-control form-control-sm" 
                                           value="{{ $comp['vat_no'] ?? '' }}" 
                                           >
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-dark">CR Number</label>
                                    <input type="text" name="companies[{{ $index }}][cr_no]" 
                                           class="form-control form-control-sm" 
                                           value="{{ $comp['cr_no'] ?? '' }}" 
                                           >
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label small fw-semibold text-dark">Address</label>
                                    <input type="text" name="companies[{{ $index }}][address]" 
                                           class="form-control form-control-sm" 
                                           value="{{ $comp['address'] ?? '' }}" 
                                           >
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-dark">City</label>
                                    <input type="text" name="companies[{{ $index }}][city]" 
                                           class="form-control form-control-sm" 
                                           value="{{ $comp['city'] ?? '' }}" 
                                           >
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-dark">ZIP Code</label>
                                    <input type="text" name="companies[{{ $index }}][zip]" 
                                           class="form-control form-control-sm" 
                                           value="{{ $comp['zip'] ?? '' }}" 
                                           >
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-dark">Country</label>
                                    <input type="text" name="companies[{{ $index }}][country]" 
                                           class="form-control form-control-sm" 
                                           value="{{ $comp['country'] ?? 'Saudi Arabia' }}" 
                                           >
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- FORM ACTIONS -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <a href="{{ route('customers.index') }}" class="btn btn-link text-muted text-decoration-none">
            <i class="fas fa-times me-1"></i> Cancel and Go Back
        </a>
        <button type="submit" class="btn btn-primary-modern py-2 px-4 shadow-sm" id="saveCustomerBtn">
            <i class="fas fa-save me-2"></i> Save / Update Customer
        </button>
    </div>
</form>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('companies-container');
    const addBtn = document.getElementById('add-company-btn');
    const badge = document.getElementById('companies-count-badge');
    let nextIndex = container.querySelectorAll('.company-card').length;

    function updateBadgeAndButtons() {
        const cards = container.querySelectorAll('.company-card');
        const count = cards.length;
        if (badge) {
            badge.innerHTML = `<i class="fas fa-layer-group me-1"></i>${count} ${count === 1 ? 'Company' : 'Companies'}`;
        }
        cards.forEach(card => {
            const removeBtn = card.querySelector('.btn-remove-company');
            if (removeBtn) {
                removeBtn.disabled = (count <= 1);
            }
        });
    }

    function attachCardEvents(card) {
        const nameInput = card.querySelector('.company-name-input');
        const titleSpan = card.querySelector('.company-card-title');
        const radio = card.querySelector('.default-company-radio');
        const removeBtn = card.querySelector('.btn-remove-company');

        // Live title update
        if (nameInput && titleSpan) {
            nameInput.addEventListener('input', function() {
                const val = this.value.trim();
                const idx = parseInt(card.dataset.index) + 1;
                titleSpan.textContent = val ? val : `Company #${idx}`;
            });
        }

        // Radio highlight
        if (radio) {
            radio.addEventListener('change', function() {
                if (this.checked) {
                    container.querySelectorAll('.company-card').forEach(c => c.classList.remove('is-default-card'));
                    card.classList.add('is-default-card');
                }
            });
        }

        // Remove card
        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                const cards = container.querySelectorAll('.company-card');
                if (cards.length <= 1) return;

                const wasDefault = radio && radio.checked;
                card.remove();

                // If removed card was default, mark first remaining card as default
                if (wasDefault) {
                    const firstRemaining = container.querySelector('.company-card');
                    if (firstRemaining) {
                        const firstRadio = firstRemaining.querySelector('.default-company-radio');
                        if (firstRadio) {
                            firstRadio.checked = true;
                            firstRemaining.classList.add('is-default-card');
                        }
                    }
                }

                updateBadgeAndButtons();
            });
        }
    }

    // Attach to existing cards
    container.querySelectorAll('.company-card').forEach(attachCardEvents);
    updateBadgeAndButtons();

    // Add company
    addBtn.addEventListener('click', function() {
        const idx = nextIndex++;
        const cardHtml = `
            <div class="card company-card" data-index="${idx}">
                <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center bg-light">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-building text-muted"></i>
                        <span class="fw-bold text-dark company-card-title">Company #${idx + 1}</span>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="form-check form-check-inline m-0">
                            <input class="form-check-input default-company-radio cursor-pointer" type="radio" 
                                   name="default_company_index" 
                                   id="default_company_${idx}" 
                                   value="${idx}">
                            <label class="form-check-label fw-semibold text-primary cursor-pointer small" for="default_company_${idx}">
                                <i class="fas fa-star text-warning me-1"></i>Primary / Default
                            </label>
                        </div>
                        <button type="button" class="btn btn-outline-danger btn-sm btn-remove-company py-1 px-2" title="Remove Company">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-3">
                    <input type="hidden" name="companies[${idx}][id]" value="">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-dark">Company Name <span class="text-danger">*</span></label>
                            <input type="text" name="companies[${idx}][company_name]" 
                                   class="form-control form-control-sm company-name-input" 
                                   value="" 
                                    required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-dark">Company Phone</label>
                            <div class="country-phone-group" id="comp_phone_${idx}_group">
                                <div class="dropdown">
                                    <button class="country-picker-btn dropdown-toggle" 
                                            type="button" 
                                            id="comp_phone_${idx}_btn" 
                                            data-bs-toggle="dropdown" 
                                            data-bs-auto-close="outside"
                                            aria-expanded="false">
                                        <span class="fi fi-sa" id="comp_phone_${idx}_flag"></span>
                                        <span class="country-code-text fw-semibold ms-1" id="comp_phone_${idx}_code_text">+966</span>
                                    </button>
                                    <div class="dropdown-menu country-picker-menu p-2 shadow-lg" aria-labelledby="comp_phone_${idx}_btn">
                                        <div class="country-search-wrap">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                                                <input type="text" class="form-control border-start-0 country-search-input" id="comp_phone_${idx}_search" placeholder="Search country or code..." autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="country-list-scroll" id="comp_phone_${idx}_list">
                                            @foreach(\App\Models\Customer::allCountries() as $iso => $c)
                                                <div class="country-item {{ $iso === 'sa' ? 'active' : '' }}" 
                                                     data-iso="{{ $iso }}" 
                                                     data-code="{{ $c['code'] }}" 
                                                     data-name="{{ $c['name'] }}">
                                                    <span class="fi fi-{{ $iso }}"></span>
                                                    <span class="c-name">{{ $c['name'] }}</span>
                                                    <span class="c-code">{{ $c['code'] }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="companies[${idx}][country_code]" id="comp_phone_${idx}_code" value="+966">
                                <input type="tel" 
                                       name="companies[${idx}][phone]" 
                                       id="comp_phone_${idx}_input" 
                                       class="form-control phone-input" 
                                       value="" 
                                       autocomplete="tel">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-dark">VAT Number</label>
                            <input type="text" name="companies[${idx}][vat_no]" 
                                   class="form-control form-control-sm" 
                                   value="" 
                                   >
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-dark">CR Number</label>
                            <input type="text" name="companies[${idx}][cr_no]" 
                                   class="form-control form-control-sm" 
                                   value="" 
                                   >
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-semibold text-dark">Address</label>
                            <input type="text" name="companies[${idx}][address]" 
                                   class="form-control form-control-sm" 
                                   value="" 
                                   >
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-dark">City</label>
                            <input type="text" name="companies[${idx}][city]" 
                                   class="form-control form-control-sm" 
                                   value="" 
                                   >
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-dark">ZIP Code</label>
                            <input type="text" name="companies[${idx}][zip]" 
                                   class="form-control form-control-sm" 
                                   value="" 
                                   >
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-dark">Country</label>
                            <input type="text" name="companies[${idx}][country]" 
                                   class="form-control form-control-sm" 
                                   value="Saudi Arabia" 
                                   >
                        </div>
                    </div>
                </div>
            </div>
        `;
        const temp = document.createElement('div');
        temp.innerHTML = cardHtml.trim();
        const newCard = temp.firstElementChild;
        container.appendChild(newCard);
        attachCardEvents(newCard);
        updateBadgeAndButtons();
        if (typeof window.initCountryPhonePicker === 'function') {
            window.initCountryPhonePicker(`comp_phone_${idx}_group`);
        }

        // Focus the new company name field
        const inputToFocus = newCard.querySelector('.company-name-input');
        if (inputToFocus) inputToFocus.focus();
    });

    // Form submit loading
    const form = document.getElementById('customer-form');
    if (form) {
        form.addEventListener('submit', function() {
            const btn = document.getElementById('saveCustomerBtn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';
            }
        });
    }
});
</script>
@endsection