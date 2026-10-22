@extends('layouts.guest')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
<style>
    body {
        overflow: hidden;
    }
    .btn-login {
        width: 100%;
        padding: 12px 0;
        font-size: 15px;
        font-weight: 500;
        color: #ffffff;
        background-color: #00aeef;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        letter-spacing: 0.03em;
        transition: background-color 0.2s ease, transform 0.1s ease;
    }
    .btn-login:hover {
        background-color: #0099d4;
    }
    .btn-login:active {
        transform: scale(0.98);
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
        color: #6c757d;
        display: flex;
        align-items: center;
    }
    .toggle-password:hover {
        color: #00aeef;
    }
</style>
@endpush

@section('content')
<div class="auth-bg h-100 d-flex align-items-center justify-content-center mt-5">
    <div class="auth-card p-4 shadow-sm" style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); border-radius: 12px; max-width: 420px; width: 100%;">

        <img src="{{ asset('uploads/logo/crelogics-logo.png') }}" alt="Logo" class="mb-4 mx-auto d-block" style="max-height:80px;">

        <h3 class="text-center mb-4">{{ __('Login') }}</h3>
        
        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        @if (session('error'))
            <div class="alert alert-danger mb-3">
                {{ session('error') }}
            </div>
        @endif
        
        <form method="POST" action="{{ route('login') }}" id="login-form">
            @csrf

            <div class="mb-3">
                <label for="login" class="form-label">{{ __('Username') }}</label>
                <input id="login" type="text" class="form-control @error('login') is-invalid @enderror" name="login" value="{{ old('login') }}" required autocomplete="username" autofocus>
                @error('login')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">{{ __('Password') }}</label>
                <div class="password-wrapper">
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                    <button type="button" class="toggle-password" onclick="togglePassword()">
                        <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg id="eye-off-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display:none;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 012.084-3.37M6.228 6.228A9.97 9.97 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.97 9.97 0 01-4.51 5.345M3 3l18 18" />
                        </svg>
                    </button>
                </div>
                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3 form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">{{ __('Remember Me') }}</label>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn-login" id="login-btn">
                    <span class="spinner-border spinner-border-sm d-none" id="login-spinner" role="status" aria-hidden="true"></span>
                    <span id="login-text">{{ __('Login') }}</span>
                </button>
            </div>

        </form>
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
            eyeOff.style.display = 'block';
        } else {
            input.type = 'password';
            eyeOn.style.display = 'block';
            eyeOff.style.display = 'none';
        }
    }

    document.getElementById('login-form').addEventListener('submit', function() {
        const btn = document.getElementById('login-btn');
        const spinner = document.getElementById('login-spinner');
        const text = document.getElementById('login-text');
        
        btn.disabled = true;
        spinner.classList.remove('d-none');
        text.innerText = 'Logging in...';
    });
</script>
@endsection