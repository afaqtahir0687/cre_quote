<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Expired - {{ config('app.name', 'Crelogics') }}</title>
    
    <!-- Local Vendor CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-theme.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Outfit', 'Inter', -apple-system, sans-serif;
            background: linear-gradient(135deg, #f8f9fd 0%, #edf2f9 100%);
            min-height: 100vh;
        }
        .package-card {
            border: 2px solid #e9ecef;
            border-radius: 1rem;
            transition: all 0.25s ease;
            background: #fff;
        }
        .package-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.08);
            border-color: #0d6efd;
        }
        .package-card.featured {
            border-color: #0d6efd;
            box-shadow: 0 8px 20px rgba(13,110,253,0.12);
            position: relative;
        }
        .package-badge {
            position: absolute;
            top: -12px;
            right: 20px;
        }
    </style>
</head>
<body class="py-5">

<div class="container" style="max-width: 1080px;">
    <!-- Top Brand & Header -->
    <div class="text-center mb-4">
        @php
            $logo = platform_logo_url();
            $brand = platform_brand_name();
        @endphp
        @if($logo)
            <img src="{{ $logo }}" alt="{{ $brand }}" class="img-fluid mb-2" style="max-height: 65px; object-fit: contain;">
        @else
            <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-3 mb-2 shadow-sm" style="width: 56px; height: 56px; font-size: 1.5rem;">
                <i class="fas fa-store"></i>
            </div>
            <h3 class="fw-bold text-dark mb-0">{{ $brand }}</h3>
        @endif
    </div>

    <!-- Alert Banner -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="border-left: 5px solid #dc3545 !important;">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-danger bg-opacity-10 text-danger p-3 d-flex align-items-center justify-content-center" style="width: 58px; height: 58px; min-width: 58px;">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h4 class="fw-bold text-danger mb-0">
                                @if($shop->isTrialAccount())
                                    1-Week Test Account (Trial) Expired
                                @else
                                    Shop Subscription Expired
                                @endif
                            </h4>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill">
                                Access Paused
                            </span>
                        </div>
                        <p class="text-muted mb-0 mt-1">
                            Your shop <strong>{{ $shop->name }}</strong> 
                            @if($shop->subscription_expires_at)
                                passed its validity on <strong>{{ $shop->subscription_expires_at->format('d M, Y') }}</strong>.
                            @else
                                has reached its expiration date.
                            @endif
                            To resume creating quotes, job orders, and invoices, please renew via <strong>Cash By Hand</strong>.
                        </p>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-primary px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#requestRenewalModal">
                        <i class="fas fa-money-bill-wave me-1"></i> I Paid Cash (Request Activation)
                    </button>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary">
                            <i class="fas fa-sign-out-alt me-1"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Cash By Hand Payment Info Box -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-4">
            <div class="row align-items-center g-3">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-2 text-warning-emphasis mb-2">
                        <i class="fas fa-hand-holding-usd fs-4 text-warning"></i>
                        <h5 class="fw-bold mb-0 text-dark">Payment Mode: Cash By Hand</h5>
                    </div>
                    <p class="text-muted small mb-0">
                        Online credit card / digital payment gateways are currently not in use. All packages and renewal fees are collected <strong>in cash directly by hand</strong>. Please hand over the package payment to Super Admin or authorized collection representative. Once cash is confirmed, your account will be activated immediately!
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <div class="p-3 bg-light rounded-3 text-start text-lg-end">
                        <div class="small text-muted">Super Admin Contact</div>
                        @php
                            $adminPhone = $setting->company_phone ?? '+966 50 000 0000';
                            $adminEmail = $setting->company_email ?? 'admin@crelogics.com';
                        @endphp
                        <div class="fw-bold text-dark fs-6 mt-1">
                            <i class="fas fa-phone-alt text-success me-1"></i> {{ $adminPhone }}
                        </div>
                        <div class="small text-muted mt-1">
                            <i class="fas fa-envelope text-primary me-1"></i> {{ $adminEmail }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Packages Grid -->
    <div class="text-center mb-4">
        <h4 class="fw-bold text-dark mb-1">Available System Packages</h4>
        <p class="text-muted small">Choose the package that suits your business requirements.</p>
    </div>

    <div class="row g-4 justify-content-center mb-5">
        @foreach($packages as $pkg)
            <div class="col-md-4">
                <div class="card package-card h-100 p-4 {{ $pkg->slug === 'yearly' ? 'featured' : '' }}">
                    @if($pkg->slug === 'yearly')
                        <span class="badge bg-primary package-badge px-3 py-1 rounded-pill shadow-sm">
                            <i class="fas fa-star me-1"></i> Recommended
                        </span>
                    @elseif($pkg->slug === 'business')
                        <span class="badge bg-success package-badge px-3 py-1 rounded-pill shadow-sm">
                            <i class="fas fa-building me-1"></i> Multi-Shop
                        </span>
                    @endif

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-dark mb-0">{{ $pkg->name }}</h5>
                        <span class="badge {{ $pkg->badgeClass() }}">
                            {{ $pkg->billing_cycle === 'weekly' ? '1 Week' : '1 Year' }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <span class="display-6 fw-bold text-dark">
                            {{ $pkg->price == 0 ? 'Free' : 'SAR ' . number_format($pkg->price, 0) }}
                        </span>
                        @if($pkg->price > 0)
                            <span class="text-muted fw-normal">/ {{ $pkg->duration_days }} days (Cash)</span>
                        @endif
                    </div>

                    <p class="text-muted small mb-3">
                        {{ $pkg->description }}
                    </p>

                    <hr class="my-3 opacity-25">

                    <ul class="list-unstyled mb-4 small">
                        <li class="mb-2 d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <strong>
                                @if($pkg->max_shops > 1)
                                    Multi-Shop: Up to {{ $pkg->max_shops }} Shops
                                @else
                                    Single Shop (1 Branch)
                                @endif
                            </strong>
                        </li>
                        <li class="mb-2 d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Duration: {{ $pkg->duration_days }} Days
                        </li>
                        @if(!empty($pkg->features) && is_array($pkg->features))
                            @foreach($pkg->features as $feat)
                                <li class="mb-2 d-flex align-items-center text-secondary">
                                    <i class="fas fa-check text-primary me-2"></i> {{ $feat }}
                                </li>
                            @endforeach
                        @endif
                        <li class="mb-2 d-flex align-items-center text-secondary">
                            <i class="fas fa-handshake text-warning me-2"></i> Offline Cash In Hand Payment
                        </li>
                    </ul>

                    <div class="mt-auto">
                        <button type="button" class="btn {{ $pkg->slug === 'yearly' ? 'btn-primary' : 'btn-outline-primary' }} w-100 py-2 shadow-sm"
                            data-bs-toggle="modal" data-bs-target="#requestRenewalModal" data-package-id="{{ $pkg->id }}" data-package-name="{{ $pkg->name }}">
                            <i class="fas fa-check-circle me-1"></i> Select & Pay Cash
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Request Cash Verification Modal -->
<div class="modal fade" id="requestRenewalModal" tabindex="-1" aria-labelledby="requestRenewalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="{{ route('subscription.request_renewal') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark" id="requestRenewalModalLabel">
                        <i class="fas fa-money-bill-wave text-success me-2"></i> Cash By Hand Payment Notice
                    </h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small">
                        Please notify Super Admin that you have handed over cash for your subscription renewal. Once received, Super Admin will approve and unlock your account.
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Select Desired Package *</label>
                        <select name="package_id" id="modalPackageSelect" class="form-select" required>
                            @foreach($packages as $pkg)
                                @if(!$pkg->isTrial())
                                    <option value="{{ $pkg->id }}" {{ $pkg->slug === 'yearly' ? 'selected' : '' }}>
                                        {{ $pkg->name }} — SAR {{ number_format($pkg->price, 0) }} ({{ $pkg->duration_days }} Days, {{ $pkg->max_shops > 1 ? $pkg->max_shops . ' Shops' : '1 Shop' }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Cash Handover Details / Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="e.g. Handed Rs/SAR cash to Super Admin Afaq at main office on 23 Oct"></textarea>
                    </div>

                    <div class="alert alert-warning py-2 px-3 small border-0 rounded-3 mb-0">
                        <i class="fas fa-info-circle me-1"></i> Payment Mode is <strong>Cash By Hand only</strong>. No online transaction required.
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-paper-plane me-1"></i> Send Cash Payment Notice
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{ asset('assets/vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modal = document.getElementById('requestRenewalModal');
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
    });
</script>
</body>
</html>
