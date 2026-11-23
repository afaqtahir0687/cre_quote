@extends('admin.layouts.app')

@section('title', __('Umrah & Bus Seat Bookings'))

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
        <p class="text-muted small mb-0">{{ __('Manage passenger manifests, allocated 49-seat coach reservations, package hotels, and boarding pass vouchers.') }}</p>
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
                    <span class="extra-small text-muted">{{ __('All registered passenger tickets') }}</span>
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
                    <span class="extra-small text-muted">{{ __('Pilgrims with locked seats') }}</span>
                </div>
            </div>
        </div>
    </div>

    @if(!$isEmployee)
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
                    <span class="extra-small text-success fw-semibold"><i class="fas fa-arrow-up me-1"></i>{{ __('Realized Revenue') }}</span>
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
                    <span class="extra-small text-muted">{{ __('To collect before boarding') }}</span>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- 3. Active Trips -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-info shadow-xs" 
                     style="width: 52px; height: 52px; background: rgba(14, 165, 233, 0.12); font-size: 1.35rem; flex-shrink: 0;">
                    <i class="fas fa-bus"></i>
                </div>
                <div>
                    <div class="text-muted extra-small text-uppercase fw-bold">{{ __('Active Scheduled Trips') }}</div>
                    <h3 class="fw-bold text-dark mb-0">{{ $trips->count() }}</h3>
                    <span class="extra-small text-muted">{{ __('Ready for seat booking') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Quick Actions -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-success shadow-xs" 
                     style="width: 52px; height: 52px; background: rgba(16, 185, 129, 0.12); font-size: 1.35rem; flex-shrink: 0;">
                    <i class="fas fa-chair"></i>
                </div>
                <div>
                    <div class="text-muted extra-small text-uppercase fw-bold">{{ __('Bus Availability') }}</div>
                    <a href="{{ route('travel.trips.index') }}" class="btn btn-sm btn-outline-success mt-1 py-0 px-2 fw-semibold">
                        <i class="fas fa-eye me-1"></i> {{ __('View Bus Seat Occupancy') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Filters Bar Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
    <div class="card-body p-3">
        <form action="{{ route('travel.bookings.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-{{ isset($branches) && $branches->count() > 0 ? '3' : '4' }}">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="{{ __('Search passenger name, booking ref, phone, ID/passport...') }}" value="{{ request('search') }}">
                </div>
            </div>
            @if(isset($branches) && $branches->count() > 0)
                <div class="col-md-2">
                    <select name="branch_id" class="form-select">
                        <option value="">{{ __('All Branches') }}</option>
                        <option value="{{ current_shop_id() }}" {{ request('branch_id') == current_shop_id() ? 'selected' : '' }}>{{ current_shop()->name }} ({{ __('Head Office') }})</option>
                        @foreach($branches as $br)
                            <option value="{{ $br->id }}" {{ request('branch_id') == $br->id ? 'selected' : '' }}>{{ $br->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="col-md-2">
                <select name="trip_id" class="form-select">
                    <option value="">{{ __('All Scheduled Trips') }}</option>
                    @foreach($trips as $t)
                        <option value="{{ $t->id }}" {{ request('trip_id') == $t->id ? 'selected' : '' }}>
                            {{ $t->trip_code }} &bull; {{ $t->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="employee_id" class="form-select">
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
            <div class="col-md-2">
                <select name="payment_status" class="form-select">
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
                                <li>
                                    <form action="{{ route('travel.bookings.destroy', $booking->id) }}" method="POST" class="delete-form" onsubmit="return confirm('{{ __('Are you sure you want to cancel this booking and free the seat?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-ban me-2"></i> {{ __('Cancel Booking') }}
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
@endsection
