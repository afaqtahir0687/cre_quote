@extends('admin.layouts.app')

@section('title', __('Dashboard'))

@section('content')
<!-- Active Shop Branding Banner -->
@php
    $dashLogo = shop_logo_url($currentShop, $setting);
    $dashTitle = shop_brand_name($currentShop, $setting);
    $isSuper = auth()->check() && auth()->user()->isSuperAdmin();
    $isSwitched = session('active_shop_id') && session('active_shop_id') !== 'all';
@endphp

<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #ffffff 0%, #f4f7fc 100%); border-left: 4px solid var(--erp-primary) !important;">
    <div class="card-body p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white p-2 rounded-3 shadow-sm border d-flex align-items-center justify-content-center" style="width: 68px; height: 68px; min-width: 68px;">
                    @if($dashLogo)
                        <img src="{{ $dashLogo }}" alt="{{ $dashTitle }}" class="img-fluid" style="max-height: 52px; max-width: 52px; object-fit: contain;">
                    @else
                        <div class="w-100 h-100 rounded text-white d-flex align-items-center justify-content-center fs-3" style="background: var(--erp-primary);">
                            <i class="fas fa-store"></i>
                        </div>
                    @endif
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h4 class="fw-bold text-dark mb-0">{{ $dashTitle }}</h4>
                        <span class="badge-soft badge-soft-success">
                            <i class="fas fa-circle me-1" style="font-size: 0.45rem;"></i> {{ __('Active Shop') }}
                        </span>
                        @if($currentShop)
                            <span class="badge bg-light text-secondary border px-2 py-1 rounded-pill small">
                                <i class="fas fa-store text-primary me-1"></i>{{ __('Branch') }} #{{ $currentShop->id }}
                            </span>
                            @if($currentShop->package)
                                <a href="{{ route('subscription.index') }}" class="text-decoration-none">
                                    <span class="badge {{ $currentShop->package->badgeClass() }} border px-2 py-1 rounded-pill small">
                                        <i class="fas fa-gem me-1"></i> {{ $currentShop->package->name }}
                                        &bull; {{ $currentShop->subscriptionDaysRemaining() }}d left
                                    </span>
                                </a>
                            @endif
                        @endif
                        @if($isSuper && $isSwitched)
                            <span class="badge-soft badge-soft-warning">
                                <i class="fas fa-exchange-alt me-1"></i>{{ __('Switched View') }}
                            </span>
                        @endif
                    </div>
                    <p class="text-muted small mb-0 mt-1 d-flex align-items-center gap-3 flex-wrap">
                        @if($currentShop?->city || $setting?->company_city)
                            <span><i class="fas fa-map-marker-alt text-primary me-1"></i> {{ $currentShop->city ?? $setting->company_city }}, {{ $currentShop->country ?? $setting->company_country ?? 'Saudi Arabia' }}</span>
                        @endif
                        @if($currentShop?->phone || $setting?->company_phone)
                            <span><i class="fas fa-phone-alt text-primary me-1"></i> {{ $currentShop->phone ?? $setting->company_phone }}</span>
                        @endif
                        <span><i class="fas fa-coins text-warning me-1"></i> {{ __('Currency') }}: <strong>{{ $currentShop->currency_symbol ?? $setting->currency_symbol ?? '$' }}</strong></span>
                    </p>
                </div>
            </div>

            <!-- ERP Quick Action Shortcuts -->
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('job-orders.create') }}" class="btn btn-primary-modern btn-sm">
                    <i class="fas fa-plus"></i> {{ __('New Job Order') }}
                </a>
                <a href="{{ route('quotes.create') }}" class="btn btn-secondary-modern btn-sm">
                    <i class="fas fa-file-invoice"></i> {{ __('New Quote') }}
                </a>
                <a href="{{ route('invoices.create') }}" class="btn btn-secondary-modern btn-sm">
                    <i class="fas fa-receipt"></i> {{ __('New Invoice') }}
                </a>
            </div>
        </div>
    </div>
</div>

