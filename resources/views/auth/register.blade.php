@extends('layouts.app')

@section('content')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush
<div class="auth-bg min-vh-100 d-flex align-items-center justify-content-center">
    <div class="auth-card p-4 shadow-sm" style="background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); border-radius: 12px; max-width: 420px; width: 100%;">
        @php
            $logoPath = \App\Models\Setting::first()->company_logo ?? asset('uploads/logo/newsign-logo.png');
        @endphp
        <img src="{{ asset($logoPath) }}" alt="Logo" class="mb-4 mx-auto d-block" style="max-height:80px;">
        <h3 class="text-center mb-4">{{ __('Register') }}</h3>
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">{{ __('Name') }}</label>
                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">{{ __('Email Address') }}</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">{{ __('Password') }}</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password-confirm" class="form-label">{{ __('Confirm Password') }}</label>
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary-modern btn-block">{{ __('Register') }}</button>
            </div>

            <div class="mt-3 text-center">
                <a class="link-primary" href="{{ route('login') }}">{{ __('Already have an account? Login') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
