@extends('admin.layouts.app')

@section('title', 'Create New Shop')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Add New Shop</h2>
        <p class="text-muted small mb-0">Create an isolated branch with dedicated data and assign its primary administrator.</p>
    </div>
    <a href="{{ route('super_admin.shops.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to Shops
    </a>
</div>

<form action="{{ route('super_admin.shops.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row g-4">
        <!-- Shop Profile Card -->
        <div class="col-lg-7">
            <x-card title="1. Branch Profile & Settings">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-bold text-dark">Shop / Branch Name *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Al-Noor Signage - Branch 2" value="{{ old('name') }}" required id="shopNameInput">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">URL Slug</label>
                        <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" placeholder="auto-generated" value="{{ old('slug') }}" id="shopSlugInput">
                        @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">Shop Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="branch2@example.com" value="{{ old('email') }}">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">Shop Phone</label>
                        <x-country-phone-picker id="shop_phone" name="phone" codeName="country_code" :required="false" placeholder="50 123 4567" :error="$errors->first('phone')" />
                        @error('phone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        <small class="text-muted d-block mt-1">Select country code and enter phone number without leading 0.</small>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold text-dark">Physical Address</label>
                        <textarea name="address" rows="2" class="form-control @error('address') is-invalid @enderror" placeholder="Street, Industrial Area, Building...">{{ old('address') }}</textarea>
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">City</label>
                        <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" placeholder="e.g. Riyadh, Jeddah, Dammam" value="{{ old('city') }}">
                        @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">Country</label>
                        <input type="text" name="country" class="form-control @error('country') is-invalid @enderror" placeholder="Saudi Arabia" value="{{ old('country', 'Saudi Arabia') }}">
                        @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">Currency Symbol *</label>
                        <input type="text" name="currency_symbol" class="form-control @error('currency_symbol') is-invalid @enderror" placeholder="$, SAR, AED, PKR" value="{{ old('currency_symbol', '$') }}" required>
                        @error('currency_symbol') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">Quote Prefix *</label>
                        <input type="text" name="quote_prefix" class="form-control @error('quote_prefix') is-invalid @enderror" placeholder="QT-" value="{{ old('quote_prefix', 'QT-') }}" required>
                        @error('quote_prefix') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">Invoice Prefix *</label>
                        <input type="text" name="invoice_prefix" class="form-control @error('invoice_prefix') is-invalid @enderror" placeholder="INV-" value="{{ old('invoice_prefix', 'INV-') }}" required>
                        @error('invoice_prefix') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <div class="p-3 border rounded-3 bg-light">
                            <label class="form-label fw-bold text-dark mb-1">Company Logo</label>
                            <div class="d-flex align-items-center gap-3 mt-1">
                                <div id="createLogoPreviewBox" class="bg-white p-2 rounded border d-flex align-items-center justify-content-center" style="width: 70px; height: 70px; min-width: 70px;">
                                    <i class="fas fa-image text-muted fa-2x" id="createLogoPlaceholder"></i>
                                    <img id="createLogoPreviewImg" src="" alt="Preview" class="img-fluid d-none" style="max-height: 54px; max-width: 54px; object-fit: contain;">
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" name="logo" id="createShopLogoInput" class="form-control @error('logo') is-invalid @enderror" accept="image/*">
                                    <small class="text-muted d-block mt-1">Recommended: Clean PNG/JPG logo under 20MB. This logo will display on this shop's dashboard, sidebar, and invoices.</small>
                                    @error('logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold text-dark">Internal Notes</label>
                        <textarea name="notes" rows="2" class="form-control" placeholder="Any private remarks about this branch...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Admin Account Card -->
        <div class="col-lg-5">
            <x-card title="2. Primary Shop Administrator">
                <p class="text-muted small">
                    This account will have full administrative rights restricted exclusively to this branch.
                </p>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold text-dark">Admin Name *</label>
                        <input type="text" name="admin_name" class="form-control @error('admin_name') is-invalid @enderror" placeholder="Full name of shop owner/manager" value="{{ old('admin_name') }}" required>
                        @error('admin_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold text-dark">Username *</label>
                        <input type="text" name="admin_username" class="form-control @error('admin_username') is-invalid @enderror" placeholder="Username for login" value="{{ old('admin_username') }}" required>
                        @error('admin_username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold text-dark">Email Address *</label>
                        <input type="email" name="admin_email" class="form-control @error('admin_email') is-invalid @enderror" placeholder="email@branch.com" value="{{ old('admin_email') }}" required>
                        @error('admin_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold text-dark">Password *</label>
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
            <x-card title="3. Package & Subscription (Cash By Hand)" class="mt-4">
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark">Select Initial Package *</label>
                    <select name="package_id" id="createPackageSelect" class="form-select" required>
                        @foreach($packages as $pkg)
                            <option value="{{ $pkg->id }}" 
                                data-days="{{ $pkg->duration_days }}" 
                                data-price="{{ $pkg->price }}" 
                                data-max-shops="{{ $pkg->max_shops }}"
                                {{ $pkg->slug === 'trial' ? 'selected' : '' }}>
                                {{ $pkg->name }} — {{ $pkg->price == 0 ? 'Free' : number_format($pkg->price, 0) }} ({{ $pkg->duration_days }} Days, {{ $pkg->max_shops > 1 ? $pkg->max_shops . ' Shops' : '1 Shop' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-bold text-dark small">Duration (Days)</label>
                        <input type="number" name="duration_days" id="createDurationInput" class="form-control" value="7" min="1" required>
                        <div class="form-text small">7 days for 1-Week Test Trial.</div>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold text-dark small">Cash Received</label>
                        <input type="number" step="0.01" name="cash_paid" id="createCashInput" class="form-control" value="0" min="0" required>
                        <div class="form-text small">Offline Cash In Hand.</div>
                    </div>
                </div>

                <div class="p-3 rounded-3 bg-light border border-light-subtle mt-3 mb-0 small text-muted">
                    <i class="fas fa-magic text-primary me-2"></i>
                    <strong class="text-dark">Automated Provisioning:</strong> Default production departments (Workshop, Banner Machine, Sticker Print, Sign Board) and initial settings are configured automatically.
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('super_admin.shops.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary-modern">
                        <i class="fas fa-check-circle me-1"></i> Create Shop & Admin
                    </button>
                </div>
            </x-card>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
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

    document.getElementById('shopNameInput')?.addEventListener('input', function() {
        const slugInput = document.getElementById('shopSlugInput');
        if (slugInput && !slugInput.dataset.manual) {
            slugInput.value = this.value
                .toLowerCase()
                .trim()
                .replace(/[^\w\s-]/g, '')
                .replace(/[\s_-]+/g, '-')
                .replace(/^-+|-+$/g, '');
        }
    });

    document.getElementById('shopSlugInput')?.addEventListener('input', function() {
        this.dataset.manual = "true";
    });

    document.getElementById('createShopLogoInput')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(evt) {
                const img = document.getElementById('createLogoPreviewImg');
                const ph = document.getElementById('createLogoPlaceholder');
                if (img) {
                    img.src = evt.target.result;
                    img.classList.remove('d-none');
                }
                if (ph) {
                    ph.classList.add('d-none');
                }
            };
            reader.readAsDataURL(file);
        }
    });

    const createPkgSelect = document.getElementById('createPackageSelect');
    createPkgSelect?.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        if (opt) {
            document.getElementById('createDurationInput').value = opt.getAttribute('data-days') || 7;
            document.getElementById('createCashInput').value = opt.getAttribute('data-price') || 0;
        }
    });
</script>
@endpush
