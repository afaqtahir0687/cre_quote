@extends('admin.layouts.app')

@section('title', __('Dashboard'))

@section('content')
@php
    $dashLogo = shop_logo_url($currentShop, $setting);
    $dashTitle = shop_brand_name($currentShop, $setting);
    $isSuper = auth()->check() && auth()->user()->isSuperAdmin();
    $isSwitched = session('active_shop_id') && session('active_shop_id') !== 'all';
@endphp

<!-- ERPGo-Style Page Header with Title, Breadcrumb & Actions -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">{{ __('Dashboard Overview') }}</h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 extra-small">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted"><i class="fas fa-home me-1"></i>{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">{{ __('Dashboard') }}</li>
            </ol>
        </nav>
    </div>

    <!-- Quick Action Buttons -->
    <div class="d-flex align-items-center gap-2 flex-wrap">
        @if(shop_has_module('travel_tourism'))
            <a href="{{ route('travel.bookings.create') }}" class="btn btn-primary-modern btn-sm shadow-sm">
                <i class="fas fa-ticket-alt me-1"></i> {{ __('Book Bus Seat') }}
            </a>
            <a href="{{ route('travel.trips.create') }}" class="btn btn-secondary-modern btn-sm shadow-sm">
                <i class="fas fa-bus me-1"></i> {{ __('Schedule Umrah Trip') }}
            </a>
        @endif
        @if(shop_has_module('job_orders'))
            <a href="{{ route('job-orders.create') }}" class="btn btn-primary-modern btn-sm shadow-sm">
                <i class="fas fa-plus me-1"></i> {{ __('New Job Order') }}
            </a>
        @endif
        @if(shop_has_module('quotations'))
            <a href="{{ route('quotes.create') }}" class="btn btn-secondary-modern btn-sm shadow-sm">
                <i class="fas fa-file-invoice me-1"></i> {{ __('New Quote') }}
            </a>
        @endif
        @if(shop_has_module('invoices'))
            <a href="{{ route('invoices.create') }}" class="btn btn-secondary-modern btn-sm shadow-sm">
                <i class="fas fa-receipt me-1"></i> {{ __('New Invoice') }}
            </a>
        @endif
    </div>
</div>

<!-- Active Shop & Context Banner -->
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #ffffff 0%, #f8faff 100%); border-left: 4px solid var(--erp-primary, #4361EE) !important;">
    <div class="card-body p-3 p-md-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white p-2 rounded-3 shadow-sm border d-flex align-items-center justify-content-center flex-shrink-0" style="width: 64px; height: 64px;">
                    @if($dashLogo)
                        <img src="{{ $dashLogo }}" alt="{{ $dashTitle }}" class="img-fluid" style="max-height: 48px; max-width: 48px; object-fit: contain;">
                    @else
                        <div class="w-100 h-100 rounded text-white d-flex align-items-center justify-content-center fs-3" style="background: var(--erp-primary, #4361EE);">
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

            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-white text-muted border px-3 py-2 rounded-3 shadow-xs">
                    <i class="fas fa-calendar-day text-primary me-1"></i> {{ date('l, d M Y') }}
                </span>
            </div>
        </div>
    </div>
</div>

