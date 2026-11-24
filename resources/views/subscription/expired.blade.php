<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Subscription Expired') }} - {{ platform_brand_name() }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Amiri:wght@400;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS (Local with CDN fallback) -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" onerror="this.onerror=null;">

    <!-- FontAwesome (Local with CDN fallback) -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" onerror="this.onerror=null;">

    <!-- Theme CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/admin-theme.css') }}?v={{ @filemtime(public_path('assets/css/admin-theme.css')) ?: time() }}">

    <style>
        :root {
            --app-font: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            --brand-primary: #4361ee;
            --brand-dark: #0f172a;
        }
        body {
            font-family: var(--app-font);
            background: #f8fafc radial-gradient(circle at 15% 15%, rgba(67, 97, 238, 0.06) 0%, transparent 40%),
                        radial-gradient(circle at 85% 85%, rgba(220, 53, 69, 0.05) 0%, transparent 40%);
            min-height: 100vh;
            color: #1e293b;
        }
        [dir="rtl"] body {
            font-family: 'Amiri', var(--app-font);
        }
        .expired-hero-card {
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05), 0 20px 25px -5px rgba(0, 0, 0, 0.02);
            border-radius: 1.25rem;
            position: relative;
            overflow: hidden;
        }
        .expired-hero-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #ef4444 0%, #f59e0b 50%, #ef4444 100%);
        }
        .status-pulse-icon {
            width: 68px;
            height: 68px;
            min-width: 68px;
            border-radius: 1.25rem;
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            position: relative;
        }
        .status-pulse-icon::after {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 1.5rem;
            border: 2px solid rgba(239, 68, 68, 0.25);
            animation: pulseWave 2s infinite ease-out;
        }
        @keyframes pulseWave {
            0% { transform: scale(0.95); opacity: 1; }
            100% { transform: scale(1.15); opacity: 0; }
        }
        .package-card {
            background: #ffffff;
            border: 2px solid #e2e8f0;
            border-radius: 1.25rem;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .package-card:hover {
            transform: translateY(-5px);
            border-color: #3b82f6;
            box-shadow: 0 20px 35px -10px rgba(59, 130, 246, 0.15);
        }
        .package-card.recommended {
            border-color: #4361ee;
            box-shadow: 0 15px 30px -10px rgba(67, 97, 238, 0.18);
        }
        .package-card.business {
            border-color: #10b981;
            box-shadow: 0 15px 30px -10px rgba(16, 185, 129, 0.15);
        }
        .pkg-ribbon {
            position: absolute;
            top: -13px;
            right: 24px;
            padding: 4px 14px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 9999px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .step-pill {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--brand-primary);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
            flex-shrink: 0;
        }
        .btn-brand-primary {
            background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            color: #fff;
            border: none;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .btn-brand-primary:hover {
            background: linear-gradient(135deg, #3b52d4 0%, #2f0985 100%);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(67, 97, 238, 0.25);
        }
        .btn-whatsapp {
            background-color: #25D366;
            color: #fff;
            border: none;
            font-weight: 600;
        }
        .btn-whatsapp:hover {
            background-color: #1ebc59;
            color: #fff;
            box-shadow: 0 6px 16px rgba(37, 211, 102, 0.25);
        }
        .contact-chip {
            background: #f1f5f9;
            border-radius: 0.75rem;
            padding: 10px 14px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
    </style>
</head>
<body class="py-4 py-lg-5">

<div class="container" style="max-width: 1140px;">
    <!-- Top Navigation & Brand Header -->
    <header class="d-flex justify-content-between align-items-center mb-4 pb-2 flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            @php
                $logo = platform_logo_url();
                $brand = platform_brand_name();
            @endphp
            @if($logo)
                <img src="{{ $logo }}" alt="{{ $brand }}" style="max-height: 48px; object-fit: contain;">
            @else
                <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-3 shadow-sm" style="width: 44px; height: 44px; font-size: 1.25rem;">
                    <i class="fas fa-store"></i>
                </div>
            @endif
            <div>
                <h4 class="fw-bold text-dark mb-0 line-height-1">{{ $brand }}</h4>
                <span class="text-muted small">{{ __('Enterprise Management Platform') }}</span>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <div class="d-none d-sm-flex align-items-center bg-white px-3 py-2 rounded-3 border shadow-xs gap-2">
                <i class="fas fa-building text-primary"></i>
                <div class="text-start">
                    <span class="d-block extra-small text-muted fw-bold text-uppercase">{{ __('Active Shop') }}</span>
                    <strong class="text-dark small">{{ $shop->name }}</strong>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm px-3 rounded-3 shadow-xs d-flex align-items-center gap-2">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>{{ __('Sign Out') }}</span>
                </button>
            </form>
        </div>
    </header>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4 shadow-sm border-0 d-flex align-items-center gap-3 p-3 bg-success-subtle text-success-emphasis" role="alert">
            <div class="rounded-circle bg-success text-white p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                <i class="fas fa-check"></i>
            </div>
            <div class="flex-grow-1">
                <strong class="d-block">{{ __('Request Submitted Successfully!') }}</strong>
                <span class="small">{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4 shadow-sm border-0 d-flex align-items-center gap-3 p-3" role="alert">
            <i class="fas fa-exclamation-triangle fs-4 text-danger"></i>
            <div class="flex-grow-1">
                <strong>{{ __('Notice') }}:</strong> {{ session('error') }}
            </div>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Hero Card: Subscription Paused Notice -->
    <div class="card expired-hero-card mb-4 p-4 p-lg-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-4">
            <div class="d-flex align-items-start align-items-sm-center gap-3">
                <div class="status-pulse-icon">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                        <h4 class="fw-bold text-danger mb-0">
                            @if($shop->isTrialAccount())
                                {{ __('Trial Period Concluded (7 Days Test Expired)') }}
                            @else
                                {{ __('Subscription Expired — Access Paused') }}
                            @endif
                        </h4>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill extra-small fw-bold">
                            <i class="fas fa-lock me-1"></i> {{ __('Renewal Required') }}
                        </span>
                    </div>
                    <p class="text-secondary mb-0 small" style="max-width: 680px; line-height: 1.55;">
                        {{ __('Your shop') }} <strong class="text-dark">{{ $shop->name }}</strong>
                        @if($shop->subscription_expires_at)
                            {{ __('reached its validity date on') }} <strong class="text-dark">{{ $shop->subscription_expires_at->format('d M, Y') }}</strong>.
                        @else
                            {{ __('has passed its active license period.') }}
                        @endif
                        {{ __('To resume creating Quotes, Job Orders, Travel Bookings, and Tax Invoices, please select a package and complete offline cash renewal.') }}
                    </p>
                </div>
            </div>

            <div class="d-flex flex-sm-row flex-column gap-2 align-items-stretch align-items-sm-center flex-shrink-0">
                <button type="button" class="btn btn-brand-primary px-4 py-2 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2" data-bs-toggle="modal" data-bs-target="#requestRenewalModal">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>{{ __('I Paid Cash (Activate)') }}</span>
                </button>
                @php
                    $adminPhone = $setting->company_phone ?? '+966 50 000 0000';
                    $cleanPhone = preg_replace('/[^0-9]/', '', $adminPhone);
                    $waText = urlencode("Hello Super Admin, I want to renew subscription for shop: " . $shop->name . " (Shop ID: " . $shop->id . ")");
                @endphp
                @if($cleanPhone)
                    <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waText }}" target="_blank" class="btn btn-whatsapp px-3 py-2 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2">
                        <i class="fab fa-whatsapp fs-5"></i>
                        <span>{{ __('WhatsApp Admin') }}</span>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- 3 Simple Steps & Cash Notice -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-xs rounded-4 p-3 bg-white h-100 border">
                <div class="d-flex align-items-center gap-3">
                    <div class="step-pill">1</div>
                    <div>
                        <strong class="d-block text-dark small mb-1">{{ __('1. Choose Package') }}</strong>
                        <span class="text-muted extra-small d-block">{{ __('Pick 1-Year Single or Multi-Shop plan below.') }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-xs rounded-4 p-3 bg-white h-100 border">
                <div class="d-flex align-items-center gap-3">
                    <div class="step-pill" style="background: #10b981;">2</div>
                    <div>
                        <strong class="d-block text-dark small mb-1">{{ __('2. Cash Handover') }}</strong>
                        <span class="text-muted extra-small d-block">{{ __('Hand over cash by hand to Super Admin / representative.') }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-xs rounded-4 p-3 bg-white h-100 border">
                <div class="d-flex align-items-center gap-3">
                    <div class="step-pill" style="background: #f59e0b;">3</div>
                    <div>
                        <strong class="d-block text-dark small mb-1">{{ __('3. Instant Activation') }}</strong>
                        <span class="text-muted extra-small d-block">{{ __('Submit payment notice to unlock system immediately.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Packages Section Heading -->
    <div class="text-center my-4">
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill extra-small fw-bold text-uppercase mb-2">
            {{ __('Flexible Plans') }}
        </span>
        <h3 class="fw-bold text-dark mb-1">{{ __('Select Your Subscription Package') }}</h3>
        <p class="text-muted small mb-0">{{ __('Transparent pricing with zero hidden fees. Select any package to submit cash payment request.') }}</p>
    </div>

    <!-- Available Packages Grid -->
    <div class="row g-4 justify-content-center mb-5">
        @foreach($packages as $pkg)
            @php
                $isRecommended = $pkg->slug === 'yearly';
                $isBusiness = $pkg->slug === 'business' || $pkg->max_shops > 1;
                $isTrial = $pkg->isTrial();
            @endphp
            <div class="col-lg-4 col-md-6">
                <div class="package-card p-4 {{ $isRecommended ? 'recommended' : ($isBusiness ? 'business' : '') }}">
                    @if($isRecommended)
                        <span class="pkg-ribbon bg-primary text-white">
                            <i class="fas fa-crown me-1 text-warning"></i> {{ __('Recommended') }}
                        </span>
                    @elseif($isBusiness)
                        <span class="pkg-ribbon bg-success text-white">
                            <i class="fas fa-store-alt me-1"></i> {{ __('Multi-Branch') }}
                        </span>
                    @endif

                    <div class="d-flex justify-content-between align-items-start mb-3 mt-1">
                        <div>
                            <h5 class="fw-bold text-dark mb-1">{{ $pkg->name }}</h5>
                            <span class="text-muted extra-small">
                                <i class="fas fa-calendar-alt text-primary me-1"></i> {{ $pkg->duration_days }} {{ __('Days Validity') }}
                            </span>
                        </div>
                        <span class="badge bg-light text-secondary border font-monospace">
                            {{ $pkg->billing_cycle === 'weekly' ? __('1 Week') : __('1 Year') }}
                        </span>
                    </div>

                    <!-- Pricing Banner -->
                    <div class="p-3 rounded-3 bg-light border mb-3">
                        <div class="d-flex align-items-baseline gap-2">
                            <span class="fs-2 fw-bold text-dark font-monospace">
                                @if($pkg->price == 0)
                                    {{ __('Free') }}
                                @else
                                    SAR {{ number_format($pkg->price_sar, 0) }}
                                @endif
                            </span>
                            @if($pkg->price > 0)
                                <span class="text-muted small">/ {{ $pkg->duration_days }} {{ __('days') }}</span>
                            @endif
                        </div>
                        @if($pkg->price > 0)
                            <div class="text-muted extra-small mt-1 d-flex align-items-center gap-1 font-monospace">
                                <i class="fas fa-globe text-primary"></i>
                                <span>≈ ${{ number_format($pkg->price, 0) }} USD</span>
                                <span class="text-secondary">({{ __('Official Pegged Rate') }})</span>
                            </div>
                        @endif
                    </div>

                    <p class="text-muted small mb-3">
                        {{ $pkg->description }}
                    </p>

                    <div class="border-top pt-3 mb-3">
                        <span class="extra-small text-uppercase fw-bold text-muted d-block mb-2">{{ __('Included Features') }}:</span>
                        <ul class="list-unstyled mb-0 small">
                            <li class="mb-2 d-flex align-items-start gap-2">
                                <i class="fas fa-check-circle text-success mt-1"></i>
                                <span>
                                    <strong>
                                        @if($pkg->max_shops > 1)
                                            {{ __('Multi-Shop: Up to :count Branches', ['count' => $pkg->max_shops]) }}
                                        @else
                                            {{ __('Single Shop Branch (1 Location)') }}
                                        @endif
                                    </strong>
                                </span>
                            </li>
                            <li class="mb-2 d-flex align-items-start gap-2">
                                <i class="fas fa-check-circle text-success mt-1"></i>
                                <span>{{ __('Staff Accounts: Up to :count Members', ['count' => $pkg->max_employees ?: 10]) }}</span>
                            </li>
                            @if(!empty($pkg->features) && is_array($pkg->features))
                                @foreach($pkg->features as $feat)
                                    <li class="mb-2 d-flex align-items-start gap-2 text-secondary">
                                        <i class="fas fa-check text-primary mt-1"></i>
                                        <span>{{ $feat }}</span>
                                    </li>
                                @endforeach
                            @endif
                            <li class="mb-2 d-flex align-items-start gap-2 text-secondary">
                                <i class="fas fa-hand-holding-usd text-warning mt-1"></i>
                                <span><strong>{{ __('Payment Mode') }}:</strong> {{ __('Cash By Hand Offline') }}</span>
                            </li>
                        </ul>
                    </div>

                    <div class="mt-auto pt-3">
                        <button type="button" 
                                class="btn {{ $isRecommended ? 'btn-brand-primary' : ($isBusiness ? 'btn-success' : 'btn-outline-primary') }} w-100 py-2 rounded-3 shadow-sm fw-bold d-flex align-items-center justify-content-center gap-2"
                                data-bs-toggle="modal" 
                                data-bs-target="#requestRenewalModal" 
                                data-package-id="{{ $pkg->id }}" 
                                data-package-name="{{ $pkg->name }}">
                            <i class="fas fa-check-circle"></i>
                            <span>{{ __('Select & Pay Cash') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Offline Cash & Super Admin Helpline Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4 border">
        <div class="row align-items-center g-3">
            <div class="col-lg-7">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <div class="p-2 rounded-3 bg-warning-subtle text-warning">
                        <i class="fas fa-hand-holding-usd fs-5"></i>
                    </div>
                    <h5 class="fw-bold mb-0 text-dark">{{ __('Offline Cash Handover Information') }}</h5>
                </div>
                <p class="text-muted small mb-0 mt-2" style="line-height: 1.6;">
                    {{ __('Digital gateway charges are zero. All subscription fees are received offline by hand or direct bank transfer. Hand over your cash payment to Super Admin or authorized representative. Your shop account will be activated immediately upon confirmation!') }}
                </p>
            </div>
            <div class="col-lg-5">
                <div class="p-3 bg-light rounded-3 border">
                    <span class="extra-small text-uppercase fw-bold text-muted d-block mb-1">{{ __('Super Admin Direct Helpline') }}</span>
                    @php
                        $adminEmail = $setting->company_email ?? 'admin@crelogics.com';
                    @endphp
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <a href="tel:{{ $adminPhone }}" class="fw-bold text-dark fs-6 text-decoration-none font-monospace">
                            <i class="fas fa-phone-alt text-success me-1"></i> {{ $adminPhone }}
                        </a>
                        @if($cleanPhone)
                            <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waText }}" target="_blank" class="badge bg-success text-white text-decoration-none">
                                <i class="fab fa-whatsapp me-1"></i> {{ __('WhatsApp') }}
                            </a>
                        @endif
                    </div>
                    <div class="small text-muted text-truncate">
                        <i class="fas fa-envelope text-primary me-1"></i> <a href="mailto:{{ $adminEmail }}" class="text-secondary text-decoration-none">{{ $adminEmail }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Request Cash Verification Modal -->
<div class="modal fade" id="requestRenewalModal" tabindex="-1" aria-labelledby="requestRenewalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="{{ route('subscription.request_renewal') }}" method="POST">
                @csrf
                <div class="modal-header bg-light border-0 pb-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 rounded-3 bg-success-subtle text-success">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0" id="requestRenewalModalLabel">
                                {{ __('Cash Payment Handover Notice') }}
                            </h5>
                            <span class="text-muted extra-small">{{ __('Notify Super Admin for instant activation') }}</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        {{ __('Please select your chosen package and optionally mention who received the cash or transfer reference. Super Admin will immediately verify and unlock your shop.') }}
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">{{ __('Selected Package') }} <span class="text-danger">*</span></label>
                        <select name="package_id" id="modalPackageSelect" class="form-select" required>
                            @foreach($packages as $pkg)
                                @if(!$pkg->isTrial())
                                    <option value="{{ $pkg->id }}" {{ $pkg->slug === 'yearly' ? 'selected' : '' }}>
                                        {{ $pkg->name }} — SAR {{ number_format($pkg->price_sar, 0) }} (${{ number_format($pkg->price, 0) }} USD) ({{ $pkg->duration_days }} {{ __('Days') }}, {{ $pkg->max_shops > 1 ? $pkg->max_shops . ' ' . __('Branches') : __('1 Branch') }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">{{ __('Cash Handover / Bank Note') }} <span class="text-muted fw-normal">({{ __('Optional') }})</span></label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="{{ __('e.g. Handed SAR 1875 cash to Super Admin at branch office on 24 Nov, or Bank Transfer Ref #...') }}"></textarea>
                    </div>

                    <div class="alert alert-warning py-2 px-3 extra-small border-0 rounded-3 mb-0 d-flex align-items-center gap-2">
                        <i class="fas fa-info-circle text-warning fs-5"></i>
                        <div>{{ __('Payment Mode is offline Cash By Hand or Bank Transfer. Once confirmed, activation is instantaneous.') }}</div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-brand-primary px-4 shadow-sm">
                        <i class="fas fa-paper-plane me-1"></i> {{ __('Submit Activation Request') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap 5 Bundle JS (Local with CDN fallback) -->
<script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" onerror="this.onerror=null;"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modal = document.getElementById('requestRenewalModal');
        if (modal) {
            modal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                if (button && button.getAttribute('data-package-id')) {
                    var pkgId = button.getAttribute('data-package-id');
                    var select = document.getElementById('modalPackageSelect');
                    if (select) {
                        select.value = pkgId;
                    }
                }
            });
        }
    });
</script>
</body>
</html>
