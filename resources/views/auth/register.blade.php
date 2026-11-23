@extends('layouts.guest')

@section('title', __('Create Your Account — Crelogics ERP'))

@push('styles')
<style>
    body {
        background-color: var(--erp-body-bg, #F5F7FB);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        padding: 2rem 0;
    }
    .erp-auth-container {
        width: 100%;
        max-width: 530px;
        padding: 1.5rem;
    }
    .erp-auth-card {
        background: #FFFFFF;
        border-radius: 16px;
        border: 1px solid #E9EDF3;
        box-shadow: 0 12px 36px rgba(38, 50, 71, 0.08);
        padding: 2.25rem 2rem;
    }
    .erp-auth-btn {
        width: 100%;
        padding: 0.8rem 1rem;
        font-size: 0.95rem;
        font-weight: 700;
        color: #FFFFFF;
        background-color: #4361EE;
        border: none;
        border-radius: 10px;
        transition: all 0.2s ease;
        box-shadow: 0 4px 14px rgba(67, 97, 238, 0.3);
    }
    .erp-auth-btn:hover {
        background-color: #3451D1;
        box-shadow: 0 6px 18px rgba(67, 97, 238, 0.4);
        transform: translateY(-1px);
        color: #FFFFFF;
    }
    .erp-auth-input {
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        padding: 0.65rem 0.95rem;
        font-size: 0.9rem;
        color: #17243A;
    }
    .erp-auth-input:focus {
        border-color: #4361EE;
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
    }
</style>
@endpush

@section('content')
<div class="erp-auth-container">
    <div class="erp-auth-card">
        <!-- Logo & Header -->
        <div class="text-center mb-4">
            <a href="{{ url('/') }}" class="d-inline-block mb-3">
                <img src="{{ platform_logo_url() ?: asset('uploads/logo/crelogics-logo.png') }}" alt="{{ platform_brand_name() }}" style="max-height: 55px; max-width: 180px; object-fit: contain;">
            </a>
            <div class="d-flex justify-content-center align-items-center gap-2 mb-1">
                <h4 class="fw-bold text-dark mb-0">{{ __('Create Your Account') }}</h4>
            </div>
            <p class="text-muted small mb-0">{{ __('Get started with full access to manage quotes, jobs, and invoices.') }}</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger py-2 px-3 small rounded-3 mb-3 border-0 shadow-sm">
                <div class="fw-bold mb-1"><i class="fas fa-exclamation-circle me-1"></i> {{ __('Please fix the following issues:') }}</div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" id="register-form">
            @csrf

            <!-- 1. Shop / Business Name -->
            <div class="mb-3">
                <label for="company_name" class="form-label fw-semibold text-dark small">
                    <i class="fas fa-store text-primary me-1"></i> {{ __('Shop / Business Name') }} <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-building"></i></span>
                    <input id="company_name" type="text" class="form-control erp-auth-input border-start-0 @error('company_name') is-invalid @enderror" name="company_name" value="{{ old('company_name') }}" required placeholder="Modern Business & Services" autofocus>
                </div>
                @error('company_name')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- 2. Primary Business Industry -->
            <div class="mb-3">
                <label for="business_type" class="form-label fw-semibold text-dark small">
                    <i class="fas fa-briefcase text-primary me-1"></i> {{ __('Business Type / Industry') }} <span class="text-danger">*</span>
                </label>
                <select name="business_type" id="business_type" class="form-select erp-auth-input @error('business_type') is-invalid @enderror" required>
                    <option value="" disabled {{ old('business_type') ? '' : 'selected' }}>{{ __('Select your business industry...') }}</option>
                    @if(isset($businessTypes) && is_array($businessTypes))
                        @foreach($businessTypes as $typeKey => $typeInfo)
                            <option value="{{ $typeKey }}" {{ old('business_type', 'travel_umrah') === $typeKey ? 'selected' : '' }}>
                                {{ app()->getLocale() === 'ar' ? ($typeInfo['name_ar'] ?? $typeInfo['name']) : $typeInfo['name'] }}
                            </option>
                        @endforeach
                    @else
                        <option value="travel_umrah" {{ old('business_type', 'travel_umrah') === 'travel_umrah' ? 'selected' : '' }}>{{ __('Umrah, Travel & Tourism Agency') }}</option>
                        <option value="printing_advertising" {{ old('business_type') === 'printing_advertising' ? 'selected' : '' }}>{{ __('Printing, Signage & Advertising') }}</option>
                        <option value="general_trading" {{ old('business_type') === 'general_trading' ? 'selected' : '' }}>{{ __('General Trading & Retail Store') }}</option>
                        <option value="services_agency" {{ old('business_type') === 'services_agency' ? 'selected' : '' }}>{{ __('Digital & Professional Services') }}</option>
                    @endif
                </select>
                <small class="text-muted d-block mt-1" style="font-size: 0.74rem;">
                    {{ __('Modules (Umrah, Travel, Signage, Invoices) will be automatically configured for your industry.') }}
                </small>
                @error('business_type')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- 3. Owner Full Name -->
            <div class="mb-3">
                <label for="name" class="form-label fw-semibold text-dark small">
                    <i class="fas fa-user text-primary me-1"></i> {{ __('Your Full Name') }} <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-id-badge"></i></span>
                    <input id="name" type="text" class="form-control erp-auth-input border-start-0 @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required placeholder="Abdullah Khan">
                </div>
                @error('name')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- 4. Work Email -->
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold text-dark small">
                    <i class="fas fa-envelope text-primary me-1"></i> {{ __('Email Address') }} <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-at"></i></span>
                    <input id="email" type="email" class="form-control erp-auth-input border-start-0 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required placeholder="owner@company.com">
                </div>
                @error('email')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- 5. Phone / WhatsApp Number with Country Picker -->
            <div class="mb-3">
                <label for="register_phone" class="form-label fw-semibold text-dark small">
                    <i class="fas fa-phone-alt text-success me-1"></i> {{ __('Phone / WhatsApp') }} <span class="text-danger">*</span>
                </label>
                <x-country-phone-picker id="register_phone" name="phone" codeName="country_code" :required="true" placeholder="50 123 4567" :error="$errors->first('phone')" />
                @error('phone')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <small class="text-muted d-block mt-1" style="font-size: 0.74rem;">{{ __('Select country code and enter mobile number without leading 0.') }}</small>
            </div>

            <!-- 6. Password & Confirm Grid -->
            <div class="row g-2 mb-4">
                <div class="col-sm-6">
                    <label for="password" class="form-label fw-semibold text-dark small">
                        <i class="fas fa-lock text-primary me-1"></i> {{ __('Password') }} <span class="text-danger">*</span>
                    </label>
                    <input id="password" type="password" class="form-control erp-auth-input @error('password') is-invalid @enderror" name="password" required placeholder="Min 8 chars" autocomplete="new-password">
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-sm-6">
                    <label for="password-confirm" class="form-label fw-semibold text-dark small">
                        <i class="fas fa-shield-alt text-primary me-1"></i> {{ __('Confirm Password') }} <span class="text-danger">*</span>
                    </label>
                    <input id="password-confirm" type="password" class="form-control erp-auth-input" name="password_confirmation" required placeholder="Retype password" autocomplete="new-password">
                </div>
            </div>

            <!-- Submit Button -->
            <div class="d-grid mt-2">
                <button type="submit" class="erp-auth-btn d-flex align-items-center justify-content-center gap-2" id="register-btn">
                    <span class="spinner-border spinner-border-sm d-none" id="register-spinner" role="status" aria-hidden="true"></span>
                    <span id="register-text">{{ __('Create Account & Start 7-Day Trial') }}</span>
                    <i class="fas fa-arrow-right ms-1"></i>
                </button>
            </div>

            <div class="mt-3 text-center">
                <span class="text-muted small">{{ __('Already have an account?') }}</span>
                <a href="{{ route('login') }}" class="fw-bold text-primary small text-decoration-none ms-1">
                    {{ __('Sign In') }} &rarr;
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('register-form').addEventListener('submit', function() {
        const btn = document.getElementById('register-btn');
        const spinner = document.getElementById('register-spinner');
        const text = document.getElementById('register-text');

        btn.disabled = true;
        spinner.classList.remove('d-none');
        text.textContent = '{{ __("Setting up your workspace...") }}';
    });
</script>
@endsection