@if(shop_has_module('travel_tourism') && !shop_has_module('job_orders'))
    <!-- Umrah & Travel Agency Specialized Metric Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. Coach Fleet Occupancy (49-Seat Buses) -->
        <div class="col-12 col-sm-6 col-lg-custom-5 col-xl">
            <div class="kpi-card bg-gradient-indigo h-100 d-flex flex-column justify-content-between shadow-sm">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="kpi-title mb-0">{{ __('Coach Bus Occupancy') }}</div>
                        <div class="icon-box">
                            <i class="fas fa-bus"></i>
                        </div>
                    </div>
                    <h2 class="kpi-value text-white mb-1" style="color: #ffffff !important;">
                        {{ $travelStats['booked_coach_seats'] ?? 0 }} <span class="fs-6 opacity-75">/ {{ $travelStats['total_coach_seats'] ?? 0 }}</span>
                    </h2>
                </div>
                <div class="small opacity-75 mt-2 pt-2 border-top border-white border-opacity-10">
                    <span class="text-white fw-bold">{{ $travelStats['coach_occupancy_percent'] ?? 0 }}%</span> {{ __('Occupied') }} &bull; <span class="text-white fw-bold">{{ $travelStats['available_coach_seats'] ?? 0 }}</span> {{ __('Seats Free') }}
                </div>
            </div>
        </div>

        <!-- 2. Upcoming Umrah Trips -->
        <div class="col-12 col-sm-6 col-lg-custom-5 col-xl">
            <div class="kpi-card bg-gradient-emerald h-100 d-flex flex-column justify-content-between shadow-sm">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="kpi-title mb-0">{{ __('Active Umrah Trips') }}</div>
                        <div class="icon-box">
                            <i class="fas fa-kaaba"></i>
                        </div>
                    </div>
                    <h2 class="kpi-value text-white mb-1" style="color: #ffffff !important;">{{ $travelStats['upcoming_trips'] ?? 0 }}</h2>
                </div>
                <div class="small opacity-75 mt-2 pt-2 border-top border-white border-opacity-10">
                    <i class="fas fa-route me-1"></i> {{ __('Scheduled Coach Departures') }}
                </div>
            </div>
        </div>

        <!-- 3. Confirmed Pilgrims & Passengers -->
        <div class="col-12 col-sm-6 col-lg-custom-5 col-xl">
            <div class="kpi-card bg-gradient-purple h-100 d-flex flex-column justify-content-between shadow-sm">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="kpi-title mb-0">{{ __('Confirmed Travelers') }}</div>
                        <div class="icon-box">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                    </div>
                    <h2 class="kpi-value text-white mb-1" style="color: #ffffff !important;">{{ $travelStats['confirmed_bookings'] ?? 0 }}</h2>
                </div>
                <div class="small opacity-75 mt-2 pt-2 border-top border-white border-opacity-10">
                    <i class="fas fa-check-circle me-1"></i> {{ __('Active Seat Reservations') }}
                </div>
            </div>
        </div>

        <!-- 4. Realized Fare Cash -->
        <div class="col-12 col-sm-6 col-lg-custom-5 col-xl">
            <div class="kpi-card bg-gradient-amber h-100 d-flex flex-column justify-content-between shadow-sm">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="kpi-title mb-0">{{ __('Fare Collected') }}</div>
                        <div class="icon-box">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                    <h2 class="kpi-value text-white mb-1" style="color: #ffffff !important;">{{ format_currency($travelStats['travel_paid'] ?? 0) }}</h2>
                </div>
                <div class="small opacity-75 mt-2 pt-2 border-top border-white border-opacity-10">
                    <i class="fas fa-arrow-up me-1"></i> {{ __('Bus & Hotel Cash Received') }}
                </div>
            </div>
        </div>

        <!-- 5. Passenger Balance Due -->
        <div class="col-12 col-sm-6 col-lg-custom-5 col-xl">
            <div class="kpi-card bg-gradient-rose h-100 d-flex flex-column justify-content-between shadow-sm">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="kpi-title mb-0">{{ __('Travel Balance Due') }}</div>
                        <div class="icon-box">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <h2 class="kpi-value text-white mb-1" style="color: #ffffff !important;">{{ format_currency($travelStats['travel_pending'] ?? 0) }}</h2>
                </div>
                <div class="small opacity-75 mt-2 pt-2 border-top border-white border-opacity-10">
                    <i class="fas fa-exclamation-triangle me-1"></i> {{ __('Receivable at Departure') }}
                </div>
            </div>
        </div>
    </div>
