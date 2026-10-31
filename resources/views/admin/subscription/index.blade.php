@extends('admin.layouts.app')

@section('title', 'My Subscription & Package')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Subscription & Package Details</h2>
        <p class="text-muted small mb-0">View your active plan, days remaining, multi-branch entitlement, and cash payment history.</p>
    </div>
    <button type="button" class="btn btn-primary-modern" data-bs-toggle="modal" data-bs-target="#requestUpgradeModal">
        <i class="fas fa-arrow-circle-up me-1"></i> Request Package Upgrade / Renewal
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@php
    $pkg = $shop->package;
    $daysRemaining = $shop->subscriptionDaysRemaining();
    $isExpired = $shop->isSubscriptionExpired();
@endphp

<!-- Current Plan Overview Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #ffffff 0%, #f8faff 100%); border-left: 5px solid {{ $isExpired ? '#dc3545' : '#0d6efd' }} !important;">
    <div class="card-body p-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle {{ $isExpired ? 'bg-danger bg-opacity-10 text-danger' : 'bg-primary bg-opacity-10 text-primary' }} p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                        @if($pkg && $pkg->isTrial())
                            <i class="fas fa-flask fs-4"></i>
                        @elseif($pkg && $pkg->isBusiness())
                            <i class="fas fa-building fs-4"></i>
                        @else
                            <i class="fas fa-gem fs-4"></i>
                        @endif
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h4 class="fw-bold text-dark mb-0">{{ $pkg ? $pkg->name : 'No Active Package' }}</h4>
                            @if($isExpired)
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill">
                                    <i class="fas fa-clock me-1"></i> Expired
                                </span>
                            @else
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">
                                    <i class="fas fa-check-circle me-1"></i> Active
                                </span>
                            @endif
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 rounded-pill">
                                <i class="fas fa-hand-holding-usd me-1"></i> Cash By Hand
                            </span>
                        </div>
                        <div class="text-muted small mt-1">
                            Shop: <strong>{{ $shop->name }}</strong> &bull; Currency: <strong>{{ $shop->currency_symbol }}</strong>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-sm-4">
                        <div class="p-3 bg-white rounded-3 border">
                            <div class="text-muted small">Subscription Started</div>
                            <div class="fw-bold text-dark mt-1">
                                {{ $shop->subscription_starts_at ? $shop->subscription_starts_at->format('d M, Y') : 'N/A' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="p-3 bg-white rounded-3 border">
                            <div class="text-muted small">Expiration Date</div>
                            <div class="fw-bold {{ $isExpired ? 'text-danger' : 'text-dark' }} mt-1">
                                {{ $shop->subscription_expires_at ? $shop->subscription_expires_at->format('d M, Y') : 'N/A' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="p-3 bg-white rounded-3 border">
                            <div class="text-muted small">Multi-Shop Allocation</div>
                            <div class="fw-bold text-dark mt-1">
                                @if($pkg && $pkg->max_shops > 1)
                                    {{ $shop->branches->count() + 1 }} / {{ $pkg->max_shops }} Shops
                                @else
                                    1 Shop (Single)
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 text-center text-lg-end">
                <div class="p-4 bg-light rounded-4 text-center">
                    <div class="text-muted small">Time Remaining</div>
                    <div class="display-6 fw-bold {{ $isExpired ? 'text-danger' : ($daysRemaining <= 7 ? 'text-warning' : 'text-primary') }} my-1">
                        {{ $isExpired ? '0' : $daysRemaining }}
                    </div>
                    <div class="small text-muted mb-3">Days Remaining</div>
                    <button type="button" class="btn btn-outline-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#requestUpgradeModal">
                        <i class="fas fa-sync-alt me-1"></i> Renew with Cash
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Available Packages Comparison -->
<div class="mb-4">
    <h5 class="fw-bold text-dark mb-1">Upgrade or Switch Package</h5>
    <p class="text-muted small">All payments are made directly by cash. Select your desired plan to request activation.</p>
</div>

<div class="row g-4 mb-5">
    @foreach($packages as $p)
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-4 bg-white position-relative {{ $pkg && $pkg->id === $p->id ? 'border border-2 border-primary' : '' }}">
                @if($pkg && $pkg->id === $p->id)
                    <span class="badge bg-primary position-absolute top-0 end-0 m-3 px-3 py-1 rounded-pill">
                        Current Plan
                    </span>
                @endif

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="fw-bold text-dark mb-0">{{ $p->name }}</h5>
                </div>
                <div class="mb-3">
                    <span class="fs-2 fw-bold text-dark">
                        {{ $p->price == 0 ? 'Free' : 'SAR ' . number_format($p->price, 0) }}
                    </span>
                    <span class="text-muted small">/ {{ $p->duration_days }} days (Cash)</span>
                </div>

                <p class="text-muted small mb-3">{{ $p->description }}</p>
                <hr class="my-2 opacity-25">

                <ul class="list-unstyled small mb-4">
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        <strong>{{ $p->max_shops > 1 ? 'Multi-Shop: Up to ' . $p->max_shops . ' Branches' : 'Single Shop (1 Branch)' }}</strong>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        Validity: {{ $p->duration_days }} Days
                    </li>
                    @if(!empty($p->features) && is_array($p->features))
                        @foreach($p->features as $f)
                            <li class="mb-2 text-secondary">
                                <i class="fas fa-check text-primary me-2"></i> {{ $f }}
                            </li>
                        @endforeach
                    @endif
                </ul>

                <div class="mt-auto">
                    <button type="button" class="btn {{ $pkg && $pkg->id === $p->id ? 'btn-outline-secondary disabled' : 'btn-primary-modern' }} w-100"
                        data-bs-toggle="modal" data-bs-target="#requestUpgradeModal" data-package-id="{{ $p->id }}" data-package-name="{{ $p->name }}">
                        {{ $pkg && $pkg->id === $p->id ? 'Active Plan' : 'Select & Request' }}
                    </button>
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Cash Payment History / Receipts -->
<x-card title="Cash By Hand Payment History">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="text-muted small">
                <tr>
                    <th>Date</th>
                    <th>Package Plan</th>
                    <th>Amount Paid</th>
                    <th>Status</th>
                    <th>Payment Notes</th>
                    <th>Recorded By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subscriptions as $sub)
                    <tr>
                        <td class="small">{{ $sub->created_at->format('d M, Y - h:i A') }}</td>
                        <td>
                            <span class="badge {{ $sub->package->badgeClass() }}">
                                {{ $sub->package->name }}
                            </span>
                        </td>
                        <td class="fw-bold text-success">{{ format_subscription_currency($sub->amount_paid) }}</td>
                        <td>
                            <span class="badge {{ $sub->statusBadgeClass() }}">
                                {{ ucfirst($sub->status) }}
                            </span>
                        </td>
                        <td class="small text-muted">{{ $sub->payment_notes ?? '---' }}</td>
                        <td class="small">{{ $sub->recordedBy->name ?? 'Super Admin' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            No subscription history recorded yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>

<!-- Request Upgrade Modal -->
<div class="modal fade" id="requestUpgradeModal" tabindex="-1" aria-labelledby="requestUpgradeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="{{ route('subscription.request_renewal') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark" id="requestUpgradeModalLabel">
                        <i class="fas fa-hand-holding-usd text-success me-2"></i> Offline Cash Renewal / Upgrade
                    </h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small">
                        Please select the package you wish to activate and enter any cash handover details. Once received, Super Admin will activate your new package.
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Package Plan *</label>
                        <select name="package_id" id="upgradeModalPackageSelect" class="form-select" required>
                            @foreach($packages as $p)
                                @if(!$p->isTrial())
                                    <option value="{{ $p->id }}" {{ $pkg && $pkg->id === $p->id ? 'selected' : '' }}>
                                        {{ $p->name }} — Price: SAR {{ number_format($p->price, 0) }} ({{ $p->duration_days }} Days, {{ $p->max_shops > 1 ? $p->max_shops . ' Shops' : '1 Shop' }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="e.g. Handed cash to Super Admin Afaq at the main office."></textarea>
                    </div>

                    <div class="alert alert-warning py-2 px-3 small border-0 rounded-3 mb-0">
                        <i class="fas fa-info-circle me-1"></i> Payment mode is <strong>Cash By Hand only</strong>.
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-modern px-4">
                        <i class="fas fa-paper-plane me-1"></i> Send Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var modal = document.getElementById('requestUpgradeModal');
    modal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        if (button && button.getAttribute('data-package-id')) {
            var pkgId = button.getAttribute('data-package-id');
            var select = document.getElementById('upgradeModalPackageSelect');
            if (select) {
                select.value = pkgId;
            }
        }
    });
});
</script>
@endpush
@endsection
