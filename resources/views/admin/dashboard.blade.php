@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<!-- Active Shop Branding Banner -->
@php
    $dashLogo = shop_logo_url($currentShop, $setting);
    $dashTitle = shop_brand_name($currentShop, $setting);
    $isSuper = auth()->check() && auth()->user()->isSuperAdmin();
    $isSwitched = session('active_shop_id') && session('active_shop_id') !== 'all';
@endphp
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #ffffff 0%, #f8faff 100%); border-left: 4px solid #0d6efd !important;">
    <div class="card-body p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white p-2 rounded-3 shadow-sm border d-flex align-items-center justify-content-center" style="width: 72px; height: 72px; min-width: 72px;">
                    @if($dashLogo)
                        <img src="{{ $dashLogo }}" alt="{{ $dashTitle }}" class="img-fluid" style="max-height: 56px; max-width: 56px; object-fit: contain;">
                    @else
                        <div class="w-100 h-100 rounded bg-primary text-white d-flex align-items-center justify-content-center fs-3">
                            <i class="fas fa-store"></i>
                        </div>
                    @endif
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h4 class="fw-bold text-dark mb-0">{{ $dashTitle }}</h4>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill small">
                            <i class="fas fa-circle text-success me-1" style="font-size: 0.5rem;"></i> Active Shop
                        </span>
                        @if($currentShop)
                            <span class="badge bg-light text-secondary border px-2 py-1 rounded-pill small">
                                <i class="fas fa-store me-1"></i>Branch #{{ $currentShop->id }}
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
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 rounded-pill small">
                                <i class="fas fa-exchange-alt me-1"></i>Switched View
                            </span>
                        @endif
                    </div>
                    <p class="text-muted small mb-0 mt-1 d-flex align-items-center gap-3 flex-wrap">
                        @if($currentShop?->city || $setting?->company_city)
                            <span><i class="fas fa-map-marker-alt text-primary me-1"></i> {{ $currentShop->city ?? $setting->company_city }}, {{ $currentShop->country ?? $setting->company_country ?? 'Saudi Arabia' }}</span>
                        @endif
                        @if($currentShop?->email || $setting?->company_email)
                            <span><i class="fas fa-envelope text-primary me-1"></i> {{ $currentShop->email ?? $setting->company_email }}</span>
                        @endif
                        @if($currentShop?->phone || $setting?->company_phone)
                            <span><i class="fas fa-phone-alt text-primary me-1"></i> {{ $currentShop->phone ?? $setting->company_phone }}</span>
                        @endif
                        <span><i class="fas fa-coins text-warning me-1"></i> Currency: <strong>{{ $currentShop->currency_symbol ?? $setting->currency_symbol ?? '$' }}</strong></span>
                    </p>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                @if($isSuper)
                    <a href="{{ route('super_admin.shops.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="fas fa-store-alt me-1"></i> Switch Shop
                    </a>
                @endif
                @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
                    <a href="{{ route('settings.index') }}" class="btn btn-primary-modern btn-sm rounded-pill px-3">
                        <i class="fas fa-paint-brush me-1"></i> Customize Logo & Title
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4 kpi-row-5">
    <!-- KPI Cards (5 in 1 row) -->
    <div class="kpi-col-5">
        <div class="card-modern kpi-card kpi-card-sm bg-gradient-indigo h-100">
            <div class="icon-box">
                <i class="fas fa-users"></i>
            </div>
            <h6 class="text-white-50 text-uppercase fw-semibold">Total Customers</h6>
            <h2 class="mb-0 fw-bold">{{ $stats['customers_count'] }}</h2>
        </div>
    </div>
    
    <div class="kpi-col-5">
        <div class="card-modern kpi-card kpi-card-sm bg-gradient-emerald h-100">
            <div class="icon-box">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <h6 class="text-white-50 text-uppercase fw-semibold">Total Invoices</h6>
            <h2 class="mb-0 fw-bold">{{ $stats['invoices_count'] }}</h2>
        </div>
    </div>

    <div class="kpi-col-5">
        <div class="card-modern kpi-card kpi-card-sm bg-gradient-amber h-100">
            <div class="icon-box">
                <i class="fas fa-check-circle"></i>
            </div>
            <h6 class="text-white-50 text-uppercase fw-semibold">Paid Amount</h6>
            <h2 class="mb-0 fw-bold">{{ format_currency($stats['total_paid']) }}</h2>
        </div>
    </div>

    <div class="kpi-col-5">
        <div class="card-modern kpi-card kpi-card-sm bg-gradient-rose h-100">
            <div class="icon-box">
                <i class="fas fa-clock"></i>
            </div>
            <h6 class="text-white-50 text-uppercase fw-semibold">Pending Amount</h6>
            <h2 class="mb-0 fw-bold">{{ format_currency($stats['total_pending']) }}</h2>
        </div>
    </div>

    <div class="kpi-col-5">
        <div class="card-modern kpi-card kpi-card-sm bg-gradient-purple h-100">
            <div class="icon-box">
                <i class="fas fa-tags"></i>
            </div>
            <h6 class="text-white-50 text-uppercase fw-semibold">Total Discount</h6>
            <h2 class="mb-0 fw-bold">{{ format_currency($stats['total_discount']) }}</h2>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <x-card title="Recent Invoices" :padding="false">
            <x-slot name="headerActions">
                <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-link text-decoration-none fw-medium">View All</a>
            </x-slot>

            <x-table>
                <x-slot name="thead">
                    <th>Invoice #</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th class="text-end">Action</th>
                </x-slot>

                @foreach($recentInvoices as $invoice)
                <tr>
                    <td class="fw-medium text-dark">{{ $invoice->invoice_number }}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            {{ $invoice->customer->name }}
                        </div>
                    </td>
                    <td class="fw-semibold text-dark">{{ format_currency($invoice->grand_total) }}</td>
                    <td>
                        <span class="badge-soft {{ $invoice->status == 'Paid' ? 'badge-soft-success' : 'badge-soft-warning' }}">
                            {{ $invoice->status }}
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-sm btn-light rounded-circle shadow-none">
                            <i class="fas fa-eye text-muted"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </x-table>
        </x-card>
    </div>

    <div class="col-lg-4">
        <x-card title="Quick Actions">
            <div class="d-grid gap-3">
                <a href="{{ route('quotes.create') }}" class="btn btn-primary-modern text-start d-flex align-items-center">
                    <i class="fas fa-file-invoice me-3"></i> 
                    <div>
                        <span class="d-block fw-bold">Create New Quote</span>
                        <small class="text-white-50 fw-normal">Generate a professional quote</small>
                    </div>
                </a>
                
                <a href="{{ route('invoices.create') }}" class="btn btn-outline-primary border-2 text-start d-flex align-items-center py-3 rounded-3 group">
                    <i class="fas fa-file-invoice-dollar me-3 fa-lg opacity-75"></i>
                    <div>
                        <span class="d-block fw-bold text-dark">Create New Invoice</span>
                        <small class="text-muted fw-normal">Bill your customers easily</small>
                    </div>
                </a>

                <a href="{{ route('customers.create') }}" class="btn btn-outline-info border-2 text-start d-flex align-items-center py-3 rounded-3">
                    <i class="fas fa-user-plus me-3 fa-lg opacity-75"></i>
                    <div>
                        <span class="d-block fw-bold text-dark">Add New Customer</span>
                        <small class="text-muted fw-normal">Expand your client base</small>
                    </div>
                </a>

                <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary border-2 text-start d-flex align-items-center py-3 rounded-3">
                    <i class="fas fa-cog me-3 fa-lg opacity-75"></i>
                    <div>
                        <span class="d-block fw-bold text-dark">System Settings</span>
                        <small class="text-muted fw-normal">Configure your system</small>
                    </div>
                </a>
            </div>
        </x-card>
    </div>
</div>
@endsection