@else
    <!-- 5-Column Standard Metric Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. Total Customers -->
        <div class="col-12 col-sm-6 col-lg-custom-5 col-xl">
            <div class="kpi-card bg-gradient-indigo h-100 d-flex flex-column justify-content-between shadow-sm">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="kpi-title mb-0">{{ __('Total Customers') }}</div>
                        <div class="icon-box">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <h2 class="kpi-value text-white mb-1" style="color: #ffffff !important;">{{ $stats['customers_count'] }}</h2>
                </div>
                <div class="small opacity-75 mt-2 pt-2 border-top border-white border-opacity-10">
                    <i class="fas fa-check-circle me-1"></i> {{ __('Active Registered Clients') }}
                </div>
            </div>
        </div>
        
        <!-- 2. Total Invoices -->
        <div class="col-12 col-sm-6 col-lg-custom-5 col-xl">
            <div class="kpi-card bg-gradient-emerald h-100 d-flex flex-column justify-content-between shadow-sm">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="kpi-title mb-0">{{ __('Total Invoices') }}</div>
                        <div class="icon-box">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                    </div>
                    <h2 class="kpi-value text-white mb-1" style="color: #ffffff !important;">{{ $stats['invoices_count'] }}</h2>
                </div>
                <div class="small opacity-75 mt-2 pt-2 border-top border-white border-opacity-10">
                    <span class="text-white fw-bold">{{ $stats['paid_invoices_count'] }}</span> {{ __('Paid') }} &bull; <span class="text-white-50">{{ $stats['unpaid_invoices_count'] }}</span> {{ __('Unpaid') }}
                </div>
            </div>
        </div>

        <!-- 3. Collected Revenue -->
        <div class="col-12 col-sm-6 col-lg-custom-5 col-xl">
            <div class="kpi-card bg-gradient-amber h-100 d-flex flex-column justify-content-between shadow-sm">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="kpi-title mb-0">{{ __('Collected Revenue') }}</div>
                        <div class="icon-box">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                    <h2 class="kpi-value text-white mb-1" style="color: #ffffff !important;">{{ format_currency($stats['total_paid']) }}</h2>
                </div>
                <div class="small opacity-75 mt-2 pt-2 border-top border-white border-opacity-10">
                    <i class="fas fa-arrow-up me-1"></i> {{ __('Total Realized Cash') }}
                </div>
            </div>
        </div>

        <!-- 4. Pending Due -->
        <div class="col-12 col-sm-6 col-lg-custom-5 col-xl">
            <div class="kpi-card bg-gradient-rose h-100 d-flex flex-column justify-content-between shadow-sm">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="kpi-title mb-0">{{ __('Pending Due') }}</div>
                        <div class="icon-box">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <h2 class="kpi-value text-white mb-1" style="color: #ffffff !important;">{{ format_currency($stats['total_pending']) }}</h2>
                </div>
                <div class="small opacity-75 mt-2 pt-2 border-top border-white border-opacity-10">
                    <i class="fas fa-exclamation-triangle me-1"></i> {{ __('Outstanding Receivables') }}
                </div>
            </div>
        </div>

        <!-- 5. Job Orders or Travel Bookings -->
        <div class="col-12 col-sm-6 col-lg-custom-5 col-xl">
            <div class="kpi-card bg-gradient-purple h-100 d-flex flex-column justify-content-between shadow-sm">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="kpi-title mb-0">{{ shop_has_module('job_orders') ? __('Job Orders') : __('Umrah Bookings') }}</div>
                        <div class="icon-box">
                            <i class="fas {{ shop_has_module('job_orders') ? 'fa-tasks' : 'fa-ticket-alt' }}"></i>
                        </div>
                    </div>
                    <h2 class="kpi-value text-white mb-1" style="color: #ffffff !important;">
                        {{ shop_has_module('job_orders') ? $stats['job_orders_count'] : ($travelStats['total_bookings'] ?? 0) }}
                    </h2>
                </div>
                <div class="small opacity-75 mt-2 pt-2 border-top border-white border-opacity-10">
                    @if(shop_has_module('job_orders'))
                        <span class="text-white fw-bold">{{ $stats['pending_job_orders_count'] }}</span> {{ __('Pending') }} &bull; <span class="text-white-50">{{ $stats['completed_job_orders_count'] }}</span> {{ __('Done') }}
                    @else
                        <span class="text-white fw-bold">{{ $travelStats['confirmed_bookings'] ?? 0 }}</span> {{ __('Confirmed') }}
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif

