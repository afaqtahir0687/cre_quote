@extends('admin.layouts.app')

@section('title', __('Add Business Category & Industry'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="fas fa-plus-circle text-primary me-2"></i>{{ __('Add Business Category & Industry') }}
        </h2>
        <p class="text-muted small mb-0">{{ __('Define a new industry preset with default modular permissions and starter departments for newly created shops.') }}</p>
    </div>
    <a href="{{ route('super_admin.business_types.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Categories') }}
    </a>
</div>

<form action="{{ route('super_admin.business_types.store') }}" method="POST" id="createBusinessTypeForm">
    @csrf

    <div class="row g-4">
        <!-- Left: Category Identity & Descriptions -->
        <div class="col-lg-7">
            <x-card :title="__('1. Category Identity & Branding')">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">{{ __('Industry Name (English) *') }}</label>
                        <input type="text" name="name" id="nameInput" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="e.g. Real Estate Agency">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">{{ __('Industry Name (Arabic) / الاسم بالعربية') }}</label>
                        <input type="text" name="name_ar" class="form-control font-arabic @error('name_ar') is-invalid @enderror" value="{{ old('name_ar') }}" dir="rtl" placeholder="مثال: وكالة العقارات والتطوير">
                        @error('name_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">{{ __('Unique Identifier / Code Slug') }}</label>
                        <input type="text" name="slug" id="slugInput" class="form-control font-monospace @error('slug') is-invalid @enderror" value="{{ old('slug') }}" placeholder="e.g. real_estate">
                        <span class="text-muted extra-small">{{ __('Leave empty to auto-generate from English name.') }}</span>
                        @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">{{ __('Sort Order / Display Sequence') }}</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 10) }}" min="0">
                        <span class="text-muted extra-small">{{ __('Lower numbers appear first on the shop creation screen.') }}</span>
                    </div>

                    <!-- Icon Picker -->
                    <div class="col-12">
                        <label class="form-label fw-bold text-dark">{{ __('Category Icon *') }}</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text bg-light fs-5" id="iconPreview">
                                <i class="{{ old('icon', 'fas fa-briefcase') }} text-primary"></i>
                            </span>
                            <input type="text" name="icon" id="iconInput" class="form-control font-monospace @error('icon') is-invalid @enderror" value="{{ old('icon', 'fas fa-briefcase') }}" required oninput="updateIconPreview(this.value)">
                        </div>
                        <span class="text-muted extra-small d-block mb-2">{{ __('FontAwesome 6 class name (e.g. fas fa-briefcase, fas fa-building, fas fa-car). Click a quick preset below:') }}</span>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($suggestedIcons as $iconClass => $iconLabel)
                                <button type="button" class="btn btn-sm btn-light border d-inline-flex align-items-center gap-1 extra-small" onclick="selectSuggestedIcon('{{ $iconClass }}')">
                                    <i class="{{ $iconClass }} text-primary"></i> {{ $iconLabel }}
                                </button>
                            @endforeach
                        </div>
                        @error('icon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold text-dark">{{ __('Short Description (English)') }}</label>
                        <textarea name="description" rows="2" class="form-control @error('description') is-invalid @enderror" placeholder="Brief summary of businesses using this category...">{{ old('description') }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold text-dark">{{ __('Short Description (Arabic) / الوصف بالعربية') }}</label>
                        <textarea name="description_ar" rows="2" class="form-control font-arabic @error('description_ar') is-invalid @enderror" dir="rtl" placeholder="نبذة مختصرة عن هذا النشاط التجاري...">{{ old('description_ar') }}</textarea>
                        @error('description_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <div class="form-check form-switch p-3 bg-light rounded-3 border">
                            <input class="form-check-input ms-1" type="checkbox" name="is_active" id="isActiveSwitch" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-dark ms-2" for="isActiveSwitch">
                                {{ __('Active Status (Enable for Shop Creation)') }}
                            </label>
                            <p class="text-muted extra-small mb-0 ms-2">{{ __('When enabled, this category will immediately appear as a selectable option when creating or editing shops.') }}</p>
                        </div>
                    </div>
                </div>
            </x-card>

            <!-- Starter Departments Card -->
            <x-card :title="__('2. Starter Departments (Auto-Seeded on Shop Creation)')" class="mt-4">
                <p class="text-muted small mb-2">
                    {{ __('Enter the department names that should automatically be created when a new shop selects this industry. Enter one department name per line.') }}
                </p>
                <textarea name="default_departments_text" rows="4" class="form-control font-monospace" placeholder="e.g.&#10;Sales & Showroom&#10;Operations & Logistics&#10;Customer Care&#10;Accounts & Billing">{{ old('default_departments_text', "Sales & Marketing\nOperations & Fulfillment\nCustomer Service\nAccounts & Finance") }}</textarea>
                <span class="text-muted extra-small">{{ __('Each non-empty line will become an active department for the newly created shop.') }}</span>
            </x-card>
        </div>

        <!-- Right: Default Modular Permissions -->
        <div class="col-lg-5">
            <x-card :title="__('3. Default Recommended Modules')">
                <p class="text-muted small mb-3">
                    {{ __('Select which ERP modules should be checked by default whenever Super Admin selects this category during shop registration.') }}
                </p>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleAllModules(true)">
                        <i class="fas fa-check-double me-1"></i> {{ __('Select All') }}
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleAllModules(false)">
                        <i class="fas fa-times me-1"></i> {{ __('Deselect All') }}
                    </button>
                </div>

                <div class="d-flex flex-column gap-2" style="max-height: 520px; overflow-y: auto;">
                    @foreach($allModules as $modKey => $modInfo)
                        <label class="border rounded-3 p-3 d-flex align-items-start gap-3 cursor-pointer hover-bg-light" style="cursor: pointer;">
                            <input type="checkbox" name="default_modules[]" value="{{ $modKey }}" 
                                   class="form-check-input module-checkbox mt-1 flex-shrink-0"
                                   {{ is_array(old('default_modules')) && in_array($modKey, old('default_modules')) ? 'checked' : '' }}>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="{{ $modInfo['icon'] }} text-primary"></i>
                                    <strong class="text-dark small">{{ __(app()->getLocale() === 'ar' ? $modInfo['name_ar'] : $modInfo['name']) }}</strong>
                                </div>
                                <span class="badge bg-light text-secondary border font-monospace extra-small">{{ $modKey }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>

                <div class="d-grid gap-2 mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-primary-modern py-2">
                        <i class="fas fa-save me-1"></i> {{ __('Save & Create Category') }}
                    </button>
                    <a href="{{ route('super_admin.business_types.index') }}" class="btn btn-outline-secondary">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </x-card>
        </div>
    </div>
</form>

@push('scripts')
<script>
function selectSuggestedIcon(iconClass) {
    document.getElementById('iconInput').value = iconClass;
    updateIconPreview(iconClass);
}

function updateIconPreview(iconClass) {
    const preview = document.getElementById('iconPreview');
    preview.innerHTML = `<i class="${iconClass} text-primary"></i>`;
}

function toggleAllModules(check) {
    document.querySelectorAll('.module-checkbox').forEach(cb => cb.checked = check);
}

// Auto slug generator from name
document.getElementById('nameInput').addEventListener('input', function() {
    const slugInput = document.getElementById('slugInput');
    if (!slugInput.dataset.manual) {
        slugInput.value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '');
    }
});
document.getElementById('slugInput').addEventListener('input', function() {
    this.dataset.manual = 'true';
});
</script>
@endpush
@endsection