<!-- 5-Col ERPGo KPI Cards -->
<div class="row g-3 mb-4 kpi-row-5">
    <!-- Total Customers -->
    <div class="col-12 col-sm-6 col-md-4 col-xl-custom kpi-col-5">
        <div class="kpi-card bg-gradient-indigo h-100">
            <div class="icon-box">
                <i class="fas fa-users"></i>
            </div>
            <div class="kpi-title">{{ __('Total Customers') }}</div>
            <h2 class="kpi-value">{{ $stats['customers_count'] }}</h2>
            <div class="small opacity-75 mt-2">
                <i class="fas fa-check-circle me-1"></i> {{ __('Active Clients') }}
            </div>
        </div>
    </div>
    
    <!-- Total Invoices -->
    <div class="col-12 col-sm-6 col-md-4 col-xl-custom kpi-col-5">
        <div class="kpi-card bg-gradient-emerald h-100">
            <div class="icon-box">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div class="kpi-title">{{ __('Total Invoices') }}</div>
            <h2 class="kpi-value">{{ $stats['invoices_count'] }}</h2>
            <div class="small opacity-75 mt-2">
                <span class="text-white fw-bold">{{ $stats['paid_invoices_count'] }}</span> {{ __('Paid') }} &bull; <span class="text-white-50">{{ $stats['unpaid_invoices_count'] }}</span> {{ __('Unpaid') }}
            </div>
        </div>
    </div>

    <!-- Paid Amount -->
    <div class="col-12 col-sm-6 col-md-4 col-xl-custom kpi-col-5">
        <div class="kpi-card bg-gradient-amber h-100">
            <div class="icon-box">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="kpi-title">{{ __('Collected Revenue') }}</div>
            <h2 class="kpi-value">{{ format_currency($stats['total_paid']) }}</h2>
            <div class="small opacity-75 mt-2">
                <i class="fas fa-arrow-up me-1"></i> {{ __('Total Paid') }}
            </div>
        </div>
    </div>

    <!-- Pending Due -->
    <div class="col-12 col-sm-6 col-md-4 col-xl-custom kpi-col-5">
        <div class="kpi-card bg-gradient-rose h-100">
            <div class="icon-box">
                <i class="fas fa-clock"></i>
            </div>
            <div class="kpi-title">{{ __('Pending Due') }}</div>
            <h2 class="kpi-value">{{ format_currency($stats['total_pending']) }}</h2>
            <div class="small opacity-75 mt-2">
                <i class="fas fa-exclamation-triangle me-1"></i> {{ __('Outstanding Balance') }}
            </div>
        </div>
    </div>

    <!-- Job Orders -->
    <div class="col-12 col-sm-6 col-md-4 col-xl-custom kpi-col-5">
        <div class="kpi-card bg-gradient-purple h-100">
            <div class="icon-box">
                <i class="fas fa-tasks"></i>
            </div>
            <div class="kpi-title">{{ __('Job Orders') }}</div>
            <h2 class="kpi-value">{{ $stats['job_orders_count'] }}</h2>
            <div class="small opacity-75 mt-2">
                <span class="text-white fw-bold">{{ $stats['pending_job_orders_count'] }}</span> {{ __('Pending') }} &bull; <span class="text-white-50">{{ $stats['completed_job_orders_count'] }}</span> {{ __('Done') }}
            </div>
        </div>
    </div>
</div>