<!-- ERPGo Charts Row (Monthly Trends & Workflow Breakdown) -->
<div class="row g-4 mb-4">
    <!-- Left: 6-Month Revenue Trend Line Chart -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
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
                <div style="height: 280px; position: relative;">
                    <canvas id="revenueTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Right: Business Activity Ratio Doughnut Chart -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="fw-bold text-dark mb-0">{{ __('Workflow Breakdown') }}</h5>
                    <span class="badge-soft badge-soft-info"><i class="fas fa-pie-chart me-1"></i>{{ __('Ratio') }}</span>
                </div>
                <p class="text-muted small mb-3">{{ __('Volume distribution between Quotes, Orders and Invoices') }}</p>
                <div class="my-auto d-flex justify-content-center" style="height: 200px; position: relative;">
                    <canvas id="workflowPieChart"></canvas>
                </div>
                <div class="d-flex justify-content-around text-center pt-3 border-top mt-3">
                    @if(shop_has_module('travel_tourism') && !shop_has_module('job_orders'))
                        <div>
                            <div class="text-muted extra-small">{{ __('Umrah Trips') }}</div>
                            <div class="fw-bold text-dark fs-6">{{ $travelStats['total_trips'] ?? 0 }}</div>
                        </div>
                        <div>
                            <div class="text-muted extra-small">{{ __('Bookings') }}</div>
                            <div class="fw-bold text-dark fs-6">{{ $travelStats['total_bookings'] ?? 0 }}</div>
                        </div>
                        <div>
                            <div class="text-muted extra-small">{{ __('Invoices') }}</div>
                            <div class="fw-bold text-dark fs-6">{{ $stats['invoices_count'] }}</div>
                        </div>
                    @else
                        <div>
                            <div class="text-muted extra-small">{{ __('Quotes') }}</div>
                            <div class="fw-bold text-dark fs-6">{{ $stats['quotes_count'] }}</div>
                        </div>
                        <div>
                            <div class="text-muted extra-small">{{ __('Job Orders') }}</div>
                            <div class="fw-bold text-dark fs-6">{{ $stats['job_orders_count'] }}</div>
                        </div>
                        <div>
                            <div class="text-muted extra-small">{{ __('Invoices') }}</div>
                            <div class="fw-bold text-dark fs-6">{{ $stats['invoices_count'] }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if(shop_has_module('travel_tourism') && !shop_has_module('job_orders'))
<!-- Umrah & Travel Trips + Invoices Row -->
<div class="row g-4 mb-4">
    <!-- Active Coach Bus Trips (Live 49-Seat Occupancy) -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-0">
                <div class="p-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                    <div>
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="fas fa-bus-alt text-primary me-2"></i>{{ __('Active Umrah Trips & Bus Fleet') }}
                        </h5>
                        <p class="text-muted extra-small mb-0">{{ __('Live 49-seat coach occupancy and departure schedule') }}</p>
                    </div>
                    <a href="{{ route('travel.trips.index') }}" class="btn btn-sm btn-light border text-primary">
                        {{ __('View All Trips') }} <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>{{ __('Trip / Route') }}</th>
                                <th>{{ __('Departure') }}</th>
                                <th style="min-width: 170px;">{{ __('Coach Bus Occupancy') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th class="text-end">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeTrips as $trip)
                                @php
                                    $seatsTotal = max(1, $trip->total_seats ?: 49);
                                    $seatsBooked = (int) ($trip->bookings_count ?? 0);
                                    $seatsFree = max(0, $seatsTotal - $seatsBooked);
                                    $occupancyPercent = min(100, round(($seatsBooked / $seatsTotal) * 100));
                                    $progressColor = $occupancyPercent >= 90 ? 'bg-danger' : ($occupancyPercent >= 60 ? 'bg-warning' : 'bg-primary');
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-bold text-primary">
                                            <a href="{{ route('travel.trips.show', $trip) }}">{{ $trip->trip_code }}</a>
                                        </div>
                                        <div class="fw-semibold text-dark small">{{ $trip->title }}</div>
                                        <div class="text-muted extra-small">
                                            <i class="fas fa-map-marker-alt text-danger me-1"></i>{{ $trip->departure_city }} &rarr; {{ $trip->destination_city }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark small">
                                            {{ $trip->departure_date ? $trip->departure_date->format('d M, Y') : '---' }}
                                        </div>
                                        <div class="text-muted extra-small">
                                            <i class="fas fa-clock me-1"></i>{{ $trip->departure_date ? $trip->departure_date->format('h:i A') : '' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="extra-small fw-bold text-dark">{{ $seatsBooked }}/{{ $seatsTotal }} {{ __('Seats') }}</span>
                                            <span class="extra-small fw-semibold text-muted">{{ $occupancyPercent }}%</span>
                                        </div>
                                        <div class="progress" style="height: 6px; border-radius: 4px; background: #eef2f6;">
                                            <div class="progress-bar {{ $progressColor }}" role="progressbar" style="width: {{ $occupancyPercent }}%;" aria-valuenow="{{ $occupancyPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="extra-small text-muted mt-1">
                                            <span class="text-success fw-semibold">{{ $seatsFree }} {{ __('Seats Available') }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $tripBadge = match($trip->status) {
                                                'upcoming' => 'badge-soft-info',
                                                'ongoing' => 'badge-soft-success',
                                                'completed' => 'badge-soft-primary',
                                                'cancelled' => 'badge-soft-danger',
                                                default => 'badge-soft-warning'
                                            };
                                        @endphp
                                        <span class="badge-soft {{ $tripBadge }}">{{ ucfirst(__($trip->status)) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('travel.trips.show', $trip) }}" class="btn btn-sm btn-light border p-1 px-2" title="{{ __('View 49-Seat Layout') }}">
                                                <i class="fas fa-th-large text-primary"></i>
                                            </a>
                                            <a href="{{ route('travel.bookings.create', ['trip_id' => $trip->id]) }}" class="btn btn-sm btn-primary p-1 px-2" title="{{ __('Book Passenger Seat') }}">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fas fa-bus-alt fa-2x mb-2 opacity-50 d-block"></i>
                                        {{ __('No active Umrah trips scheduled yet.') }}
                                        <div class="mt-2">
                                            <a href="{{ route('travel.trips.create') }}" class="btn btn-sm btn-primary-modern">
                                                <i class="fas fa-plus me-1"></i> {{ __('Schedule First Umrah Trip') }}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Invoices -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
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

<!-- Passenger Manifest & Registered Travelers Row -->
<div class="row g-4 mb-4">
    <!-- Passenger Manifest (Recent Bookings) -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-0">
                <div class="p-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                    <div>
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="fas fa-ticket-alt text-primary me-2"></i>{{ __('Recent Passenger Bookings') }}
                        </h5>
                        <p class="text-muted extra-small mb-0">{{ __('Seat allocations, hotel packages, and boarding pass actions') }}</p>
                    </div>
                    <a href="{{ route('travel.bookings.index') }}" class="btn btn-sm btn-light border text-primary">
                        {{ __('View All Bookings') }} <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>{{ __('Passenger / Seat') }}</th>
                                <th>{{ __('Trip Route') }}</th>
                                <th>{{ __('Package') }}</th>
                                <th>{{ __('Fare & Payment') }}</th>
                                <th class="text-end">{{ __('Voucher') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBookings as $b)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-primary text-white px-2 py-1" style="font-size: 0.75rem; border-radius: 6px;">
                                                <i class="fas fa-chair me-1"></i>#{{ $b->seat_number ?: '?' }}
                                            </span>
                                            <div>
                                                <div class="fw-bold text-dark small">{{ $b->passenger_name }}</div>
                                                <div class="text-muted extra-small">{{ $b->passenger_phone }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-primary small">
                                            {{ $b->trip->trip_code ?? '---' }}
                                        </div>
                                        <div class="text-muted extra-small">
                                            {{ $b->trip->departure_city ?? '' }} &rarr; {{ $b->trip->destination_city ?? '' }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($b->package_type === 'with_hotel')
                                            <span class="badge-soft badge-soft-primary">
                                                <i class="fas fa-hotel me-1"></i>{{ __('With Hotel') }}
                                            </span>
                                            @if($b->hotel_name)
                                                <div class="text-muted extra-small mt-1">{{ Str::limit($b->hotel_name, 16) }}</div>
                                            @endif
                                        @else
                                            <span class="badge-soft badge-soft-info">
                                                <i class="fas fa-bus me-1"></i>{{ __('Bus Only') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark small">{{ format_currency($b->total_amount) }}</div>
                                        @php
                                            $payBadge = match($b->payment_status) {
                                                'paid' => 'badge-soft-success',
                                                'partial' => 'badge-soft-warning',
                                                default => 'badge-soft-danger'
                                            };
                                        @endphp
                                        <span class="badge-soft {{ $payBadge }} extra-small">{{ ucfirst(__($b->payment_status ?? 'unpaid')) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('travel.bookings.voucher', $b) }}" target="_blank" class="btn btn-sm btn-light border p-1 px-2" title="{{ __('Print Boarding Pass') }}">
                                                <i class="fas fa-print text-dark"></i>
                                            </a>
                                            <a href="{{ route('travel.bookings.show', $b) }}" class="btn btn-sm btn-light border p-1 px-2" title="{{ __('View Details') }}">
                                                <i class="fas fa-eye text-primary"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fas fa-ticket-alt fa-2x mb-2 opacity-50 d-block"></i>
                                        {{ __('No passenger bookings recorded yet.') }}
                                        <div class="mt-2">
                                            <a href="{{ route('travel.bookings.create') }}" class="btn btn-sm btn-primary-modern">
                                                <i class="fas fa-plus me-1"></i> {{ __('Book Passenger Seat') }}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Registered Pilgrims / Customers -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-0">
                <div class="p-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                    <div>
                        <h5 class="fw-bold text-dark mb-0"><i class="fas fa-user-friends text-primary me-2"></i>{{ __('Registered Pilgrims & Clients') }}</h5>
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
                                    style="width: 40px; height: 40px; font-size: 0.88rem; background: var(--erp-primary, #4361EE);">
                                    {{ strtoupper(substr($cust->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark">{{ tdb($cust->name) }}</div>
                                    <div class="text-muted extra-small">
                                        {{ $cust->phone ?: ($cust->email ?: __('No contact number')) }}
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
@else
<!-- Recent Job Orders & Invoices Tables Row -->
<div class="row g-4 mb-4">
    <!-- Recent Job Orders -->
    @if(shop_has_module('job_orders'))
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
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
    @endif

    <!-- Recent Invoices -->
    <div class="{{ shop_has_module('job_orders') ? 'col-lg-6' : 'col-12' }}">
        <div class="card border-0 shadow-sm rounded-4 h-100">
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

<!-- Recent Quotations & Registered Customers Row -->
<div class="row g-4">
    <!-- Recent Quotations -->
    @if(shop_has_module('quotations'))
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
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
                                        <a href="{{ route('quotes.show', $quote) }}" class="btn btn-sm btn-light border p-1 px-2" title="{{ __('View') }}">
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
    @endif

    <!-- Recent Registered Customers -->
    <div class="{{ shop_has_module('quotations') ? 'col-lg-5' : 'col-12' }}">
        <div class="card border-0 shadow-sm rounded-4 h-100">
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
                                    style="width: 40px; height: 40px; font-size: 0.88rem; background: var(--erp-primary, #4361EE);">
                                    {{ strtoupper(substr($cust->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark">{{ tdb($cust->name) }}</div>
                                    <div class="text-muted extra-small">
                                        {{ $cust->phone ?: ($cust->email ?: __('No contact number')) }}
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
@endif

                <div class="p-3">
                    @forelse($recentCustomers as $cust)
                        <div class="d-flex align-items-center justify-content-between p-2 rounded-3 hover-bg-light mb-2">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                                    style="width: 40px; height: 40px; font-size: 0.88rem; background: var(--erp-primary, #4361EE);">
                                    {{ strtoupper(substr($cust->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark">{{ tdb($cust->name) }}</div>
                                    <div class="text-muted extra-small">
                                        {{ $cust->phone ?: ($cust->email ?: __('No contact number')) }}
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
        @if(shop_has_module('travel_tourism') && !shop_has_module('job_orders'))
            const tripCount = {{ (int) ($travelStats['total_trips'] ?? 0) }};
            const bookingCount = {{ (int) ($travelStats['total_bookings'] ?? 0) }};
            const invCount = {{ (int) ($stats['invoices_count'] ?? 0) }};

            new Chart(ctxWorkflow, {
                type: 'doughnut',
                data: {
                    labels: ['{{ __("Umrah Trips") }}', '{{ __("Bookings") }}', '{{ __("Invoices") }}'],
                    datasets: [{
                        data: [tripCount, bookingCount, invCount],
                        backgroundColor: ['#4361EE', '#10B981', '#F59E0B'],
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
        @else
            const qCount = {{ (int) ($stats['quotes_count'] ?? 0) }};
            const joCount = {{ (int) ($stats['job_orders_count'] ?? 0) }};
            const invCount = {{ (int) ($stats['invoices_count'] ?? 0) }};

            new Chart(ctxWorkflow, {
                type: 'doughnut',
                data: {
                    labels: ['{{ __("Quotes") }}', '{{ __("Job Orders") }}', '{{ __("Invoices") }}'],
                    datasets: [{
                        data: [qCount, joCount, invCount],
                        backgroundColor: ['#17A2B8', '#8B5CF6', '#10B981'],
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
        @endif
    }
});
</script>
@endpush
