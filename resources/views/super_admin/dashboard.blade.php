@extends('admin.layouts.app')

@section('title', __('Super Admin Portal'))

@section('content')
<!-- Header Section -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">{{ __('Super Admin Portal') }}</h2>
        <p class="text-muted small mb-0">{{ __('Multi-shop overview, global revenue tracking, and centralized branch management.') }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('super_admin.users.index') }}" class="btn btn-outline-secondary d-flex align-items-center">
            <i class="fas fa-users-cog me-2"></i>{{ __('Users') }}
        </a>
        <a href="{{ route('super_admin.shops.create') }}" class="btn btn-primary-modern d-flex align-items-center">
            <i class="fas fa-plus me-2"></i> {{ __('Add New Shop') }}
        </a>
    </div>
</div>

<!-- Active Shop Context Notice -->
@if(session('active_shop_id') && session('active_shop_id') !== 'all')
    @php $activeS = \App\Models\Shop::find(session('active_shop_id')); @endphp
    @if($activeS)
        <div class="card-modern mb-4 p-3 border-start border-4 border-primary">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 me-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="fas fa-store-alt fa-lg"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark">{{ __('Currently Switched To:') }} {{ $activeS->name }}</div>
                        <div class="text-muted small">{{ __('Sidebar modules (Quotes, Invoices, Job Orders, etc.) are currently scoped to this shop.') }}</div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-primary-modern d-flex align-items-center">
                        <i class="fas fa-chart-line me-1"></i> {{ __('Shop Dashboard') }}
                    </a>
                    <form action="{{ route('super_admin.switch_shop_all') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-globe me-1"></i> {{ __('Reset to All Shops') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endif

<!-- Admin-Themed KPI Cards (5 in 1 row) -->
<div class="row g-3 mb-4 kpi-row-5">
    <!-- {{ __('Total Shops') }} -->
    <div class="col-12 col-sm-6 col-md-4 col-xl-custom kpi-col-5">
        <div class="card-modern kpi-card kpi-card-sm bg-gradient-indigo h-100">
            <div class="icon-box">
                <i class="fas fa-store"></i>
            </div>
            <h6 class="text-white opacity-75 text-uppercase fw-semibold mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">{{ __('Total Shops') }}</h6>
            <h2 class="mb-0 fw-bold text-white kpi-value" style="color: #ffffff !important; font-weight: 800; font-size: 1.6rem;">{{ $totalShops }}</h2>
            <div class="text-white opacity-75 extra-small mt-1" style="font-size: 0.75rem;">
                {{ $activeShops }} {{ __('Active') }} @if($inactiveShops > 0)&bull; {{ $inactiveShops }} {{ __('Inactive') }} @endif @if(isset($suspendedShops) && $suspendedShops > 0)&bull; {{ $suspendedShops }} {{ __('Suspended') }} @endif
            </div>
        </div>
    </div>

    <!-- Total Collected Revenue -->
    <div class="col-12 col-sm-6 col-md-4 col-xl-custom kpi-col-5">
        <div class="card-modern kpi-card kpi-card-sm bg-gradient-emerald h-100">
            <div class="icon-box">
                <i class="fas fa-wallet"></i>
            </div>
            <h6 class="text-white opacity-75 text-uppercase fw-semibold mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">{{ __('Total Revenue') }}</h6>
            <h2 class="mb-0 fw-bold text-white kpi-value" style="color: #ffffff !important; font-weight: 800; font-size: 1.6rem;">${{ number_format($totalRevenue ?? 0, 2) }}</h2>
            <div class="text-white opacity-75 extra-small mt-1" style="font-size: 0.75rem;">{{ __('Across all branches') }}</div>
        </div>
    </div>

    <!-- Total Receivable / Due -->
    <div class="col-12 col-sm-6 col-md-4 col-xl-custom kpi-col-5">
        <div class="card-modern kpi-card kpi-card-sm bg-gradient-rose h-100">
            <div class="icon-box">
                <i class="fas fa-clock"></i>
            </div>
            <h6 class="text-white opacity-75 text-uppercase fw-semibold mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">{{ __('Pending Due') }}</h6>
            <h2 class="mb-0 fw-bold text-white kpi-value" style="color: #ffffff !important; font-weight: 800; font-size: 1.6rem;">${{ number_format($totalReceivable ?? 0, 2) }}</h2>
            <div class="text-white opacity-75 extra-small mt-1" style="font-size: 0.75rem;">{{ __('Unpaid balances') }}</div>
        </div>
    </div>

    <!-- Quotes & Job Orders -->
    <div class="col-12 col-sm-6 col-md-4 col-xl-custom kpi-col-5">
        <div class="card-modern kpi-card kpi-card-sm bg-gradient-cyan h-100">
            <div class="icon-box">
                <i class="fas fa-tasks"></i>
            </div>
            <h6 class="text-white opacity-75 text-uppercase fw-semibold mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">{{ __('Quotes & Orders') }}</h6>
            <h2 class="mb-0 fw-bold text-white kpi-value" style="color: #ffffff !important; font-weight: 800; font-size: 1.6rem;">{{ $totalQuotes }} <span class="fs-6 fw-normal text-white" style="opacity: 0.7;">/ {{ $totalJobOrders }}</span></h2>
            <div class="text-white opacity-75 extra-small mt-1" style="font-size: 0.75rem;">{{ $totalInvoices }} {{ __('Invoices Generated') }}</div>
        </div>
    </div>

    <!-- Users & Clients -->
    <div class="col-12 col-sm-6 col-md-4 col-xl-custom kpi-col-5">
        <div class="card-modern kpi-card kpi-card-sm bg-gradient-purple h-100">
            <div class="icon-box">
                <i class="fas fa-users"></i>
            </div>
            <h6 class="text-white opacity-75 text-uppercase fw-semibold mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">{{ __('Users & Clients') }}</h6>
            <h2 class="mb-0 fw-bold text-white kpi-value" style="color: #ffffff !important; font-weight: 800; font-size: 1.6rem;">{{ $totalUsers }}</h2>
            <div class="text-white opacity-75 extra-small mt-1" style="font-size: 0.75rem;">{{ $totalCustomers }} {{ __('Registered Customers') }}</div>
        </div>
    </div>
</div>

<!-- Shops Overview Table using x-card and x-table -->
<x-card :title="__('Registered Shops & Branches')" :padding="false">
    <x-slot name="headerActions">
        <a href="{{ route('super_admin.shops.index') }}" class="btn btn-sm btn-link text-decoration-none fw-medium">{{ __('View All Shops') }}</a>
    </x-slot>

    <x-table>
        <x-slot name="thead">
            <th>{{ __('Shop / Branch') }}</th>
            <th>{{ __('Primary Admin') }}</th>
            <th>{{ __('Quotes / Orders') }}</th>
            <th>{{ __('Invoices') }}</th>
            <th>{{ __('Collected Revenue') }}</th>
            <th>{{ __('Status') }}</th>
            <th class="text-end pe-4">{{ __('Action') }}</th>
        </x-slot>

        @forelse($shops as $shop)
        <tr class="align-middle">
            <td>
                <div class="d-flex align-items-center">
                    @if($shop->logo)
                        <img src="{{ asset($shop->logo) }}" alt="{{ $shop->name }}" class="rounded me-3 border p-1" style="width: 42px; height: 42px; object-fit: contain;">
                    @else
                        <div class="rounded bg-light text-primary d-flex align-items-center justify-content-center me-3 border" style="width: 42px; height: 42px; font-size: 1.15rem;">
                            <i class="fas fa-store"></i>
                        </div>
                    @endif
                    <div>
                        <a href="{{ route('super_admin.shops.edit', $shop->id) }}" class="fw-bold text-dark text-decoration-none d-block">
                            {{ $shop->name }}
                        </a>
                        <span class="text-muted small">
                            <i class="fas fa-map-marker-alt me-1 text-danger"></i>{{ $shop->city ?? __('Main Location') }} 
                            @if($shop->currency_symbol) &bull; <strong>{{ $shop->currency_symbol }}</strong>@endif
                        </span>
                    </div>
                </div>
            </td>
            <td>
                @php $admin = $shop->admins->first(); @endphp
                @if($admin)
                    <div class="fw-semibold text-dark">{{ $admin->name }}</div>
                    <div class="text-muted small">{{ $admin->email }}</div>
                @else
                    <span class="badge-soft badge-soft-secondary">{{ __('No Admin') }}</span>
                @endif
            </td>
            <td>
                <span class="badge-soft badge-soft-primary">
                    <i class="fas fa-file-invoice me-1"></i>{{ $shop->quotes_count }} {{ __('Quotes') }}
                </span>
                <span class="badge-soft badge-soft-info ms-1">
                    <i class="fas fa-tasks me-1"></i>{{ $shop->jobOrders_count ?? $shop->job_orders_count }} {{ __('Orders') }}
                </span>
            </td>
            <td>
                <span class="fw-bold text-dark">{{ $shop->invoices_count }}</span>
                <div class="text-muted small">{{ __('Invoices') }}</div>
            </td>
            <td>
                <div class="fw-bold text-success">
                    {{ $shop->currency_symbol ?? '$' }} {{ number_format($shop->total_revenue ?? 0, 2) }}
                </div>
                @if(($shop->total_balance_due ?? 0) > 0)
                <div class="text-danger extra-small">
                    {{ __('Due') }}: {{ $shop->currency_symbol ?? '$' }} {{ number_format($shop->total_balance_due ?? 0, 2) }}
                </div>
                @endif
            </td>
            <td>
                @if($shop->isActive())
                    <span class="badge-soft badge-soft-success">
                        <i class="fas fa-check-circle me-1"></i> {{ __('Active') }}
                    </span>
                @elseif($shop->isInactive())
                    <div>
                        <span class="badge-soft badge-soft-warning">
                            <i class="fas fa-pause-circle me-1"></i> {{ __('Inactive') }}
                        </span>
                        @if($shop->status_reason)
                            <div class="text-muted extra-small mt-1 text-truncate" style="max-width: 170px;" title="{{ $shop->status_reason }}">
                                <i class="fas fa-exclamation-circle text-warning me-1"></i>{{ $shop->status_reason }}
                            </div>
                        @endif
                        @if($shop->action_deadline)
                            @php
                                $daysRemaining = (int) now()->startOfDay()->diffInDays($shop->action_deadline->startOfDay(), false);
                            @endphp
                            <div class="extra-small {{ $daysRemaining <= 2 ? 'text-danger fw-bold' : 'text-muted' }}">
                                <i class="fas fa-clock me-1"></i>Due: {{ $shop->action_deadline->format('d M') }} ({{ max(0, $daysRemaining) }}d left)
                            </div>
                        @endif
                    </div>
                @else
                    <div>
                        <span class="badge-soft badge-soft-danger">
                            <i class="fas fa-ban me-1"></i> {{ __('Suspended') }}
                        </span>
                        @if($shop->status_reason)
                            <div class="text-danger extra-small mt-1 text-truncate" style="max-width: 170px;" title="{{ $shop->status_reason }}">
                                {{ $shop->status_reason }}
                            </div>
                        @endif
                    </div>
                @endif
            </td>
            <td class="text-end pe-4">
                <div class="d-inline-flex align-items-center gap-2">
                    <!-- Quick Enter Shop Button -->
                    <form action="{{ route('super_admin.switch_shop', $shop->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-primary-modern" title="{{ __('Enter / Switch to this Shop') }}">
                            <i class="fas fa-sign-in-alt me-1"></i> {{ __('Enter Shop') }}
                        </button>
                    </form>

                    <!-- Admin-Style 3-Dots Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-light btn-sm rounded-circle shadow-none" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v text-muted"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('super_admin.shops.edit', $shop->id) }}">
                                    <i class="fas fa-edit me-2 text-info"></i> {{ __('Edit Shop Details') }}
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('super_admin.users.index') }}?shop_id={{ $shop->id }}">
                                    <i class="fas fa-users me-2 text-primary"></i> {{ __('View Shop Users') }}
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>

                            @if($shop->isActive())
                                <li>
                                    <button type="button" class="dropdown-item py-2 text-warning" onclick="openStatusModal({{ $shop->id }}, '{{ addslashes($shop->name) }}', 'inactive', '{{ addslashes($shop->status_reason ?? '') }}', {{ $shop->grace_period_days ?? 7 }}, '{{ $shop->action_deadline ? $shop->action_deadline->format('Y-m-d') : '' }}', '{{ addslashes($shop->status_message ?? '') }}')">
                                        <i class="fas fa-pause-circle me-2"></i> {{ __('Set Inactive (Payment Issue)') }}
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item py-2 text-danger" onclick="openStatusModal({{ $shop->id }}, '{{ addslashes($shop->name) }}', 'suspended', '{{ addslashes($shop->status_reason ?? '') }}')">
                                        <i class="fas fa-ban me-2"></i> {{ __('Suspend Shop') }}
                                    </button>
                                </li>
                            @elseif($shop->isInactive())
                                <li>
                                    <button type="button" class="dropdown-item py-2 text-success" onclick="openStatusModal({{ $shop->id }}, '{{ addslashes($shop->name) }}', 'active')">
                                        <i class="fas fa-play-circle me-2"></i> {{ __('Re-Activate Shop') }}
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item py-2 text-info" onclick="openStatusModal({{ $shop->id }}, '{{ addslashes($shop->name) }}', 'inactive', '{{ addslashes($shop->status_reason ?? '') }}', {{ $shop->grace_period_days ?? 7 }}, '{{ $shop->action_deadline ? $shop->action_deadline->format('Y-m-d') : '' }}', '{{ addslashes($shop->status_message ?? '') }}')">
                                        <i class="fas fa-envelope me-2"></i> {{ __('Update Reason / Resend Notice') }}
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item py-2 text-danger" onclick="openStatusModal({{ $shop->id }}, '{{ addslashes($shop->name) }}', 'suspended')">
                                        <i class="fas fa-ban me-2"></i> {{ __('Permanently Suspend') }}
                                    </button>
                                </li>
                            @else
                                <li>
                                    <button type="button" class="dropdown-item py-2 text-success" onclick="openStatusModal({{ $shop->id }}, '{{ addslashes($shop->name) }}', 'active')">
                                        <i class="fas fa-redo me-2"></i> {{ __('Lift Suspension (Re-Activate)') }}
                                    </button>
                                </li>
                            @endif

                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('super_admin.shops.destroy', $shop->id) }}" method="POST" class="d-inline delete-form" data-item-name="{{ $shop->name }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item py-2 text-danger">
                                        <i class="fas fa-trash-alt me-2"></i> {{ __('Delete Shop') }}
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center py-5 text-muted">
                <i class="fas fa-store-slash fa-3x mb-3 text-secondary opacity-50 d-block"></i>
                {{ __('No shops registered yet. Click ":add" to get started!', ['add' => __('Add New Shop')]) }}
            </td>
        </tr>
        @endforelse
    </x-table>
</x-card>

@include('super_admin.shops._status_modal')
@endsection

@section('scripts')
<script>
    // SweetAlert2 for Delete Form
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const itemName = this.dataset.itemName || 'this shop';
            Swal.fire({
                title: 'Are you sure?',
                text: `Do you really want to delete "${itemName}"? This will move it to trash.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // SweetAlert2 for Toggle Status Form
    document.querySelectorAll('.toggle-status-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const shopName = this.dataset.shopName;
            const action = this.dataset.action;
            const isDeactivate = action === 'deactivate';

            Swal.fire({
                title: isDeactivate ? 'Deactivate Shop?' : 'Activate Shop?',
                text: isDeactivate 
                    ? `Staff and admins of "${shopName}" will not be able to log in while deactivated.` 
                    : `"${shopName}" will be activated and accessible immediately.`,
                icon: isDeactivate ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonColor: isDeactivate ? '#f59e0b' : '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: isDeactivate ? 'Yes, deactivate' : 'Yes, activate',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endsection
