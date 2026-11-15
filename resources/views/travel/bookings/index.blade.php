@extends('admin.layouts.app')

@section('title', __('Umrah & Bus Seat Bookings'))

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="fas fa-ticket-alt text-primary me-2"></i>{{ __('Umrah & Bus Seat Bookings') }}
        </h2>
        <p class="text-muted small mb-0">{{ __('Manage passenger reservations, bus seat allocations, package payments, and boarding passes.') }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('travel.trips.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-bus me-1"></i> {{ __('View Trips & Fleet') }}
        </a>
        <a href="{{ route('travel.bookings.create') }}" class="btn btn-primary-modern">
            <i class="fas fa-plus-circle me-1"></i> {{ __('Book a Seat') }}
        </a>
    </div>
</div>

<!-- KPI Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="p-3 bg-white rounded-4 border shadow-xs h-100 d-flex align-items-center gap-3">
            <div class="p-3 rounded-circle bg-primary-subtle text-primary fs-4" style="width: 52px; height: 52px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-ticket-alt"></i>
            </div>
            <div>
                <div class="text-muted small uppercase fw-bold" style="font-size: 0.72rem;">{{ __('Total Bookings') }}</div>
                <h3 class="fw-bold text-dark mb-0">{{ $stats['total_bookings'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="p-3 bg-white rounded-4 border shadow-xs h-100 d-flex align-items-center gap-3">
            <div class="p-3 rounded-circle bg-success-subtle text-success fs-4" style="width: 52px; height: 52px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-user-check"></i>
            </div>
            <div>
                <div class="text-muted small uppercase fw-bold" style="font-size: 0.72rem;">{{ __('Confirmed Seats') }}</div>
                <h3 class="fw-bold text-success mb-0">{{ $stats['confirmed_bookings'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="p-3 bg-white rounded-4 border shadow-xs h-100 d-flex align-items-center gap-3">
            <div class="p-3 rounded-circle bg-info-subtle text-info fs-4" style="width: 52px; height: 52px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
            <div>
                <div class="text-muted small uppercase fw-bold" style="font-size: 0.72rem;">{{ __('Cash Received') }}</div>
                <h3 class="fw-bold text-info mb-0">{{ currency_symbol() }}{{ number_format($stats['paid_amount'], 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="p-3 bg-white rounded-4 border shadow-xs h-100 d-flex align-items-center gap-3">
            <div class="p-3 rounded-circle bg-danger-subtle text-danger fs-4" style="width: 52px; height: 52px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <div class="text-muted small uppercase fw-bold" style="font-size: 0.72rem;">{{ __('Balance Due') }}</div>
                <h3 class="fw-bold text-danger mb-0">{{ currency_symbol() }}{{ number_format($stats['balance_due'], 2) }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Filters Bar -->
<div class="p-3 bg-white rounded-4 border shadow-xs mb-4">
    <form action="{{ route('travel.bookings.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="{{ __('Search passenger, ref, phone, passport...') }}" value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
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
            <select name="payment_status" class="form-select">
                <option value="">{{ __('All Payments') }}</option>
                <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
                <option value="partial" {{ request('payment_status') === 'partial' ? 'selected' : '' }}>{{ __('Partial') }}</option>
                <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>{{ __('Unpaid') }}</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary-modern flex-grow-1">
                <i class="fas fa-filter me-1"></i> {{ __('Filter') }}
            </button>
            <a href="{{ route('travel.bookings.index') }}" class="btn btn-light border">
                {{ __('Reset') }}
            </a>
        </div>
    </form>
</div>

<!-- Bookings Table -->
<div class="card border rounded-4 shadow-xs bg-white overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="small text-muted text-uppercase">
                    <th style="width: 70px;">{{ __('Seat') }}</th>
                    <th>{{ __('Booking Ref') }}</th>
                    <th>{{ __('Passenger Details') }}</th>
                    <th>{{ __('Trip & Bus Route') }}</th>
                    <th>{{ __('Package Type') }}</th>
                    <th>{{ __('Financials') }}</th>
                    <th>{{ __('Payment') }}</th>
                    <th class="text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                    <tr>
                        <td>
                            <div class="p-2 rounded-3 text-center fw-bold bg-primary text-white" style="width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                                #{{ $booking->seat_number }}
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-primary border fw-bold">{{ $booking->booking_reference }}</span>
                            <div class="text-muted extra-small mt-1">{{ $booking->created_at->format('d M, Y') }}</div>
                        </td>
                        <td>
                            <a href="{{ route('travel.bookings.show', $booking->id) }}" class="fw-bold text-dark text-decoration-none">
                                {{ $booking->passenger_name }}
                            </a>
                            <div class="text-muted extra-small">
                                <i class="fas fa-phone-alt me-1 text-primary"></i>{{ $booking->passenger_phone }}
                                @if($booking->passenger_id_number)
                                    &bull; <i class="fas fa-id-card ms-1 me-1 text-secondary"></i>{{ $booking->passenger_id_number }}
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($booking->trip)
                                <a href="{{ route('travel.trips.show', $booking->trip->id) }}" class="fw-semibold text-dark text-decoration-none small d-block">
                                    {{ $booking->trip->title }}
                                </a>
                                <span class="text-muted extra-small">
                                    <i class="fas fa-bus me-1"></i>{{ $booking->trip->bus_name }} &bull; {{ $booking->trip->departure_date ? $booking->trip->departure_date->format('d M, Y') : 'N/A' }}
                                </span>
                            @else
                                <span class="text-muted small">---</span>
                            @endif
                        </td>
                        <td>
                            @if($booking->package_type === 'with_hotel')
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    <i class="fas fa-hotel me-1"></i> {{ __('With Hotel') }}
                                </span>
                                @if($booking->hotel_name)
                                    <div class="text-muted extra-small mt-1" style="font-size: 0.72rem;">{{ $booking->hotel_name }}</div>
                                @endif
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                    <i class="fas fa-bus me-1"></i> {{ __('Seat Only') }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="small">
                                <strong>{{ currency_symbol() }}{{ number_format($booking->total_amount, 2) }}</strong>
                                <div class="extra-small text-muted">
                                    {{ __('Paid:') }} <span class="text-success">{{ currency_symbol() }}{{ number_format($booking->paid_amount, 2) }}</span>
                                    @if($booking->balance_due > 0)
                                        &bull; {{ __('Due:') }} <span class="text-danger fw-bold">{{ currency_symbol() }}{{ number_format($booking->balance_due, 2) }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ $booking->paymentBadgeClass() }}">
                                {{ __(ucfirst($booking->payment_status)) }}
                            </span>
                            @if($booking->booking_status === 'cancelled')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle ms-1">{{ __('Cancelled') }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('travel.bookings.voucher', $booking->id) }}" target="_blank" class="btn btn-sm btn-light border p-1 px-2" title="{{ __('Print Ticket / Boarding Pass') }}">
                                    <i class="fas fa-print text-primary"></i>
                                </a>
                                <a href="{{ route('travel.bookings.show', $booking->id) }}" class="btn btn-sm btn-light border p-1 px-2" title="{{ __('View Details') }}">
                                    <i class="fas fa-eye text-info"></i>
                                </a>
                                <a href="{{ route('travel.bookings.edit', $booking->id) }}" class="btn btn-sm btn-light border p-1 px-2" title="{{ __('Edit') }}">
                                    <i class="fas fa-edit text-warning"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fas fa-ticket-alt fa-3x opacity-25 mb-2 d-block"></i>
                            {{ __('No passenger bookings found.') }}
                            <div class="mt-2">
                                <a href="{{ route('travel.bookings.create') }}" class="btn btn-sm btn-primary-modern">
                                    <i class="fas fa-plus-circle me-1"></i> {{ __('Book a Seat Now') }}
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($bookings->hasPages())
        <div class="p-3 border-top">
            {{ $bookings->links() }}
        </div>
    @endif
</div>
@endsection
