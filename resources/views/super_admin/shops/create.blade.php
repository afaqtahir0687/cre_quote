@extends('admin.layouts.app')

@section('title', __('Add New Shop'))

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="fw-bold text-dark mb-0">{{ __('Add New Shop') }}</h2>
            <span class="badge bg-primary text-white rounded-pill px-3 py-1">
                {{ __('Multi-Tenant Isolation') }}
            </span>
        </div>
        <p class="text-muted small mb-0">{{ __('Create an isolated branch with dedicated business category, granular module permissions, and assign its administrator.') }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('super_admin.business_types.index') }}" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-layer-group me-1"></i> {{ __('Manage Categories') }}
        </a>
        <a href="{{ route('super_admin.shops.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Shops') }}
        </a>
    </div>
</div>

<form action="{{ route('super_admin.shops.store') }}" method="POST" enctype="multipart/form-data" id="createShopForm">
    @csrf

    <div class="row g-4">
        <!-- Left Column: Business Category, Module Permissions & Profile -->
        <div class="col-lg-7">
            <!-- 1. Industry / Business Category Selection -->
            <x-card :title="__('1. Business Category & Industry Type')" class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="text-muted small mb-0">
                        {{ __('Select the primary business category for this shop. Recommended modules and workflows will be configured automatically.') }}
                    </p>
                    <a href="{{ route('super_admin.business_types.index') }}" class="text-primary extra-small text-decoration-none fw-semibold text-nowrap ms-2" target="_blank">
                        <i class="fas fa-cog me-1"></i>{{ __('Configure Presets') }}
                    </a>
                </div>

                <div class="row g-3">
                    @php
                        $selectedType = old('business_type', 'printing_advertising');
                    @endphp
                    @foreach($businessTypes as $typeKey => $typeInfo)
                        @php
                            $isSelected = ($selectedType === $typeKey);
                        @endphp
                        <div class="col-md-6">
                            <label class="business-type-card border rounded-3 p-3 d-flex align-items-start gap-3 h-100 cursor-pointer position-relative shadow-xs transition-all {{ $isSelected ? 'border-primary bg-primary-subtle' : 'bg-white' }}"
                                   for="btype_{{ $typeKey }}" style="cursor: pointer; transition: all 0.2s ease;">
                                <input type="radio" name="business_type" id="btype_{{ $typeKey }}" value="{{ $typeKey }}" 
                                       class="form-check-input mt-1 flex-shrink-0" {{ $isSelected ? 'checked' : '' }}
                                       onchange="onBusinessTypeChange('{{ $typeKey }}')">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="{{ $typeInfo['icon'] }} text-primary"></i>
                                            <strong class="text-dark small">{{ __(app()->getLocale() === 'ar' ? $typeInfo['name_ar'] : $typeInfo['name']) }}</strong>
                                        </div>
                                    </div>
                                    <p class="text-muted extra-small mb-0" style="font-size: 0.76rem; line-height: 1.4;">
                                        {{ __($typeInfo['description']) }}
                                    </p>
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>
            </x-card>

            <!-- 2. Granular Module Permissions & Feature Toggles -->
            <x-card :title="__('2. Modular Permissions & Feature Toggles (Per Shop)')" class="mb-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <p class="text-muted small mb-0">
                        {{ __('Choose which modules and menus are active for this shop. Unchecked modules will not appear in the shop navigation or dashboard.') }}
                    </p>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary extra-small py-1 px-2" onclick="toggleAllModules(true)">
                            <i class="fas fa-check-double me-1"></i> {{ __('All') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary extra-small py-1 px-2" onclick="toggleAllModules(false)">
                            <i class="fas fa-times me-1"></i> {{ __('None') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary extra-small py-1 px-2" onclick="resetToIndustryDefaults()">
                            <i class="fas fa-undo me-1"></i> {{ __('Reset Defaults') }}
                        </button>
                    </div>
                </div>

                @php
                    $oldModules = old('enabled_modules', \App\Models\Shop::defaultModulesForType($selectedType));
                @endphp

                <div class="row g-2">
                    @foreach($allModules as $modKey => $modInfo)
                        @php
                            $isChecked = in_array($modKey, $oldModules, true);
                            $isTravel = $modKey === 'travel_tourism';
                        @endphp
                        <div class="col-md-6 col-sm-6">
                            <div class="p-2 px-3 border rounded-3 h-100 d-flex align-items-center justify-content-between gap-2 {{ $isTravel ? 'bg-light-subtle border-primary-subtle' : 'bg-white' }}" style="transition: all 0.2s ease;">
                                <div class="d-flex align-items-center gap-2 flex-grow-1 overflow-hidden">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input module-checkbox" type="checkbox" name="enabled_modules[]" 
                                               value="{{ $modKey }}" id="mod_{{ $modKey }}" {{ $isChecked ? 'checked' : '' }}>
                                    </div>
                                    <label class="form-check-label fw-semibold text-dark small text-truncate mb-0 cursor-pointer" for="mod_{{ $modKey }}" style="cursor: pointer;">
                                        <i class="{{ $modInfo['icon'] }} text-primary me-1"></i>
                                        {{ __(app()->getLocale() === 'ar' ? $modInfo['name_ar'] : $modInfo['name']) }}
                                    </label>
                                </div>
                                <span class="badge bg-light text-muted border extra-small flex-shrink-0" style="font-size: 0.68rem;">
                                    {{ ucfirst($modInfo['category']) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-card>

            <!-- 3. Branch Profile & Settings -->
            <x-card :title="__('3. Branch Profile & Identity')">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">{{ __('Shop / Branch Name *') }}</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Al-Noor Signage - Branch 2" value="{{ old('name') }}" required id="shopNameInput">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">
                            <i class="fas fa-quote-left text-info me-1"></i>{{ __('Tagline / Slogan (شعار المنشأة اللفظي)') }}
                        </label>
                        <input type="text" name="tagline" class="form-control @error('tagline') is-invalid @enderror" placeholder="{{ __('Your Trusted Partner for Umrah & Travel') }}" value="{{ old('tagline') }}">
                        @error('tagline') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small class="text-muted d-block mt-1" style="font-size: 0.74rem;">{{ __('Appears on vouchers, tickets, and official invoice headers below shop name.') }}</small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">{{ __('URL Slug') }}</label>
                        <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" placeholder="auto-generated" value="{{ old('slug') }}" id="shopSlugInput">
                        @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">{{ __('Shop Email') }}</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="branch2@example.com" value="{{ old('email') }}">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">{{ __('Shop Phone') }}</label>
                        <x-country-phone-picker id="shop_phone" name="phone" codeName="country_code" :required="false" placeholder="50 123 4567" :error="$errors->first('phone')" />
                        @error('phone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        <small class="text-muted d-block mt-1" style="font-size: 0.74rem;">{{ __('Select country code and enter phone number without leading 0.') }}</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">
                            <i class="fas fa-file-contract text-primary me-1"></i>{{ __('CR Number (السجل التجاري)') }}
                        </label>
                        <input type="text" name="cr_number" class="form-control @error('cr_number') is-invalid @enderror" placeholder="1010XXXXXX" value="{{ old('cr_number') }}">
                        @error('cr_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small class="text-muted d-block mt-1" style="font-size: 0.74rem;">{{ __('Official Commercial Registration Number.') }}</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">
                            <i class="fas fa-id-card-alt text-success me-1"></i>{{ __('License Number (رقم الترخيص / ترخيص الحج والعمرة)') }}
                        </label>
                        <input type="text" name="license_number" class="form-control @error('license_number') is-invalid @enderror" placeholder="LIC-XXXXXX" value="{{ old('license_number') }}">
                        @error('license_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small class="text-muted d-block mt-1" style="font-size: 0.74rem;">{{ __('Ministry of Hajj & Umrah or Tourism License Number.') }}</small>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold text-dark">{{ __('Address') }}</label>
                        <textarea name="address" rows="2" class="form-control @error('address') is-invalid @enderror" placeholder="Street, Commercial District, Building...">{{ old('address') }}</textarea>
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">{{ __('City') }}</label>
                        <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" placeholder="e.g. Riyadh, Jeddah, Makkah" value="{{ old('city') }}">
                        @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">{{ __('Country') }}</label>
                        <input type="text" name="country" class="form-control @error('country') is-invalid @enderror" placeholder="Saudi Arabia" value="{{ old('country', 'Saudi Arabia') }}">
                        @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">{{ __('Currency Symbol') }} *</label>
                        <input type="text" name="currency_symbol" class="form-control @error('currency_symbol') is-invalid @enderror" placeholder="$, SAR, AED, PKR" value="{{ old('currency_symbol', 'SAR') }}" required>
                        @error('currency_symbol') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">{{ __('Quote Prefix') }} *</label>
                        <input type="text" name="quote_prefix" class="form-control @error('quote_prefix') is-invalid @enderror" placeholder="QT-" value="{{ old('quote_prefix', 'QT-') }}" required>
                        @error('quote_prefix') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">{{ __('Invoice Prefix') }} *</label>
                        <input type="text" name="invoice_prefix" class="form-control @error('invoice_prefix') is-invalid @enderror" placeholder="INV-" value="{{ old('invoice_prefix', 'INV-') }}" required>
                        @error('invoice_prefix') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <div class="p-3 border rounded-3 bg-light">
                            <label class="form-label fw-bold text-dark mb-1">{{ __('Company Logo') }}</label>
                            <div class="d-flex align-items-center gap-3 mt-1">
                                <div id="createLogoPreviewBox" class="bg-white p-2 rounded border d-flex align-items-center justify-content-center" style="width: 70px; height: 70px; min-width: 70px;">
                                    <i class="fas fa-image text-muted fa-2x" id="createLogoPlaceholder"></i>
                                    <img id="createLogoPreviewImg" src="" alt="Preview" class="img-fluid d-none" style="max-height: 54px; max-width: 54px; object-fit: contain;">
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" name="logo" id="createShopLogoInput" class="form-control @error('logo') is-invalid @enderror" accept="image/*">
                                    <small class="text-muted d-block mt-1">{{ __('Recommended: Clean PNG or JPG logo under 20MB. This logo will display on the shop dashboard, sidebar, and invoices.') }}</small>
                                    @error('logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold text-dark">{{ __('Internal Notes') }}</label>
                        <textarea name="notes" rows="2" class="form-control" placeholder="Any private remarks about this branch...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Right Column: Admin User & Subscription Package -->
        <div class="col-lg-5">
            <div class="sticky-lg-top" style="top: 90px; z-index: 10;">
                <!-- Admin Account Card -->
                <x-card :title="__('4. Primary Shop Administrator')" class="mb-4">
                    <p class="text-muted small mb-3">
                        {{ __('This account will have full administrative rights restricted exclusively to this branch.') }}
                    </p>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark">{{ __('Admin Name') }} *</label>
                            <input type="text" name="admin_name" class="form-control @error('admin_name') is-invalid @enderror" placeholder="Full name of shop owner/manager" value="{{ old('admin_name') }}" required>
                            @error('admin_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold text-dark">{{ __('Username') }} *</label>
                            <input type="text" name="admin_username" class="form-control @error('admin_username') is-invalid @enderror" placeholder="Username for login" value="{{ old('admin_username') }}" required>
                            @error('admin_username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold text-dark">{{ __('Email Address') }} *</label>
                            <input type="email" name="admin_email" class="form-control @error('admin_email') is-invalid @enderror" placeholder="email@branch.com" value="{{ old('admin_email') }}" required>
                            @error('admin_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold text-dark">{{ __('Password *') }}</label>
                            <div class="input-group">
                                <input type="password" name="admin_password" id="admin_password" class="form-control @error('admin_password') is-invalid @enderror" placeholder="Min. 6 characters" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility()">
                                    <i class="fas fa-eye" id="pwdToggleIcon"></i>
                                </button>
                            </div>
                            @error('admin_password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </x-card>

                <!-- Package Selection Card -->
                <x-card :title="__('5. Package & Subscription (Cash By Hand)')">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">{{ __('Select Initial Package') }} *</label>
                        <select name="package_id" id="createPackageSelect" class="form-select" required>
                            @foreach($packages as $pkg)
                                <option value="{{ $pkg->id }}" 
                                    data-days="{{ $pkg->duration_days }}" 
                                    data-price="{{ $pkg->price }}" 
                                    data-max-shops="{{ $pkg->max_shops }}"
                                    {{ $pkg->slug === 'trial' ? 'selected' : '' }}>
                                    {{ $pkg->name }} — {{ $pkg->price == 0 ? __('Free') : number_format($pkg->price, 0) }} ({{ $pkg->duration_days }} {{ __('Days') }}, {{ $pkg->max_shops > 1 ? $pkg->max_shops . ' ' . __('Shops') : '1 ' . __('Shop') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark small">{{ __('Duration (Days)') }}</label>
                            <input type="number" name="duration_days" id="createDurationInput" class="form-control font-monospace" value="7" min="1" required>
                            <div class="form-text extra-small">{{ __('7 days for 1-Week Test Trial.') }}</div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark small">{{ __('Cash Received') }}</label>
                            <input type="number" step="0.01" name="cash_paid" id="createCashInput" class="form-control font-monospace" value="0" min="0" required>
                            <div class="form-text extra-small">{{ __('Offline Cash In Hand.') }}</div>
                        </div>
                    </div>

                    <div class="p-3 rounded-3 bg-light border border-light-subtle mt-3 mb-0 small text-muted">
                        <i class="fas fa-magic text-primary me-2"></i>
                        <strong class="text-dark">{{ __('Automated Tailored Provisioning:') }}</strong> 
                        <span id="provisioningDesc">{{ __('Selected industry defaults, initial settings and staff roles are configured automatically.') }}</span>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="{{ route('super_admin.shops.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                        <button type="submit" class="btn btn-primary-modern px-4">
                            <i class="fas fa-check-circle me-1"></i> {{ __('Create Shop & Admin') }}
                        </button>
                    </div>
                </x-card>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    const industryDefaults = @json(collect($businessTypes)->mapWithKeys(function ($item, $key) {
        return [$key => $item['default_modules'] ?? []];
    }));

    function onBusinessTypeChange(typeKey) {
        // Highlight active card
        document.querySelectorAll('.business-type-card').forEach(card => {
            card.classList.remove('border-primary', 'bg-primary-subtle');
            card.classList.add('bg-white');
        });
        const selectedRadio = document.getElementById('btype_' + typeKey);
        if (selectedRadio) {
            const parentCard = selectedRadio.closest('.business-type-card');
            if (parentCard) {
                parentCard.classList.remove('bg-white');
                parentCard.classList.add('border-primary', 'bg-primary-subtle');
            }
        }

        // Apply module defaults
        applyModuleDefaults(typeKey);
    }

    function applyModuleDefaults(typeKey) {
        const allowed = industryDefaults[typeKey] || industryDefaults.printing_advertising || [];
        document.querySelectorAll('.module-checkbox').forEach(cb => {
            cb.checked = allowed.includes(cb.value);
        });
    }

    function resetToIndustryDefaults() {
        const checkedType = document.querySelector('input[name="business_type"]:checked');
        if (checkedType) {
            applyModuleDefaults(checkedType.value);
        }
    }

    function toggleAllModules(check) {
        document.querySelectorAll('.module-checkbox').forEach(cb => {
            cb.checked = check;
        });
    }

    function togglePasswordVisibility() {
        const input = document.getElementById('admin_password');
        const icon = document.getElementById('pwdToggleIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Auto slug generator from name
    const nameInput = document.getElementById('shopNameInput');
    const slugInput = document.getElementById('shopSlugInput');
    if (nameInput && slugInput) {
        nameInput.addEventListener('input', function() {
            if (!slugInput.dataset.manual) {
                slugInput.value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
            }
        });
        slugInput.addEventListener('input', function() {
            this.dataset.manual = 'true';
        });
    }

    // Logo Preview
    const logoInput = document.getElementById('createShopLogoInput');
    const logoPreviewImg = document.getElementById('createLogoPreviewImg');
    const logoPlaceholder = document.getElementById('createLogoPlaceholder');

    if (logoInput) {
        logoInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    logoPreviewImg.src = e.target.result;
                    logoPreviewImg.classList.remove('d-none');
                    if (logoPlaceholder) logoPlaceholder.classList.add('d-none');
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Auto-fill duration and price on package change
    const pkgSelect = document.getElementById('createPackageSelect');
    const durationInput = document.getElementById('createDurationInput');
    const cashInput = document.getElementById('createCashInput');

    if (pkgSelect) {
        pkgSelect.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            if (opt) {
                if (durationInput) durationInput.value = opt.getAttribute('data-days') || 7;
                if (cashInput) cashInput.value = opt.getAttribute('data-price') || 0;
            }
        });
    }
</script>
@endpush
