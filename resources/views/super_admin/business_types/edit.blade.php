@extends('admin.layouts.app')

@section('title', __('Edit Business Category') . ' - ' . $businessType->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="fw-bold text-dark mb-0">
                <i class="fas fa-edit text-primary me-2"></i>{{ __('Edit Business Category') }}: {{ $businessType->name }}
            </h2>
            <span class="badge bg-light text-secondary border font-monospace">{{ $businessType->slug }}</span>
        </div>
        <p class="text-muted small mb-0">{{ __('Update category details, modify default module permissions, or adjust starter departments.') }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('super_admin.business_types.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Categories') }}
        </a>
    </div>
</div>

<form action="{{ route('super_admin.business_types.update', $businessType->id) }}" method="POST" id="editBusinessTypeForm">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <!-- Left: Category Details -->
        <div class="col-lg-7">
            <x-card :title="__('1. Category Identity & Branding')">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">{{ __('Industry Name (English) *') }}</label>
                        <input type="text" name="name" id="nameInput" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $businessType->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">{{ __('Industry Name (Arabic) / الاسم بالعربية') }}</label>
                        <input type="text" name="name_ar" class="form-control font-arabic @error('name_ar') is-invalid @enderror" value="{{ old('name_ar', $businessType->name_ar) }}" dir="rtl">
                        @error('name_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">{{ __('Unique Identifier / Code Slug') }}</label>
                        <input type="text" name="slug" id="slugInput" class="form-control font-monospace @error('slug') is-invalid @enderror" value="{{ old('slug', $businessType->slug) }}" required>
                        <span class="text-muted extra-small">{{ __('Warning: Changing slug will update all associated shops automatically.') }}</span>
                        @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">{{ __('Sort Order / Display Sequence') }}</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $businessType->sort_order) }}" min="0">
                        <span class="text-muted extra-small">{{ __('Lower numbers appear first on the shop creation screen.') }}</span>
                    </div>

                    <!-- Icon Picker -->
                    <div class="col-12">
                        <label class="form-label fw-bold text-dark">{{ __('Category Icon *') }}</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text bg-light fs-5" id="iconPreview">
                                <i class="{{ old('icon', $businessType->icon) }} text-primary"></i>
                            </span>
                            <input type="text" name="icon" id="iconInput" class="form-control font-monospace @error('icon') is-invalid @enderror" value="{{ old('icon', $businessType->icon) }}" required oninput="updateIconPreview(this.value)">
                        </div>
                        <span class="text-muted extra-small d-block mb-2">{{ __('Click a quick icon preset below or type FontAwesome class name:') }}</span>
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
                        <textarea name="description" rows="2" class="form-control @error('description') is-invalid @enderror">{{ old('description', $businessType->description) }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold text-dark">{{ __('Short Description (Arabic) / الوصف بالعربية') }}</label>
                        <textarea name="description_ar" rows="2" class="form-control font-arabic @error('description_ar') is-invalid @enderror" dir="rtl">{{ old('description_ar', $businessType->description_ar) }}</textarea>
                        @error('description_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <div class="form-check form-switch p-3 bg-light rounded-3 border">
                            <input class="form-check-input ms-1" type="checkbox" name="is_active" id="isActiveSwitch" value="1" {{ old('is_active', $businessType->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-dark ms-2" for="isActiveSwitch">
                                {{ __('Active Status (Enable for Shop Creation)') }}
                            </label>
                            <p class="text-muted extra-small mb-0 ms-2">{{ __('When enabled, this category appears as a selectable card when creating or editing shops.') }}</p>
                        </div>
                    </div>
                </div>
            </x-card>

            <!-- Starter Departments Card -->
            <x-card :title="__('2. Starter Departments (Auto-Seeded on Shop Creation)')" class="mt-4">
                <p class="text-muted small mb-2">
                    {{ __('Enter the department names that should automatically be created when a new shop selects this industry. Enter one department name per line.') }}
                </p>
                @php
                    $deptsArray = is_array($businessType->default_departments) ? $businessType->default_departments : [];
                    $deptsText = implode("\n", array_map(function($d) {
                        return is_array($d) ? ($d['name'] ?? '') : $d;
                    }, $deptsArray));
                @endphp
                <textarea name="default_departments_text" rows="4" class="form-control font-monospace" placeholder="Enter department names, one per line...">{{ old('default_departments_text', $deptsText) }}</textarea>
                <span class="text-muted extra-small">{{ __('Each line will be created as an active department for new shops in this category.') }}</span>
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

                @php
                    $activeDefaults = is_array(old('default_modules', $businessType->default_modules)) ? old('default_modules', $businessType->default_modules) : [];
                @endphp

                <div class="d-flex flex-column gap-2" style="max-height: 520px; overflow-y: auto;">
                    @foreach($allModules as $modKey => $modInfo)
                        <label class="border rounded-3 p-3 d-flex align-items-start gap-3 cursor-pointer hover-bg-light" style="cursor: pointer;">
                            <input type="checkbox" name="default_modules[]" value="{{ $modKey }}" 
                                   class="form-check-input module-checkbox mt-1 flex-shrink-0"
                                   {{ in_array($modKey, $activeDefaults) ? 'checked' : '' }}>
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
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-1"></i> {{ __('Save & Update Category') }}
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
</script>
@endpush
@endsection
