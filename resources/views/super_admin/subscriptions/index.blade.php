@extends('admin.layouts.app')

@section('title', 'Manage Subscriptions & Packages')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Subscriptions & Cash Collection</h2>
        <p class="text-muted small mb-0">Monitor shop plans, 1-week test accounts, cash in hand renewals, and multi-shop allocations.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('super_admin.packages.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-boxes me-1"></i> Manage Packages & Pricing
        </a>
        <a href="{{ route('super_admin.shops.create') }}" class="btn btn-primary-modern">
            <i class="fas fa-plus me-1"></i> New Shop With Package
        </a>
    </div>
</div>

<!-- Stats Summary Row -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="rounded-circle bg-success bg-opacity-10 text-success p-3 me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="fas fa-check-circle fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Active Subscriptions</div>
                    <div class="fs-4 fw-bold text-dark">{{ $activeCount }} <span class="fs-6 text-muted fw-normal">/ {{ $totalShops }}</span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="rounded-circle bg-danger bg-opacity-10 text-danger p-3 me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="fas fa-clock fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Expired Shops</div>
                    <div class="fs-4 fw-bold text-danger">{{ $expiredCount }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-3 me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="fas fa-flask fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">1-Week Test Accounts</div>
                    <div class="fs-4 fw-bold text-dark">{{ $trialCount }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="fas fa-hand-holding-usd fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Cash Collected</div>
                    <div class="fs-4 fw-bold text-primary">{{ format_subscription_currency($totalCashCollected) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($pendingRequests->isNotEmpty())
    <!-- Pending Cash In Hand Verification Alerts -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 border-start border-4 border-warning">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-2 mb-3">
                <i class="fas fa-bell text-warning fs-5"></i>
                <h5 class="fw-bold mb-0 text-dark">Pending Cash By Hand Verification Requests ({{ $pendingRequests->count() }})</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="text-muted small">
                        <tr>
                            <th>Shop</th>
                            <th>Requested Package</th>
                            <th>Amount</th>
                            <th>Requested By</th>
                            <th>Notes</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingRequests as $req)
                            <tr>
                                <td class="fw-bold text-dark">{{ $req->shop->name }}</td>
                                <td><span class="badge-soft {{ $req->package->badgeClass() }}">{{ $req->package->name }}</span></td>
                                <td class="fw-bold text-success">{{ format_subscription_currency($req->amount_paid) }}</td>
                                <td class="small">{{ $req->user->name ?? 'Admin' }}</td>
                                <td class="text-muted small">{{ $req->payment_notes }}</td>
                                <td class="text-end">
                                    <form action="{{ route('super_admin.subscriptions.approve', $req->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success px-3 shadow-none">
                                            <i class="fas fa-check me-1"></i> Cash Received & Activate
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

<!-- Filters & Search Toolbar -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form action="{{ route('super_admin.subscriptions.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Search shop name, phone, email..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="package_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All Packages</option>
                    @foreach($packages as $p)
                        <option value="{{ $p->id }}" {{ request('package_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <a href="{{ route('super_admin.subscriptions.index') }}" class="btn btn-outline-secondary {{ !request('filter') ? 'active' : '' }}">All</a>
                <a href="{{ route('super_admin.subscriptions.index', ['filter' => 'active']) }}" class="btn btn-outline-success {{ request('filter') === 'active' ? 'active' : '' }}">Active</a>
                <a href="{{ route('super_admin.subscriptions.index', ['filter' => 'trial']) }}" class="btn btn-outline-warning {{ request('filter') === 'trial' ? 'active' : '' }}">1-Wk Trial</a>
                <a href="{{ route('super_admin.subscriptions.index', ['filter' => 'expired']) }}" class="btn btn-outline-danger {{ request('filter') === 'expired' ? 'active' : '' }}">Expired</a>
            </div>
        </form>
    </div>
</div>

<!-- Shops Subscriptions List -->
<x-card :padding="false">
    <x-table>
        <x-slot name="thead">
            <th>Shop Details</th>
            <th>Account Status</th>
            <th>Current Package</th>
            <th>Subscription Validity</th>
            <th>Days Left</th>
            <th>Branches</th>
            <th class="text-end pe-4">Action</th>
        </x-slot>

        @forelse($shops as $shop)
            @php
                $isExpired = $shop->isSubscriptionExpired();
                $daysRemaining = $shop->subscriptionDaysRemaining();
                $pkg = $shop->package;
            @endphp
            <tr class="align-middle">
                <td>
                    <div class="d-flex align-items-center">
                        @if($shop->logo)
                            <img src="{{ asset($shop->logo) }}" alt="{{ $shop->name }}" class="rounded me-3 border p-1" style="width: 40px; height: 40px; object-fit: contain;">
                        @else
                            <div class="rounded bg-light text-primary d-flex align-items-center justify-content-center me-3 border" style="width: 40px; height: 40px; font-size: 1.15rem;">
                                <i class="fas fa-store"></i>
                            </div>
                        @endif
                        <div>
                            <div class="fw-bold text-dark">{{ $shop->name }}</div>
                            <div class="text-muted small">
                                <i class="fas fa-phone-alt me-1 text-muted"></i>{{ $shop->phone ?? 'No Phone' }}
                                @if($shop->city) &bull; {{ $shop->city }}@endif
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    @if($shop->isActive())
                        <span class="badge-soft badge-soft-success">Active</span>
                    @elseif($shop->isInactive())
                        <span class="badge-soft badge-soft-warning">Inactive</span>
                    @elseif($shop->isSuspended())
                        <span class="badge-soft badge-soft-danger">Suspended</span>
                    @else
                        <span class="badge-soft badge-soft-secondary">{{ ucfirst($shop->status) }}</span>
                    @endif
                </td>
                <td>
                    @if($pkg)
                        <span class="badge-soft {{ $pkg->badgeClass() }}">
                            @if($pkg->isTrial())
                                <i class="fas fa-flask me-1"></i>
                            @elseif($pkg->isBusiness())
                                <i class="fas fa-building me-1"></i>
                            @else
                                <i class="fas fa-calendar-alt me-1"></i>
                            @endif
                            {{ $pkg->name }}
                        </span>
                    @else
                        <span class="badge-soft badge-soft-secondary">No Package</span>
                    @endif
                </td>
                <td>
                    @if($shop->subscription_expires_at)
                        <div class="small fw-bold text-dark">
                            {{ $shop->subscription_expires_at->format('d-M-Y') }}
                        </div>
                        <div class="text-muted small">
                            Started: {{ $shop->subscription_starts_at ? $shop->subscription_starts_at->format('d-M-Y') : 'N/A' }}
                        </div>
                    @else
                        <span class="text-muted small">Not set</span>
                    @endif
                </td>
                <td>
                    @if($isExpired)
                        <span class="badge-soft badge-soft-danger">
                            <i class="fas fa-exclamation-triangle me-1"></i> Expired ({{ abs($daysRemaining) }}d)
                        </span>
                    @else
                        @if($daysRemaining <= 7)
                            <span class="badge-soft badge-soft-warning">
                                <i class="fas fa-hourglass-half me-1"></i> {{ $daysRemaining }} days left
                            </span>
                        @else
                            <span class="badge-soft badge-soft-success">
                                <i class="fas fa-check me-1"></i> {{ $daysRemaining }} days left
                            </span>
                        @endif
                    @endif
                </td>
                <td>
                    @if($pkg && $pkg->max_shops > 1)
                        <span class="badge-soft badge-soft-info">
                            <i class="fas fa-code-branch me-1"></i> Up to {{ $pkg->max_shops }} Shops
                        </span>
                    @else
                        <span class="text-muted small">Single Shop (1)</span>
                    @endif
                </td>
                <td class="text-end pe-4">
                    <div class="d-flex align-items-center justify-content-end gap-2">
                        <button type="button" class="btn btn-sm btn-primary-modern shadow-none btn-assign-modal text-nowrap" 
                            data-bs-toggle="modal" 
                            data-bs-target="#assignPackageModal"
                            data-shop-id="{{ $shop->id }}"
                            data-shop-name="{{ $shop->name }}"
                            data-shop-status="{{ $shop->status }}"
                            data-current-package-id="{{ $shop->package_id }}"
                            data-current-expiry="{{ $shop->subscription_expires_at ? $shop->subscription_expires_at->format('d-M-Y') : 'Not Set' }}"
                            data-current-expiry-raw="{{ $shop->subscription_expires_at ? $shop->subscription_expires_at->format('Y-m-d') : '' }}"
                            data-days-remaining="{{ $daysRemaining }}"
                            data-is-expired="{{ $isExpired ? '1' : '0' }}">
                            <i class="fas fa-money-bill-wave me-1"></i> Renew / Assign Cash
                        </button>

                        <!-- Admin Action Dropdown for Status Change (Inactive / Suspend / Reactivate) -->
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm rounded-circle shadow-none" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="More Actions">
                                <i class="fas fa-ellipsis-v text-muted"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li>
                                    <button type="button" class="dropdown-item py-2 btn-assign-modal" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#assignPackageModal"
                                        data-shop-id="{{ $shop->id }}"
                                        data-shop-name="{{ $shop->name }}"
                                        data-shop-status="{{ $shop->status }}"
                                        data-current-package-id="{{ $shop->package_id }}"
                                        data-current-expiry="{{ $shop->subscription_expires_at ? $shop->subscription_expires_at->format('d-M-Y') : 'Not Set' }}"
                                        data-current-expiry-raw="{{ $shop->subscription_expires_at ? $shop->subscription_expires_at->format('Y-m-d') : '' }}"
                                        data-days-remaining="{{ $daysRemaining }}"
                                        data-is-expired="{{ $isExpired ? '1' : '0' }}">
                                        <i class="fas fa-hand-holding-usd me-2 text-primary"></i> Renew / Assign Cash
                                    </button>
                                </li>
                                <li><hr class="dropdown-divider"></li>

                                @if($shop->isActive())
                                    <li>
                                        <form action="{{ route('super_admin.subscriptions.shop_status', $shop->id) }}" method="POST" class="shop-status-form" data-action-label="Deactivate this shop (Set Inactive)">
                                            @csrf
                                            <input type="hidden" name="status" value="inactive">
                                            <button type="submit" class="dropdown-item py-2 text-warning">
                                                <i class="fas fa-pause-circle me-2"></i> Set Inactive (Payment Issue)
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form action="{{ route('super_admin.subscriptions.shop_status', $shop->id) }}" method="POST" class="shop-status-form" data-action-label="Suspend this shop (Block all access)">
                                            @csrf
                                            <input type="hidden" name="status" value="suspended">
                                            <button type="submit" class="dropdown-item py-2 text-danger">
                                                <i class="fas fa-ban me-2"></i> Suspend Shop
                                            </button>
                                        </form>
                                    </li>
                                @elseif($shop->isInactive())
                                    <li>
                                        <form action="{{ route('super_admin.subscriptions.shop_status', $shop->id) }}" method="POST" class="shop-status-form" data-action-label="Re-activate this shop">
                                            @csrf
                                            <input type="hidden" name="status" value="active">
                                            <button type="submit" class="dropdown-item py-2 text-success">
                                                <i class="fas fa-play-circle me-2"></i> Re-Activate Shop
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form action="{{ route('super_admin.subscriptions.shop_status', $shop->id) }}" method="POST" class="shop-status-form" data-action-label="Suspend this shop">
                                            @csrf
                                            <input type="hidden" name="status" value="suspended">
                                            <button type="submit" class="dropdown-item py-2 text-danger">
                                                <i class="fas fa-ban me-2"></i> Permanently Suspend
                                            </button>
                                        </form>
                                    </li>
                                @else
                                    <li>
                                        <form action="{{ route('super_admin.subscriptions.shop_status', $shop->id) }}" method="POST" class="shop-status-form" data-action-label="Lift suspension and re-activate this shop">
                                            @csrf
                                            <input type="hidden" name="status" value="active">
                                            <button type="submit" class="dropdown-item py-2 text-success">
                                                <i class="fas fa-undo me-2"></i> Lift Suspension (Re-Activate)
                                            </button>
                                        </form>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center py-5 text-muted">
                    <i class="fas fa-store-slash fa-2x mb-3 text-secondary"></i>
                    <div>No shops found.</div>
                </td>
            </tr>
        @endforelse
    </x-table>

    @if($shops->hasPages())
        <div class="card-footer bg-transparent border-top p-4">
            {{ $shops->links('pagination::bootstrap-5') }}
        </div>
    @endif
</x-card>

<!-- Compact Assign / Renew Package Modal (Fits screen without scrolling) -->
<div class="modal fade" id="assignPackageModal" tabindex="-1" aria-labelledby="assignPackageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <form id="assignPackageForm" action="" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="assignPackageModalLabel">
                            <i class="fas fa-hand-holding-usd text-primary me-2"></i> Renew / Assign Package
                        </h5>
                        <div class="small text-muted mt-1">
                            Shop: <strong id="modalShopNameDisplay" class="text-dark">--</strong> &bull; <span id="modalShopStatusBadge"></span>
                        </div>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4 pt-3">
                    <!-- Package Select -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-1">Select Package <span class="text-danger">*</span></label>
                        <select name="package_id" id="packageSelect" class="form-select form-select-sm" required>
                            @foreach($packages as $p)
                                <option value="{{ $p->id }}" 
                                    data-name="{{ $p->name }}"
                                    data-price="{{ $p->price }}" 
                                    data-days="{{ $p->duration_days }}" 
                                    data-max-shops="{{ $p->max_shops }}">
                                    {{ $p->name }} — SAR {{ number_format($p->price, 0) }} ({{ $p->duration_days }} Days)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Duration & Cash Amount in 1 row -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark small mb-1">Duration (Days) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_days" id="durationDaysInput" class="form-control form-control-sm" value="365" min="1" max="3650" required>
                            <div class="d-flex gap-1 mt-1">
                                <span class="text-muted" style="font-size: 0.7rem;">Quick:</span>
                                <a href="javascript:void(0)" class="btn-day-preset text-decoration-none text-primary" data-days="7" style="font-size: 0.72rem;">7d</a>
                                <span class="text-muted" style="font-size: 0.7rem;">|</span>
                                <a href="javascript:void(0)" class="btn-day-preset text-decoration-none text-primary" data-days="30" style="font-size: 0.72rem;">30d</a>
                                <span class="text-muted" style="font-size: 0.7rem;">|</span>
                                <a href="javascript:void(0)" class="btn-day-preset text-decoration-none text-primary" data-days="365" style="font-size: 0.72rem;">365d</a>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark small mb-1">Cash Amount (SAR) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="amount_paid" id="cashAmountInput" class="form-control form-control-sm fw-bold text-success" value="500" min="0" required>
                            <div class="text-muted mt-1" style="font-size: 0.7rem;">0 for Free Test Trial, or SAR collected</div>
                        </div>
                    </div>

                    <!-- Start Date & Live Expiry preview in 1 row -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark small mb-1">Start From</label>
                            <select name="extend_from" id="extendFromSelect" class="form-select form-select-sm">
                                <option value="today">Today (Reset Expiry)</option>
                                <option value="current_expiry" id="optExtendCurrent" selected>Current Expiry (Extend)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark small mb-1">Calculated New Expiry</label>
                            <div class="form-control form-control-sm bg-light fw-bold text-success" id="calculatedExpiryDisplay">--</div>
                        </div>
                    </div>

                    <!-- Shop Account Status Selection -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-1">Shop Status After Save</label>
                        <select name="shop_status" id="shopStatusSelect" class="form-select form-select-sm">
                            <option value="active" selected>Active (Normal Access)</option>
                            <option value="inactive">Inactive (Payment Due / Warning)</option>
                            <option value="suspended">Suspended (Blocked Access)</option>
                        </select>
                    </div>

                    <!-- Payment Memo Notes (Single row compact input) -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-1">Payment Notes (Optional)</label>
                        <input type="text" name="payment_notes" id="paymentNotesInput" class="form-control form-control-sm" placeholder="e.g. Received SAR 500 cash by hand">
                    </div>

                    <!-- Compact notice -->
                    <div class="text-muted d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                        <i class="fas fa-shield-alt text-primary"></i>
                        <span>Instantly saves package & logs cash record in system.</span>
                    </div>
                </div>

                <div class="modal-footer border-0 p-4 pt-0 d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-link text-muted text-decoration-none" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="btnSubmitAssign" class="btn btn-primary-modern px-4">
                        <i class="fas fa-check-circle me-1"></i> Save & Activate Shop
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    var activeShop = {
        id: '',
        name: '',
        status: 'active',
        pkgId: '',
        expiry: '',
        expiryRaw: '',
        daysRemaining: 0,
        isExpired: false
    };

    function recalculateExpiry() {
        var extendSelect = document.getElementById('extendFromSelect');
        var durationInput = document.getElementById('durationDaysInput');
        var display = document.getElementById('calculatedExpiryDisplay');

        if (!extendSelect || !durationInput || !display) return;

        var days = parseInt(durationInput.value, 10);
        if (isNaN(days) || days < 1) days = 0;

        var baseDate = new Date();

        // If extending from current future expiry
        if (extendSelect.value === 'current_expiry' && activeShop.expiryRaw && !activeShop.isExpired) {
            var parts = activeShop.expiryRaw.split('-');
            if (parts.length === 3) {
                baseDate = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
            }
        }

        var resultDate = new Date(baseDate.getTime());
        resultDate.setDate(resultDate.getDate() + days);

        var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        var day = String(resultDate.getDate()).padStart(2, '0');
        var month = months[resultDate.getMonth()];
        var year = resultDate.getFullYear();

        display.textContent = day + '-' + month + '-' + year;
    }

    function updatePackageDefaults() {
        var pkgSelect = document.getElementById('packageSelect');
        var durationInput = document.getElementById('durationDaysInput');
        var cashInput = document.getElementById('cashAmountInput');

        if (!pkgSelect) return;
        var opt = pkgSelect.options[pkgSelect.selectedIndex];
        if (!opt) return;

        var price = opt.getAttribute('data-price') || '0';
        var days = opt.getAttribute('data-days') || '365';

        if (durationInput) durationInput.value = days;
        if (cashInput) cashInput.value = price;

        recalculateExpiry();
    }

    function setupModal(triggerBtn) {
        if (!triggerBtn) return;

        activeShop.id = triggerBtn.getAttribute('data-shop-id') || '';
        activeShop.name = triggerBtn.getAttribute('data-shop-name') || 'Shop';
        activeShop.status = triggerBtn.getAttribute('data-shop-status') || 'active';
        activeShop.pkgId = triggerBtn.getAttribute('data-current-package-id') || '';
        activeShop.expiry = triggerBtn.getAttribute('data-current-expiry') || '';
        activeShop.expiryRaw = triggerBtn.getAttribute('data-current-expiry-raw') || '';
        activeShop.daysRemaining = parseInt(triggerBtn.getAttribute('data-days-remaining') || '0', 10);
        activeShop.isExpired = triggerBtn.getAttribute('data-is-expired') === '1';

        // Update form action URL
        var form = document.getElementById('assignPackageForm');
        if (form && activeShop.id) {
            form.action = "{{ url('super-admin/subscriptions') }}/" + activeShop.id + "/assign";
        }

        // Reset submit button state
        var submitBtn = document.getElementById('btnSubmitAssign');
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-check-circle me-1"></i> Save & Activate Shop';
        }

        // Header Shop Display
        var nameDisplay = document.getElementById('modalShopNameDisplay');
        if (nameDisplay) nameDisplay.textContent = activeShop.name;

        var statusBadge = document.getElementById('modalShopStatusBadge');
        if (statusBadge) {
            if (activeShop.status === 'active') {
                statusBadge.innerHTML = '<span class="badge-soft badge-soft-success">Active</span>';
            } else if (activeShop.status === 'inactive') {
                statusBadge.innerHTML = '<span class="badge-soft badge-soft-warning">Inactive</span>';
            } else if (activeShop.status === 'suspended') {
                statusBadge.innerHTML = '<span class="badge-soft badge-soft-danger">Suspended</span>';
            } else {
                statusBadge.innerHTML = '<span class="badge-soft badge-soft-secondary">' + activeShop.status + '</span>';
            }
        }

        // Set Shop Status Dropdown
        var statusSelect = document.getElementById('shopStatusSelect');
        if (statusSelect) {
            statusSelect.value = activeShop.status || 'active';
        }

        // Configure Extend Options
        var extendSelect = document.getElementById('extendFromSelect');
        var optExtendCurrent = document.getElementById('optExtendCurrent');
        if (extendSelect && optExtendCurrent) {
            if (activeShop.isExpired || !activeShop.expiryRaw) {
                optExtendCurrent.disabled = true;
                optExtendCurrent.textContent = 'Current Expiry (Expired)';
                extendSelect.value = 'today';
            } else {
                optExtendCurrent.disabled = false;
                optExtendCurrent.textContent = 'Current Expiry (Extend)';
                extendSelect.value = 'current_expiry';
            }
        }

        // Set Package
        var pkgSelect = document.getElementById('packageSelect');
        if (pkgSelect) {
            if (activeShop.pkgId) {
                pkgSelect.value = activeShop.pkgId;
            } else if (pkgSelect.options.length > 0) {
                pkgSelect.selectedIndex = 0;
            }
        }

        updatePackageDefaults();
    }

    // Attach delegated events
    document.addEventListener('click', function(e) {
        // Quick Day Preset links
        var presetLink = e.target.closest('.btn-day-preset');
        if (presetLink) {
            e.preventDefault();
            var days = presetLink.getAttribute('data-days');
            var durationInput = document.getElementById('durationDaysInput');
            if (durationInput && days) {
                durationInput.value = days;
                recalculateExpiry();
            }
            return;
        }

        // Assign button clicked
        var assignBtn = e.target.closest('.btn-assign-modal, [data-bs-target="#assignPackageModal"]');
        if (assignBtn) {
            setupModal(assignBtn);
        }
    });

    document.addEventListener('show.bs.modal', function(event) {
        if (event.target && event.target.id === 'assignPackageModal') {
            if (event.relatedTarget) {
                setupModal(event.relatedTarget);
            }
        }
    });

    document.addEventListener('change', function(e) {
        if (e.target && e.target.id === 'packageSelect') {
            updatePackageDefaults();
        } else if (e.target && (e.target.id === 'extendFromSelect' || e.target.id === 'durationDaysInput')) {
            recalculateExpiry();
        }
    });

    document.addEventListener('input', function(e) {
        if (e.target && e.target.id === 'durationDaysInput') {
            recalculateExpiry();
        }
    });

    // Form submit verification
    var form = document.getElementById('assignPackageForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!form.action || form.action.endsWith('/subscriptions') || form.action.endsWith('/subscriptions/')) {
                e.preventDefault();
                alert('Shop not detected. Please close and re-open the popup.');
                return false;
            }
        });
    }

    // Confirm shop status changes (Inactive / Suspend / Reactivate)
    document.addEventListener('submit', function(e) {
        var statusForm = e.target.closest('.shop-status-form');
        if (statusForm && !statusForm.dataset.confirmed) {
            e.preventDefault();
            var actionLabel = statusForm.getAttribute('data-action-label') || 'change status for this shop';
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to ' + actionLabel + '?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3b82f6',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, proceed'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        statusForm.dataset.confirmed = 'true';
                        statusForm.submit();
                    }
                });
            } else {
                if (confirm('Are you sure you want to ' + actionLabel + '?')) {
                    statusForm.dataset.confirmed = 'true';
                    statusForm.submit();
                }
            }
        }
    });
})();
</script>
@endpush
@endsection