<!-- ERPGo Charts Row (Revenue Trends & Activity Breakdown) -->
<div class="row g-4 mb-4">
    <!-- Monthly Revenue Trend Chart -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold text-dark mb-1">{{ __('Revenue & Sales Overview') }}</h5>
                        <p class="text-muted small mb-0">{{ __('Past 6 months collected invoice revenues') }}</p>
                    </div>
                    <span class="badge-soft badge-soft-primary px-3 py-1">
                        <i class="fas fa-chart-line me-1"></i> {{ __('Monthly Analytics') }}
                    </span>
                </div>
                <div style="height: 270px; position: relative;">
                    <canvas id="revenueTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Business Activity Ratio Chart -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0">{{ __('Workflow Breakdown') }}</h5>
                    <span class="badge-soft badge-soft-info"><i class="fas fa-pie-chart me-1"></i>{{ __('Ratio') }}</span>
                </div>
                <p class="text-muted small mb-3">{{ __('Volume distribution between Quotes, Orders and Invoices') }}</p>
                <div class="my-auto d-flex justify-content-center" style="height: 200px; position: relative;">
                    <canvas id="workflowPieChart"></canvas>
                </div>
                <div class="d-flex justify-content-around text-center pt-3 border-top mt-3">
                    <div>
                        <div class="text-muted extra-small">{{ __('Quotes') }}</div>
                        <div class="fw-bold text-dark">{{ $stats['quotes_count'] }}</div>
                    </div>
                    <div>
                        <div class="text-muted extra-small">{{ __('Job Orders') }}</div>
                        <div class="fw-bold text-dark">{{ $stats['job_orders_count'] }}</div>
                    </div>
                    <div>
                        <div class="text-muted extra-small">{{ __('Invoices') }}</div>
                        <div class="fw-bold text-dark">{{ $stats['invoices_count'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Data Tables (Job Orders & Invoices) -->
<div class="row g-4 mb-4">
    <!-- Recent Job Orders Table -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-body p-0">
                <div class="p-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                    <div>
                        <h5 class="fw-bold text-dark mb-0"><i class="fas fa-tasks text-primary me-2"></i>{{ __('Recent Job Orders') }}</h5>
                    </div>
                    <a href="{{ route('job-orders.index') }}" class="btn btn-sm btn-light border text-primary">
                        {{ __('View All') }} <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>{{ __('Order #') }}</th>
                                <th>{{ __('Customer') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th class="text-end">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentJobOrders as $order)
                                <tr>
                                    <td class="fw-bold text-primary">
                                        <a href="{{ route('job-orders.show', $order->id) }}">{{ $order->order_number }}</a>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ tdb($order->customer->name ?? '---') }}</div>
                                        <div class="text-muted extra-small">{{ $order->created_at->format('d M, Y') }}</div>
                                    </td>
                                    <td class="fw-semibold text-dark">{{ format_currency($order->total_amount) }}</td>
                                    <td>
                                        @php
                                            $joBadge = match($order->status) {
                                                'completed' => 'badge-soft-success',
                                                'in_progress' => 'badge-soft-info',
                                                'cancelled' => 'badge-soft-danger',
                                                default => 'badge-soft-warning'
                                            };
                                        @endphp
                                        <span class="badge-soft {{ $joBadge }}">{{ ucfirst(__($order->status)) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('job-orders.show', $order->id) }}" class="btn btn-sm btn-light border p-1 px-2" title="{{ __('View') }}">
                                            <i class="fas fa-eye text-primary"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fas fa-tasks fa-2x mb-2 opacity-50 d-block"></i>
                                        {{ __('No job orders created yet.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Invoices Table -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-body p-0">
                <div class="p-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                    <div>
                        <h5 class="fw-bold text-dark mb-0"><i class="fas fa-receipt text-success me-2"></i>{{ __('Recent Invoices') }}</h5>
                    </div>
                    <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-light border text-primary">
                        {{ __('View All') }} <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>{{ __('Invoice #') }}</th>
                                <th>{{ __('Customer') }}</th>
                                <th>{{ __('Grand Total') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th class="text-end">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentInvoices as $inv)
                                <tr>
                                    <td class="fw-bold text-primary">
                                        <a href="{{ route('invoices.show', $inv) }}">{{ $inv->invoice_number }}</a>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ tdb($inv->customer->name ?? '---') }}</div>
                                        <div class="text-muted extra-small">{{ \Carbon\Carbon::parse($inv->invoice_date)->format('d M, Y') }}</div>
                                    </td>
                                    <td class="fw-semibold text-dark">{{ format_currency($inv->grand_total) }}</td>
                                    <td>
                                        @php
                                            $invBadge = match($inv->status) {
                                                'Paid' => 'badge-soft-success',
                                                'Partially Paid' => 'badge-soft-warning',
                                                'Cancelled' => 'badge-soft-danger',
                                                default => 'badge-soft-info'
                                            };
                                        @endphp
                                        <span class="badge-soft {{ $invBadge }}">{{ __($inv->status) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('invoices.show', $inv) }}" class="btn btn-sm btn-light border p-1 px-2" title="{{ __('View') }}">
                                            <i class="fas fa-eye text-primary"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fas fa-receipt fa-2x mb-2 opacity-50 d-block"></i>
                                        {{ __('No invoices recorded yet.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Quotations & Customers Quick Grid -->
<div class="row g-4">
    <!-- Recent Quotations -->
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-body p-0">
                <div class="p-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                    <div>
                        <h5 class="fw-bold text-dark mb-0"><i class="fas fa-file-invoice text-info me-2"></i>{{ __('Recent Quotations') }}</h5>
                    </div>
                    <a href="{{ route('quotes.index') }}" class="btn btn-sm btn-light border text-primary">
                        {{ __('View All') }} <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>{{ __('Quote #') }}</th>
                                <th>{{ __('Customer') }}</th>
                                <th>{{ __('Total') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th class="text-end">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentQuotes as $quote)
                                <tr>
                                    <td class="fw-bold text-primary">
                                        <a href="{{ route('quotes.show', $quote) }}">{{ $quote->quote_number }}</a>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ tdb($quote->customer->name ?? '---') }}</div>
                                        <div class="text-muted extra-small">{{ \Carbon\Carbon::parse($quote->quote_date)->format('d M, Y') }}</div>
                                    </td>
                                    <td class="fw-semibold text-dark">{{ format_currency($quote->grand_total) }}</td>
                                    <td>
                                        @php
                                            $qBadge = match($quote->status) {
                                                'Accepted' => 'badge-soft-success',
                                                'Invoiced' => 'badge-soft-primary',
                                                'Declined' => 'badge-soft-danger',
                                                default => 'badge-soft-warning'
                                            };
                                        @endphp
                                        <span class="badge-soft {{ $qBadge }}">{{ __($quote->status) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('quotes.show', $quote) }}" class="btn btn-sm btn-light border p-1 px-2">
                                            <i class="fas fa-eye text-primary"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fas fa-file-invoice fa-2x mb-2 opacity-50 d-block"></i>
                                        {{ __('No quotes available.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Registered Customers -->
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-body p-0">
                <div class="p-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                    <div>
                        <h5 class="fw-bold text-dark mb-0"><i class="fas fa-user-friends text-primary me-2"></i>{{ __('Recent Customers') }}</h5>
                    </div>
                    <a href="{{ route('customers.index') }}" class="btn btn-sm btn-light border text-primary">
                        {{ __('View All') }} <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>

                <div class="p-3">
                    @forelse($recentCustomers as $cust)
                        <div class="d-flex align-items-center justify-content-between p-2 rounded-3 hover-bg-light mb-2">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                                    style="width: 38px; height: 38px; font-size: 0.85rem; background: var(--erp-primary);">
                                    {{ strtoupper(substr($cust->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark">{{ tdb($cust->name) }}</div>
                                    <div class="text-muted extra-small">
                                        {{ $cust->phone ?: ($cust->email ?: 'No contact') }}
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('customers.show', $cust) }}" class="btn btn-sm btn-light border px-2 py-1" title="{{ __('View Profile') }}">
                                <i class="fas fa-chevron-right text-muted"></i>
                            </a>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-user-friends fa-2x mb-2 opacity-50 d-block"></i>
                            {{ __('No customers registered yet.') }}
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Revenue Monthly Trend Chart (Area Chart)
    const ctxRevenue = document.getElementById('revenueTrendChart');
    if (ctxRevenue && typeof Chart !== 'undefined') {
        const labels = {!! json_encode($monthlyLabels ?? []) !!};
        const dataValues = {!! json_encode($monthlyRevenue ?? []) !!};

        new Chart(ctxRevenue, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: '{{ __("Collected Revenue") }}',
                    data: dataValues,
                    borderColor: '#4361EE',
                    backgroundColor: 'rgba(67, 97, 238, 0.12)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#4361EE',
                    pointBorderColor: '#FFFFFF',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#17243A',
                        padding: 10,
                        titleFont: { size: 13, family: 'Inter' },
                        bodyFont: { size: 12, family: 'Inter' },
                        cornerRadius: 8
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#8792A5', font: { family: 'Inter', size: 11 } }
                    },
                    y: {
                        grid: { color: '#F1F4F9' },
                        ticks: { color: '#8792A5', font: { family: 'Inter', size: 11 } }
                    }
                }
            }
        });
    }

    // 2. Workflow Breakdown Doughnut Chart
    const ctxWorkflow = document.getElementById('workflowPieChart');
    if (ctxWorkflow && typeof Chart !== 'undefined') {
        const qCount = {{ (int) ($stats['quotes_count'] ?? 0) }};
        const joCount = {{ (int) ($stats['job_orders_count'] ?? 0) }};
        const invCount = {{ (int) ($stats['invoices_count'] ?? 0) }};

        new Chart(ctxWorkflow, {
            type: 'doughnut',
            data: {
                labels: ['{{ __("Quotes") }}', '{{ __("Job Orders") }}', '{{ __("Invoices") }}'],
                datasets: [{
                    data: [qCount, joCount, invCount],
                    backgroundColor: ['#17A2B8', '#8B5CF6', '#16A579'],
                    borderWidth: 2,
                    borderColor: '#FFFFFF',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#263247',
                            font: { family: 'Inter', size: 11 },
                            boxWidth: 10,
                            padding: 12
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
