@extends('admin.layouts.app')

@section('title', __('Umrah & Bus Seat Bookings'))

@push('styles')
<style>
    /* Filterable Search Dropdowns (Matching Trips, Quotes & Bookings) */
    .filter-dropdown { position: relative; width: 100%; }
    .filter-trigger {
        cursor: pointer;
        background-color: #fff !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
        background-repeat: no-repeat !important;
        background-position: right 0.75rem center !important;
        background-size: 16px 12px !important;
        padding-right: 2.25rem !important;
        font-weight: 500;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
    }
    [dir="rtl"] .filter-trigger {
        background-position: left 0.75rem center !important;
        padding-right: 0.75rem !important;
        padding-left: 2.25rem !important;
    }
    .filter-trigger:focus, .filter-trigger.active {
        box-shadow: 0 0 0 0.25rem rgba(13,110,253,.2) !important;
        border-color: #86b7fe !important;
    }
    .filter-panel {
        display: none; position: absolute; top: calc(100% + 4px); left: 0; min-width: 260px; max-width: 360px;
        z-index: 1060; background: #fff; border: 1px solid #dee2e6;
        border-radius: 0.5rem; box-shadow: 0 8px 24px rgba(0,0,0,.14); overflow: hidden;
    }
    [dir="rtl"] .filter-panel {
        left: auto;
        right: 0;
    }
    .filter-panel.open { display: block; animation: filterDropdownIn .12s ease-in-out; }
    @keyframes filterDropdownIn {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .filter-search-wrap { padding: 8px 10px; border-bottom: 1px solid #f0f0f0; background: #f8f9fa; }
    .filter-search {
        width: 100%; border: 1px solid #dee2e6; border-radius: 0.375rem;
        padding: 6px 10px 6px 32px; font-size: .875rem; outline: none;
        background: #fff url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='%236c757d' viewBox='0 0 16 16'%3e%3cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398l3.85 3.85a1 1 0 0 0 1.415-1.415zm-5.242 1.156a5 5 0 1 1 0-10 5 5 0 0 1 0 10'/%3e%3c/svg%3e") no-repeat 10px center;
    }
    [dir="rtl"] .filter-search {
        padding: 6px 32px 6px 10px;
        background-position: right 10px center;
    }
    .filter-search:focus { border-color: #86b7fe; box-shadow: 0 0 0 3px rgba(13,110,253,.15); }
    .filter-list { max-height: 230px; overflow-y: auto; padding: 4px 0; }
    .filter-list::-webkit-scrollbar { width: 5px; }
    .filter-list::-webkit-scrollbar-thumb { background: #dee2e6; border-radius: 4px; }
    .filter-option {
        padding: 8px 14px; font-size: .88rem; cursor: pointer;
        border-left: 3px solid transparent; transition: background .12s, border-color .12s;
        display: flex; justify-content: space-between; align-items: center;
    }
    [dir="rtl"] .filter-option {
        border-left: none;
        border-right: 3px solid transparent;
    }
    .filter-option:hover, .filter-option.focused { background: #f0f4ff; border-left-color: #0d6efd; }
    [dir="rtl"] .filter-option:hover, [dir="rtl"] .filter-option.focused { border-right-color: #0d6efd; }
    .filter-option.selected { background: #e7f0ff; border-left-color: #0d6efd; font-weight: 600; }
    [dir="rtl"] .filter-option.selected { border-right-color: #0d6efd; }
    .filter-option.selected .check-icon { display: inline-block; color: #0d6efd; }
    .filter-option .check-icon { display: none; }
    .filter-empty { padding: 14px; text-align: center; color: #6c757d; font-size: .875rem; }
</style>
@endpush

@section('content')
<!-- Page Header -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="fw-bold text-dark mb-0">
                <i class="fas fa-ticket-alt text-primary me-2"></i>{{ __('Umrah & Bus Seat Bookings') }}
            </h2>
            <span class="badge bg-primary text-white rounded-pill px-3 py-1">
                {{ __('Coach Fleet Roster') }}
            </span>
            @php
                $currShop = current_shop();
                $shopCr = $currShop?->cr_number ?: ($currShop?->setting?->company_cr_no ?? null);
                $shopLic = $currShop?->license_number ?: ($currShop?->setting?->company_license_no ?? null);
            @endphp
            @if($shopCr)
                <span class="badge bg-light text-secondary border font-monospace px-2 py-1" style="font-size: 0.72rem;" title="{{ __('Commercial Registration No') }}">
                    <i class="fas fa-file-contract text-primary me-1"></i>CR: {{ $shopCr }}
                </span>
            @endif
            @if($shopLic)
                <span class="badge bg-success-subtle text-success border font-monospace px-2 py-1" style="font-size: 0.72rem;" title="{{ __('Ministry License No') }}">
                    <i class="fas fa-id-card-alt me-1"></i>LIC: {{ $shopLic }}
                </span>
            @endif
        </div>
        <p class="text-muted small mb-0">
            @if($isEmployee)
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle me-1">
                    <i class="fas fa-user-tie me-1"></i> {{ __('My Bookings & Sales Roster') }} ({{ $currentEmp?->name ?? auth()->user()->name }})
                </span>
            @endif
            {{ __('Manage passenger manifests, allocated 49-seat coach reservations, package hotels, and boarding pass vouchers.') }}
        </p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('travel.trips.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-bus me-1"></i> {{ __('View Trips & Fleet') }}
        </a>
        <a href="{{ route('travel.bookings.create') }}" class="btn btn-primary-modern shadow-sm">
            <i class="fas fa-plus me-1"></i> {{ __('Book a Seat') }}
        </a>
    </div>
</div>

<!-- 4 ERP KPI Summary Metric Cards -->
<div class="row g-3 mb-4">
    <!-- 1. Total Bookings -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-primary shadow-xs" 
                     style="width: 52px; height: 52px; background: rgba(67, 97, 238, 0.12); font-size: 1.35rem; flex-shrink: 0;">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div>
                    <div class="text-muted extra-small text-uppercase fw-bold">{{ __('Total Bookings') }}</div>
                    <h3 class="fw-bold text-dark mb-0">{{ $stats['total_bookings'] }}</h3>
                    <span class="extra-small text-muted">{{ $isEmployee ? __('My registered tickets') : __('All registered passenger tickets') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Confirmed Travelers -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-success shadow-xs" 
                     style="width: 52px; height: 52px; background: rgba(16, 185, 129, 0.12); font-size: 1.35rem; flex-shrink: 0;">
                    <i class="fas fa-user-check"></i>
                </div>
                <div>
                    <div class="text-muted extra-small text-uppercase fw-bold">{{ __('Confirmed Seats') }}</div>
                    <h3 class="fw-bold text-success mb-0">{{ $stats['confirmed_bookings'] }}</h3>
                    <span class="extra-small text-muted">{{ $isEmployee ? __('My confirmed seats') : __('Pilgrims with locked seats') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Cash Received -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-info shadow-xs" 
                     style="width: 52px; height: 52px; background: rgba(14, 165, 233, 0.12); font-size: 1.35rem; flex-shrink: 0;">
                    <i class="fas fa-wallet"></i>
                </div>
                <div>
                    <div class="text-muted extra-small text-uppercase fw-bold">{{ __('Cash Collected') }}</div>
                    <h3 class="fw-bold text-dark mb-0">{{ format_currency($stats['paid_amount']) }}</h3>
                    <span class="extra-small text-success fw-semibold"><i class="fas fa-arrow-up me-1"></i>{{ $isEmployee ? __('My Collected Cash') : __('Realized Revenue') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Balance Due -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-danger shadow-xs" 
                     style="width: 52px; height: 52px; background: rgba(239, 68, 68, 0.12); font-size: 1.35rem; flex-shrink: 0;">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <div class="text-muted extra-small text-uppercase fw-bold">{{ __('Pending Due') }}</div>
                    <h3 class="fw-bold text-danger mb-0">{{ format_currency($stats['balance_due']) }}</h3>
                    <span class="extra-small text-muted">{{ $isEmployee ? __('My pending collection') : __('To collect before boarding') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters Bar Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
    <div class="card-body p-3">
        <form action="{{ route('travel.bookings.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-{{ $isEmployee ? '4' : (isset($branches) && $branches->count() > 0 ? '3' : '3') }}">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="{{ __('Search passenger name, booking ref, phone, ID/passport...') }}" value="{{ request('search') }}">
                </div>
            </div>
            @if(!$isEmployee && isset($branches) && $branches->count() > 0)
                <div class="col-md-2">
                    <select name="branch_id" id="filterBranchSelect" class="form-select filter-bar-select">
                        <option value="">{{ __('All Branches') }}</option>
                        <option value="{{ current_shop_id() }}" {{ request('branch_id') == current_shop_id() ? 'selected' : '' }}>{{ current_shop()->name }} ({{ __('Head Office') }})</option>
                        @foreach($branches as $br)
                            <option value="{{ $br->id }}" {{ request('branch_id') == $br->id ? 'selected' : '' }}>{{ $br->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="col-md-{{ $isEmployee ? '3' : '2' }}">
                <select name="trip_id" id="filterTripSelect" class="form-select filter-bar-select">
                    <option value="">{{ __('All Scheduled Trips') }}</option>
                    @foreach($trips as $t)
                        <option value="{{ $t->id }}" {{ request('trip_id') == $t->id ? 'selected' : '' }}>
                            {{ $t->departure_date ? '[' . $t->departure_date->format('d M') . '] ' : '' }}{{ $t->trip_code }} &bull; {{ $t->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            @if(!$isEmployee)
            <div class="col-md-2">
                <select name="employee_id" id="filterEmployeeSelect" class="form-select filter-bar-select">
                    <option value="">{{ __('All Staff / Agents') }}</option>
                    <option value="admin" {{ request('employee_id') === 'admin' ? 'selected' : '' }}>{{ __('Admin / Unassigned') }}</option>
                    @if(isset($employees))
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
            @endif
            <div class="col-md-{{ $isEmployee ? '3' : '2' }}">
                <select name="payment_status" id="filterPaymentStatusSelect" class="form-select filter-bar-select">
                    <option value="">{{ __('All Payment Statuses') }}</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>{{ __('Paid in Full') }}</option>
                    <option value="partial" {{ request('payment_status') === 'partial' ? 'selected' : '' }}>{{ __('Partially Paid') }}</option>
                    <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>{{ __('Unpaid') }}</option>
                </select>
            </div>
            <div class="col-md d-flex gap-2">
                <button type="submit" class="btn btn-primary-modern flex-grow-1">
                    <i class="fas fa-filter me-1"></i> {{ __('Filter') }}
                </button>
                @if(request()->has('search') || request()->has('trip_id') || request()->has('payment_status') || request()->has('branch_id') || request()->has('employee_id'))
                    <a href="{{ route('travel.bookings.index') }}" class="btn btn-light border" title="{{ __('Clear Filters') }}">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Bookings Roster Table Card -->
<x-card :padding="false" class="border-0 shadow-sm rounded-4 overflow-hidden">
    @if($bookings->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-ticket-alt fa-3x text-muted mb-3 opacity-25"></i>
            <h5 class="text-muted fw-bold">{{ __('No Passenger Bookings Found') }}</h5>
            <p class="text-muted small">{{ __('Book an Umrah seat or schedule a new trip to get started.') }}</p>
            <a href="{{ route('travel.bookings.create') }}" class="btn btn-primary-modern mt-2">
                <i class="fas fa-plus me-1"></i> {{ __('Book a Seat Now') }}
            </a>
        </div>
    @else
        <x-table>
            <x-slot name="thead">
                <th class="text-center" style="width: 75px;">{{ __('Seat #') }}</th>
                <th class="text-nowrap">{{ __('Booking Ref') }}</th>
                <th style="min-width: 200px;">{{ __('Passenger Manifest') }}</th>
                <th style="min-width: 190px;">{{ __('Trip & Route') }}</th>
                <th class="text-nowrap">{{ __('Assigned Agent') }}</th>
                <th class="text-nowrap">{{ __('Accommodation') }}</th>
                <th class="text-nowrap">{{ __('Financials') }}</th>
                <th class="text-nowrap">{{ __('Payment Status') }}</th>
                <th class="text-end pe-4 text-nowrap">{{ __('Actions') }}</th>
            </x-slot>

            @foreach($bookings as $booking)
                <tr class="align-middle hover-bg-light">
                    <!-- Seat Number Badge -->
                    <td class="text-center">
                        <span class="badge bg-primary text-white fs-6 px-2 py-1 rounded-3 shadow-xs" style="min-width: 42px;">
                            #{{ $booking->seat_number }}
                        </span>
                        <div class="mt-1">{!! $booking->seatClassBadge() !!}</div>
                    </td>

                    <!-- Reference & Date -->
                    <td>
                        <span class="badge-soft badge-soft-primary fw-bold font-monospace">
                            {{ $booking->booking_reference }}
                        </span>
                        <div class="text-muted extra-small mt-1">
                            <i class="far fa-calendar-alt me-1"></i>{{ $booking->created_at->format('d M, Y') }}
                        </div>
                    </td>

                    <!-- Passenger Details -->
                    <td>
                        <a href="{{ route('travel.bookings.show', $booking->id) }}" class="fw-bold text-dark text-decoration-none d-block">
                            {{ tdb($booking->passenger_name) }}
                        </a>
                        <div class="extra-small text-muted mt-1">
                            <i class="fas fa-phone-alt text-primary me-1"></i>
                            <a href="tel:{{ $booking->passenger_phone }}" class="text-muted text-decoration-none">{{ $booking->passenger_phone }}</a>
                            @if($booking->passenger_id_number)
                                &bull; <i class="fas fa-id-card ms-1 me-1 text-secondary"></i><span class="font-monospace">{{ $booking->passenger_id_number }}</span>
                            @endif
                        </div>
                        @if($booking->shop)
                            <div class="mt-1 d-flex gap-1 flex-wrap align-items-center">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle extra-small" style="font-size: 0.7rem;">
                                    <i class="fas fa-store me-1"></i>{{ tdb($booking->shop->name) }}
                                </span>
                                @php
                                    $bShopCr = $booking->shop->cr_number ?: ($booking->shop->setting?->company_cr_no ?? null);
                                    $bShopLic = $booking->shop->license_number ?: ($booking->shop->setting?->company_license_no ?? null);
                                @endphp
                                @if($bShopCr)
                                    <span class="badge bg-light text-secondary border font-monospace extra-small" style="font-size: 0.65rem;" title="{{ __('Commercial Registration No') }}">
                                        CR: {{ $bShopCr }}
                                    </span>
                                @endif
                                @if($bShopLic)
                                    <span class="badge bg-success-subtle text-success border font-monospace extra-small" style="font-size: 0.65rem;" title="{{ __('License No') }}">
                                        LIC: {{ $bShopLic }}
                                    </span>
                                @endif
                            </div>
                        @endif
                    </td>

                    <!-- Trip & Route -->
                    <td>
                        @if($booking->trip)
                            <a href="{{ route('travel.trips.show', $booking->trip->id) }}" class="fw-semibold text-dark text-decoration-none small d-block text-truncate" style="max-width: 220px;">
                                {{ tdb($booking->trip->title) }}
                            </a>
                            <div class="text-muted extra-small mt-1">
                                <i class="fas fa-route text-primary me-1"></i>{{ tdb($booking->trip->departure_city) }} &rarr; {{ tdb($booking->trip->destination_city) }}
                                &bull; {{ $booking->trip->departure_date ? $booking->trip->departure_date->format('d M, Y') : '' }}
                            </div>
                            <div class="mt-1 d-flex align-items-center gap-1 flex-wrap">
                                {!! $booking->journeyTypeBadge() !!}
                                @if($booking->dropoff_location)
                                    <span class="badge bg-light text-secondary border extra-small" title="{{ __('Drop-off Point') }}">
                                        <i class="fas fa-map-marker-alt text-danger me-1"></i>{{ $booking->dropoff_location }}
                                    </span>
                                @endif
                            </div>
                        @else
                            <span class="text-muted small">---</span>
                        @endif
                    </td>

                    <!-- Assigned Agent -->
                    <td>
                        @if($booking->employee)
                            <span class="badge bg-secondary-subtle text-secondary border extra-small d-inline-flex align-items-center gap-1">
                                <i class="fas fa-user-tie text-primary"></i> {{ $booking->employee->name }}
                            </span>
                        @elseif($booking->salesman_name)
                            <span class="badge bg-light text-dark border extra-small d-inline-flex align-items-center gap-1">
                                <i class="fas fa-user text-muted"></i> {{ $booking->salesman_name }}
                            </span>
                        @else
                            <span class="badge bg-light text-muted border extra-small">
                                <i class="fas fa-user-shield text-muted me-1"></i>{{ __('Admin') }}
                            </span>
                        @endif
                    </td>

                    <!-- Package -->
                    <td>
                        @if($booking->package_type === 'with_hotel')
                            <span class="badge-soft badge-soft-success">
                                <i class="fas fa-hotel me-1"></i> {{ __('With Hotel') }}
                            </span>
                            @if($booking->hotel_name)
                                <div class="text-muted extra-small mt-1 text-truncate" style="max-width: 150px;" title="{{ $booking->hotel_name }}">
                                    {{ $booking->hotel_name }}
                                </div>
                            @endif
                        @else
                            <span class="badge-soft badge-soft-info">
                                <i class="fas fa-bus me-1"></i> {{ __('Bus Only') }}
                            </span>
                        @endif
                    </td>

                    <!-- Financials -->
                    <td>
                        <div class="fw-bold text-dark font-monospace" style="font-size: 0.9rem;">
                            {{ format_currency($booking->total_amount) }}
                        </div>
                        <div class="extra-small text-muted mt-1">
                            {{ __('Paid:') }} <span class="text-success fw-bold">{{ format_currency($booking->paid_amount) }}</span>
                            @if($booking->balance_due > 0)
                                &bull; {{ __('Due:') }} <span class="text-danger fw-bold">{{ format_currency($booking->balance_due) }}</span>
                            @endif
                        </div>
                    </td>

                    <!-- Payment & Booking Status -->
                    <td>
                        @php
                            $pBadge = match($booking->payment_status) {
                                'paid' => 'badge-soft-success',
                                'partial' => 'badge-soft-warning',
                                default => 'badge-soft-danger'
                            };
                        @endphp
                        <span class="badge-soft {{ $pBadge }}">
                            {{ __(ucfirst($booking->payment_status)) }}
                        </span>
                        @if($booking->booking_status === 'cancelled')
                            <span class="badge-soft badge-soft-danger ms-1">{{ __('Cancelled') }}</span>
                        @endif
                    </td>

                    <!-- Action Buttons -->
                    <td class="text-end pe-4">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li>
                                    <a class="dropdown-item" href="{{ route('travel.bookings.show', $booking->id) }}">
                                        <i class="fas fa-eye me-2 text-info"></i> {{ __('View Details') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('travel.bookings.voucher', $booking->id) }}" target="_blank">
                                        <i class="fas fa-print me-2 text-primary"></i> {{ __('Print Ticket Voucher') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('travel.bookings.edit', $booking->id) }}">
                                        <i class="fas fa-edit me-2 text-warning"></i> {{ __('Edit') }}
                                    </a>
                                </li>
                                @if($booking->invoice)
                                    <li>
                                        <a class="dropdown-item" href="{{ route('invoices.show', $booking->invoice_id) }}">
                                            <i class="fas fa-file-invoice-dollar me-2 text-success"></i> {{ __('View Invoice') }}
                                        </a>
                                    </li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                @if($booking->booking_status === 'confirmed')
                                    <li>
                                        <form action="{{ route('travel.bookings.cancel', $booking->id) }}" method="POST" class="cancel-seat-form"
                                              data-seat="{{ $booking->seat_number }}"
                                              data-ref="{{ $booking->booking_reference }}"
                                              data-passenger="{{ $booking->passenger_name }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-ban me-2"></i> {{ __('Cancel Booking & Release Seat') }}
                                            </button>
                                        </form>
                                    </li>
                                @endif
                                <li>
                                    <form action="{{ route('travel.bookings.destroy', $booking->id) }}" method="POST" class="delete-form" data-confirm="{{ __('Are you sure you want to delete this booking record?') }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-muted">
                                            <i class="fas fa-trash-alt me-2"></i> {{ __('Delete Record') }}
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-table>

        @if($bookings->hasPages())
            <div class="p-3 border-top bg-light">
                {{ $bookings->links() }}
            </div>
        @endif
    @endif
</x-card>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Initialize Searchable Filter Dropdowns for Filter Bar (Auto-submits on pick for fast filtering)
    document.querySelectorAll('.filter-bar-select').forEach(function(el) {
        initSearchableFilterSelect(el, {
            onSelect: function() {
                if (el.form) {
                    el.form.submit();
                }
            }
        });
    });

    // 2. Cancellation confirmation with SweetAlert
    document.querySelectorAll('.cancel-seat-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const seat = form.dataset.seat || '';
            const ref = form.dataset.ref || '';
            const passenger = form.dataset.passenger || '';

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '{{ __("Cancel Booking & Free Seat?") }}',
                    html: `
                        <div class="text-center py-2">
                            <p class="mb-1 text-muted">{{ __("Passenger:") }} <strong class="text-dark">${passenger}</strong></p>
                            <span class="badge bg-danger fs-6 px-3 py-1 mb-2">{{ __("Seat #") }}${seat}</span>
                            <div class="small text-muted mt-2">${ref}</div>
                            <div class="alert alert-warning text-start small mt-3 mb-0">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                {{ __("Are you sure you want to cancel this booking? Seat will be immediately freed and available for other bookings.") }}
                            </div>
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-ban me-1"></i> {{ __("Yes, Cancel & Release Seat") }}',
                    cancelButtonText: '{{ __("No, Keep Booking") }}',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-4 shadow-lg'
                    }
                }).then(function(result) {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            } else {
                if (confirm('{{ __("Are you sure you want to cancel this booking and free the seat?") }}')) {
                    form.submit();
                }
            }
        });
    });
});

// Searchable Filter Dropdown Enhancer (Matching Trips & Quotes)
function initSearchableFilterSelect(selectOrId, options = {}) {
    const select = typeof selectOrId === 'string' ? document.getElementById(selectOrId) : selectOrId;
    if (!select || select.dataset.filterEnhanced === 'true') return;
    select.dataset.filterEnhanced = 'true';

    const wrapper = document.createElement('div');
    wrapper.className = 'filter-dropdown';
    if (options.wrapperClass) wrapper.classList.add(options.wrapperClass);

    const trigger = document.createElement('input');
    trigger.type = 'text';
    trigger.className = 'form-control filter-trigger';
    if (select.classList.contains('form-select-sm') || select.classList.contains('form-control-sm')) {
        trigger.classList.add('form-control-sm');
    }
    if (select.classList.contains('is-invalid')) {
        trigger.classList.add('is-invalid');
    }
    trigger.readOnly = true;
    trigger.placeholder = options.placeholder || (select.options[0]?.text || '{{ __("Select...") }}');

    const panel = document.createElement('div');
    panel.className = 'filter-panel';

    const searchWrap = document.createElement('div');
    searchWrap.className = 'filter-search-wrap';

    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.className = 'filter-search';
    searchInput.placeholder = options.searchPlaceholder || '{{ __("Type to search / filter...") }}';

    searchWrap.appendChild(searchInput);
    panel.appendChild(searchWrap);

    const listEl = document.createElement('div');
    listEl.className = 'filter-list';
    panel.appendChild(listEl);

    const emptyEl = document.createElement('div');
    emptyEl.className = 'filter-empty';
    emptyEl.style.display = 'none';
    emptyEl.textContent = options.emptyText || '{{ __("No matching results found") }}';
    listEl.appendChild(emptyEl);

    wrapper.appendChild(trigger);
    wrapper.appendChild(panel);

    select.parentNode.insertBefore(wrapper, select.nextSibling);
    select.style.setProperty('display', 'none', 'important');

    const items = [];

    function buildOptions() {
        while (listEl.firstChild && listEl.firstChild !== emptyEl) {
            listEl.removeChild(listEl.firstChild);
        }
        items.length = 0;

        Array.from(select.children).forEach(function(child) {
            if (child.tagName === 'OPTGROUP') {
                const groupLabel = document.createElement('div');
                groupLabel.className = 'filter-optgroup-label';
                groupLabel.textContent = child.label;
                listEl.insertBefore(groupLabel, emptyEl);

                Array.from(child.children).forEach(function(opt) {
                    addOptionItem(opt, groupLabel);
                });
            } else if (child.tagName === 'OPTION') {
                addOptionItem(child, null);
            }
        });

        syncFromSelect();
    }

    function addOptionItem(opt, groupHeader) {
        const itemEl = document.createElement('div');
        itemEl.className = 'filter-option';
        itemEl.dataset.value = opt.value;

        const nameSpan = document.createElement('span');
        nameSpan.className = 'opt-name';
        nameSpan.textContent = opt.text;

        const icon = document.createElement('i');
        icon.className = 'fas fa-check check-icon';

        itemEl.appendChild(nameSpan);
        itemEl.appendChild(icon);
        listEl.insertBefore(itemEl, emptyEl);

        const itemObj = {
            el: itemEl,
            opt: opt,
            value: opt.value,
            text: opt.text,
            groupHeader: groupHeader,
            searchStr: (opt.text + ' ' + opt.value).toLowerCase()
        };

        itemEl.addEventListener('click', function(e) {
            e.stopPropagation();
            selectOption(itemObj);
        });

        items.push(itemObj);
    }

    function selectOption(itemObj) {
        select.value = itemObj.value;
        syncFromSelect();
        closePanel();
        select.dispatchEvent(new Event('change', { bubbles: true }));
        if (typeof select.onchange === 'function') {
            select.onchange();
        }
        if (options.onSelect) {
            options.onSelect(itemObj.value, itemObj.text, itemObj.opt);
        }
    }

    function syncFromSelect() {
        const curVal = select.value;
        let selectedItem = null;

        items.forEach(function(item) {
            if (item.value === curVal || (item.value === '' && !curVal)) {
                if (!selectedItem) {
                    selectedItem = item;
                    item.el.classList.add('selected');
                } else {
                    item.el.classList.remove('selected');
                }
            } else {
                item.el.classList.remove('selected');
            }
        });

        if (selectedItem) {
            trigger.value = selectedItem.text;
        } else if (select.selectedIndex >= 0 && select.options[select.selectedIndex]) {
            trigger.value = select.options[select.selectedIndex].text;
        } else {
            trigger.value = '';
        }
    }

    function hl(text, q) {
        if (!text) return '';
        if (!q) return escapeHtml(text);
        const rx = new RegExp('(' + q.replace(/[.*+?^${}()|[\]\\]/g,'\\$&') + ')', 'gi');
        return escapeHtml(text).replace(rx, '<mark style="background:#fff3cd;padding:0 2px;border-radius:2px;font-weight:700;">$1</mark>');
    }

    function escapeHtml(s) {
        return (s || '').replace(/[&<>"']/g, function(c){
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
        });
    }

    function filterOptions(q) {
        q = (q || '').toLowerCase().trim();
        let visibleCount = 0;

        items.forEach(function(item) {
            const isMatch = !q || item.searchStr.indexOf(q) > -1;
            item.el.style.display = isMatch ? 'flex' : 'none';
            const nameSpan = item.el.querySelector('.opt-name');
            if (nameSpan) {
                nameSpan.innerHTML = isMatch && q ? hl(item.text, q) : escapeHtml(item.text);
            }
            if (isMatch) visibleCount++;
        });

        listEl.querySelectorAll('.filter-optgroup-label').forEach(function(gh) {
            let hasVisible = false;
            let sibling = gh.nextElementSibling;
            while (sibling && !sibling.classList.contains('filter-optgroup-label') && sibling !== emptyEl) {
                if (sibling.style.display !== 'none') {
                    hasVisible = true;
                    break;
                }
                sibling = sibling.nextElementSibling;
            }
            gh.style.display = hasVisible ? 'block' : 'none';
        });

        emptyEl.style.display = visibleCount === 0 ? 'block' : 'none';
    }

    function openPanel() {
        document.querySelectorAll('.filter-panel.open').forEach(p => p !== panel && p.classList.remove('open'));
        document.querySelectorAll('.filter-trigger.active').forEach(t => t !== trigger && t.classList.remove('active'));

        panel.classList.add('open');
        trigger.classList.add('active');
        searchInput.value = '';
        filterOptions('');
        setTimeout(() => searchInput.focus(), 50);
    }

    function closePanel() {
        panel.classList.remove('open');
        trigger.classList.remove('active');
    }

    trigger.addEventListener('click', function(e) {
        e.stopPropagation();
        panel.classList.contains('open') ? closePanel() : openPanel();
    });

    searchInput.addEventListener('input', function() {
        filterOptions(this.value);
    });

    searchInput.addEventListener('click', function(e) {
        e.stopPropagation();
    });

    document.addEventListener('click', function(e) {
        if (!wrapper.contains(e.target)) {
            closePanel();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closePanel();
    });

    select.addEventListener('change', function() {
        syncFromSelect();
    });

    // Auto-rebuild if options or option text mutated dynamically
    const observer = new MutationObserver(function() {
        buildOptions();
    });
    observer.observe(select, { childList: true, subtree: true, characterData: true });

    buildOptions();
    return wrapper;
}
</script>
@endpush
@endsection
