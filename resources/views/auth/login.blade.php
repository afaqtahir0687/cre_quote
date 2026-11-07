@extends('layouts.guest')

@section('title', __('Login'))

@push('styles')
<style>
    body {
        background-color: var(--erp-body-bg, #F5F7FB);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    .erp-auth-container {
        width: 100%;
        max-width: 440px;
        padding: 1.5rem;
    }
    .erp-auth-card {
        background: #FFFFFF;
        border-radius: 14px;
        border: 1px solid #E9EDF3;
        box-shadow: 0 10px 30px rgba(38, 50, 71, 0.08);
        padding: 2.25rem;
    }
    .erp-auth-btn {
        width: 100%;
        padding: 0.75rem 1rem;
        font-size: 0.925rem;
        font-weight: 600;
        color: #FFFFFF;
        background-color: #4361EE;
        border: none;
        border-radius: 8px;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
    }
    .erp-auth-btn:hover {
        background-color: #3451D1;
        box-shadow: 0 6px 16px rgba(67, 97, 238, 0.4);
        transform: translateY(-1px);
        color: #FFFFFF;
    }
    .erp-auth-input {
        border: 1px solid #E9EDF3;
        border-radius: 8px;
        padding: 0.65rem 0.95rem;
        font-size: 0.9rem;
    }
    .erp-auth-input:focus {
        border-color: #4361EE;
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
    }
    .password-wrapper {
        position: relative;
    }
    .password-wrapper .form-control {
        padding-right: 42px;
    }
    .toggle-password {
        position: absolute;
        top: 50%;
        right: 12px;
        transform: translateY(-50%);
        background: none;
        border: none;
        padding: 0;
        cursor: pointer;
        color: #8792A5;
    }
    .toggle-password:hover {
        color: #4361EE;
    }
</style>
@endpush

@section('content')
<div class="erp-auth-container">
    <div class="erp-auth-card">
        <!-- Logo & Branding -->
        <div class="text-center mb-4">
            <img src="{{ platform_logo_url() ?: asset('uploads/logo/crelogics-logo.png') }}" alt="{{ platform_brand_name() }}" class="mb-3 mx-auto d-block" style="max-height: 60px; max-width: 190px; object-fit: contain;">
            <h4 class="fw-bold text-dark mb-1">{{ __('Sign In to Your Account') }}</h4>
            <p class="text-muted small mb-0">{{ platform_brand_name() }} Enterprise ERP</p>
        </div>
        
        @if ($errors->any())
            <div class="alert alert-danger py-2 px-3 small rounded-3 mb-3">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        @if (session('error'))
            <div class="alert alert-danger py-2 px-3 small rounded-3 mb-3">
                {{ session('error') }}
            </div>
        @endif
        
        <form method="POST" action="{{ route('login') }}" id="login-form">
            @csrf

            <div class="mb-3">
                <label for="login" class="form-label fw-semibold text-dark small">{{ __('Username or Email') }}</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-user"></i></span>
                    <input id="login" type="text" class="form-control erp-auth-input border-start-0 @error('login') is-invalid @enderror" name="login" value="{{ old('login') }}" required autocomplete="username" placeholder="admin" autofocus>
                </div>
                @error('login')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="password" class="form-label fw-semibold text-dark small mb-0">{{ __('Password') }}</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="extra-small text-decoration-none text-primary" style="font-size: 0.775rem;">
                            {{ __('Forgot Password?') }}
                        </a>
                    @endif
                </div>
                <div class="password-wrapper input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-lock"></i></span>
                    <input id="password" type="password" class="form-control erp-auth-input border-start-0 @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="••••••••">
                    <button type="button" class="toggle-password" onclick="togglePassword()" title="{{ __('Toggle Password') }}">
                        <i class="fas fa-eye" id="eye-icon"></i>
                        <i class="fas fa-eye-slash" id="eye-off-icon" style="display:none;"></i>
                    </button>
                </div>
                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3 d-flex justify-content-between align-items-center">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label small text-muted" for="remember">{{ __('Remember Me') }}</label>
                </div>
                
                <!-- Language switcher shortcut on login -->
                <div class="dropdown">
                    <a class="btn btn-sm btn-link text-decoration-none text-muted py-0 dropdown-toggle extra-small" href="#" data-bs-toggle="dropdown">
                        <i class="fas fa-globe me-1"></i> {{ app()->getLocale() === 'ar' ? 'العربية' : 'English' }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-1" style="min-width: 110px;">
                        <li><a class="dropdown-item py-1 small" href="{{ route('language.switch', 'en') }}">English</a></li>
                        <li><a class="dropdown-item py-1 small" href="{{ route('language.switch', 'ar') }}">العربية</a></li>
                    </ul>
                </div>
            </div>

            <div class="d-grid mt-4">
                <button type="submit" class="erp-auth-btn d-flex align-items-center justify-content-center gap-2" id="login-btn">
                    <span class="spinner-border spinner-border-sm d-none" id="login-spinner" role="status" aria-hidden="true"></span>
                    <span id="login-text">{{ __('Sign In') }}</span>
                    <i class="fas fa-arrow-right ms-1"></i>
                </button>
            </div>
        </form>

        <div class="text-center mt-4 pt-3 border-top text-muted extra-small" style="font-size: 0.775rem;">
            &copy; {{ date('Y') }} {{ platform_brand_name() }}. {{ __('Secure Enterprise Portal') }}
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const eyeOn = document.getElementById('eye-icon');
        const eyeOff = document.getElementById('eye-off-icon');

        if (input.type === 'password') {
            input.type = 'text';
            eyeOn.style.display = 'none';
            eyeOff.style.display = 'inline-block';
        } else {
            input.type = 'password';
            eyeOn.style.display = 'inline-block';
            eyeOff.style.display = 'none';
        }
    }

    document.getElementById('login-form').addEventListener('submit', function() {
        const btn = document.getElementById('login-btn');
        const spinner = document.getElementById('login-spinner');
        const text = document.getElementById('login-text');

        btn.disabled = true;
        spinner.classList.remove('d-none');
        text.textContent = '{{ __("Signing in...") }}';
    });
</script>
@endsection
