@extends('admin.layouts.app')

@section('title', isset($customer) ? 'Edit Customer' : 'Add Customer')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <h5>{{ isset($customer) ? 'Edit Customer' : 'Add Customer' }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ isset($customer) ? route('customers.update', $customer) : route('customers.store') }}" method="POST" id="customer-form" novalidate>
            @csrf
            @if(isset($customer)) @method('PUT') @endif

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $customer->name ?? '') }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Company Name</label>
                    <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name', $customer->company_name ?? '') }}">
                    @error('company_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $customer->email ?? '') }}">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone Number *</label>
                    <x-country-phone-picker id="customer_phone" name="phone" codeName="country_code" :error="$errors->first('phone')" />
                    @error('phone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    <small class="text-muted d-block mt-1">Select country code and enter mobile number without leading 0.</small>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="2">{{ old('address', $customer->address ?? '') }}</textarea>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" value="{{ old('city', $customer->city ?? '') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">ZIP Code</label>
                    <input type="text" name="zip" class="form-control" value="{{ old('zip', $customer->zip ?? '') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Country</label>
                    <input type="text" name="country" class="form-control" value="{{ old('country', $customer->country ?? '') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">VAT Number</label>
                    <input type="text" name="vat_no" class="form-control" value="{{ old('vat_no', $customer->vat_no ?? '') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">CR Number</label>
                    <input type="text" name="cr_no" class="form-control" value="{{ old('cr_no', $customer->cr_no ?? '') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Status</label>
                    <select name="is_active" class="form-select">
                        <option value="1" {{ old('is_active', $customer->is_active ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active', $customer->is_active ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>
            
            <div class="text-end">
                <a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary" id="saveCustomerBtn">{{ isset($customer) ? 'Update' : 'Save' }} Customer</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('customer-form').addEventListener('submit', function() {
        const btn = document.getElementById('saveCustomerBtn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
        }
    });
</script>
@endsection

