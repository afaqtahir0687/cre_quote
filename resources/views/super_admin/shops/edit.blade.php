@extends('admin.layouts.app')

@section('title', 'Edit Shop - ' . $shop->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Edit Shop: {{ $shop->name }}</h2>
        <p class="text-muted small mb-0">Update shop details, branding, invoice prefixes, and active operating status.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <form action="{{ route('super_admin.switch_shop', $shop->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary-modern d-flex align-items-center">
                <i class="fas fa-sign-in-alt me-2"></i> Enter Shop
            </button>
        </form>
        <a href="{{ route('super_admin.shops.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Shops
        </a>
    </div>
</div>

<form action="{{ route('super_admin.shops.update', $shop->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <div class="col-lg-8">
            <x-card title="Branch Profile & Configuration">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-bold text-dark">Shop / Branch Name *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $shop->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">URL Slug *</label>
                        <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $shop->slug) }}" required>
                        @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">Shop Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $shop->email) }}">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    @php
                        $parsedPhone = \App\Models\Customer::parsePhone($shop->phone ?? '');
                        $currentCode = old('country_code', $parsedPhone['country_code']);
                        $currentIso = $parsedPhone['iso'];
                        $currentNumber = old('phone', $parsedPhone['number']);
                    @endphp
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">Shop Phone</label>
                        <x-country-phone-picker id="shop_phone" 
                                                name="phone" 
                                                codeName="country_code" 
                                                :value="$currentNumber" 
                                                :defaultCode="$currentCode" 
                                                :defaultIso="$currentIso" 
                                                :required="false" 
                                                placeholder="50 123 4567" 
                                                :error="$errors->first('phone')" />
                        @error('phone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        <small class="text-muted d-block mt-1">Select country code and enter phone number without leading 0.</small>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold text-dark">Physical Address</label>
                        <textarea name="address" rows="2" class="form-control @error('address') is-invalid @enderror">{{ old('address', $shop->address) }}</textarea>
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">City</label>
                        <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $shop->city) }}">
                        @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">Country</label>
                        <input type="text" name="country" class="form-control @error('country') is-invalid @enderror" value="{{ old('country', $shop->country) }}">
                        @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">Currency Symbol *</label>
                        <input type="text" name="currency_symbol" class="form-control @error('currency_symbol') is-invalid @enderror" value="{{ old('currency_symbol', $shop->currency_symbol) }}" required>
                        @error('currency_symbol') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">Quote Prefix *</label>
                        <input type="text" name="quote_prefix" class="form-control @error('quote_prefix') is-invalid @enderror" value="{{ old('quote_prefix', $shop->quote_prefix) }}" required>
                        @error('quote_prefix') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">Invoice Prefix *</label>
                        <input type="text" name="invoice_prefix" class="form-control @error('invoice_prefix') is-invalid @enderror" value="{{ old('invoice_prefix', $shop->invoice_prefix) }}" required>
                        @error('invoice_prefix') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">Operating Status *</label>
                        <select name="status" id="editStatusSelect" class="form-select @error('status') is-invalid @enderror" required onchange="toggleStatusFields(this.value)">
                            <option value="active" {{ old('status', $shop->status) === 'active' ? 'selected' : '' }}>Active (Allow Full Operations & Login)</option>
                            <option value="inactive" {{ old('status', $shop->status) === 'inactive' ? 'selected' : '' }}>Inactive / Payment Hold (Temporary)</option>
                            <option value="suspended" {{ old('status', $shop->status) === 'suspended' ? 'selected' : '' }}>Suspended (Blocked Permanently)</option>
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">Shop Logo</label>
                        <div class="d-flex align-items-center gap-3">
                            @php
                                $editShopLogo = shop_logo_url($shop, $shop->setting);
                            @endphp
                            <div class="bg-white p-2 rounded border d-flex align-items-center justify-content-center" style="width: 70px; height: 70px; min-width: 70px;">
                                <img id="editShopLogoPreview" src="{{ $editShopLogo ?: asset('uploads/logo/crelogics-logo.png') }}" alt="{{ $shop->name }}" class="img-fluid" style="max-height: 54px; max-width: 54px; object-fit: contain;">
                            </div>
                            <div class="flex-grow-1">
                                <input type="file" name="logo" id="editShopLogoInput" class="form-control @error('logo') is-invalid @enderror" accept="image/*">
                                <small class="text-muted d-block mt-1">Upload a new file to change this shop's logo.</small>
                                @error('logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Inactive / Suspended Notice Container -->
                    <div class="col-12" id="editStatusExtraContainer" style="{{ in_array(old('status', $shop->status), ['inactive', 'suspended']) ? '' : 'display:none;' }}">
                        <div class="p-3 rounded-3 bg-light border border-warning-subtle">
                            <h6 class="fw-bold text-dark mb-2">
                                <i class="fas fa-exclamation-circle text-warning me-1"></i> Deactivation & Notice Details
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-dark">Reason for Deactivation / Suspension</label>
                                    <input type="text" name="status_reason" class="form-control" placeholder="e.g. Overdue Subscription Payment" value="{{ old('status_reason', $shop->status_reason) }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-dark">Grace Period (Days)</label>
                                    <input type="number" name="grace_period_days" class="form-control" placeholder="7" value="{{ old('grace_period_days', $shop->grace_period_days ?? 7) }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-dark">Payment Deadline Date</label>
                                    <input type="date" name="action_deadline" class="form-control" value="{{ old('action_deadline', $shop->action_deadline ? $shop->action_deadline->format('Y-m-d') : '') }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-dark">Instructions / Note for Shop Owner</label>
                                    <textarea name="status_message" rows="2" class="form-control" placeholder="Explain the next steps or payment method...">{{ old('status_message', $shop->status_message) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold text-dark">Internal Notes</label>
                        <textarea name="notes" rows="2" class="form-control">{{ old('notes', $shop->notes) }}</textarea>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('super_admin.shops.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary-modern">
                        <i class="fas fa-save me-1"></i> Save Changes
                    </button>
                </div>
            </x-card>
        </div>

        <div class="col-lg-4">
            <!-- Current Logo Preview Card -->
            <x-card title="Current Logo" class="text-center">
                @if($shop->logo)
                    <img src="{{ asset($shop->logo) }}" alt="{{ $shop->name }}" class="img-fluid border rounded p-2 mb-2" style="max-height: 120px;">
                @else
                    <div class="rounded bg-light border d-flex align-items-center justify-content-center mx-auto mb-2 text-muted" style="width: 100px; height: 100px;">
                        <i class="fas fa-image fa-2x opacity-50"></i>
                    </div>
                    <div class="text-muted small">No custom logo uploaded yet.</div>
                @endif
            </x-card>

            <!-- Linked Admins Card -->
            <x-card title="Shop Administrators" class="mt-4">
                <x-slot name="headerActions">
                    <a href="{{ route('super_admin.users.create') }}?shop_id={{ $shop->id }}" class="btn btn-sm btn-link text-decoration-none fw-medium">
                        <i class="fas fa-plus"></i> Add
                    </a>
                </x-slot>

                <ul class="list-group list-group-flush">
                    @forelse($shop->admins as $admin)
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-bottom">
                        <div>
                            <div class="fw-bold text-dark">{{ $admin->name }}</div>
                            <span class="small text-muted">{{ $admin->email }}</span>
                        </div>
                        <a href="{{ route('super_admin.users.edit', $admin->id) }}" class="btn btn-sm btn-light rounded-circle shadow-none" title="Edit Admin">
                            <i class="fas fa-user-edit text-muted"></i>
                        </a>
                    </li>
                    @empty
                    <li class="list-group-item text-center py-3 text-muted small border-0">
                        No admin user assigned.
                    </li>
                    @endforelse
                </ul>
            </x-card>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    function toggleStatusFields(val) {
        const container = document.getElementById('editStatusExtraContainer');
        if (val === 'inactive' || val === 'suspended') {
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
        }
    }

    document.getElementById('editShopLogoInput')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(evt) {
                const img = document.getElementById('editShopLogoPreview');
                if (img) {
                    img.src = evt.target.result;
                }
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
