@extends('admin.layouts.app')

@section('title', __('Umrah & Travel Trips'))

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="fas fa-kaaba text-success me-2"></i>{{ __('Umrah & Bus Travel Trips') }}
        </h2>
        <p class="text-muted small mb-0">{{ __('Manage bus fleet trips, passenger seat occupancy, hotel accommodation packages, and travel schedules.') }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('travel.bookings.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-ticket-alt me-1"></i> {{ __('All Bookings') }}
        </a>
        <a href="{{ route('travel.trips.create') }}" class="btn btn-primary-modern">
            <i class="fas fa-plus-circle me-1"></i> {{ __('Add New Trip') }}
        </a>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="p-3 bg-white rounded-4 border shadow-xs h-100 d-flex align-items-center gap-3">
            <div class="p-3 rounded-circle bg-primary-subtle text-primary fs-4" style="width: 52px; height: 52px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-bus-alt"></i>
            </div>
            <div>
                <div class="text-muted small uppercase fw-bold" style="font-size: 0.72rem;">{{ __('Total Trips') }}</div>
                <h3 class="fw-bold text-dark mb-0">{{ $stats['total_trips'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="p-3 bg-white rounded-4 border shadow-xs h-100 d-flex align-items-center gap-3">
            <div class="p-3 rounded-circle bg-info-subtle text-info fs-4" style="width: 52px; height: 52px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div>
                <div class="text-muted small uppercase fw-bold" style="font-size: 0.72rem;">{{ __('Upcoming Departures') }}</div>
                <h3 class="fw-bold text-info mb-0">{{ $stats['upcoming_trips'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="p-3 bg-white rounded-4 border shadow-xs h-100 d-flex align-items-center gap-3">
            <div class="p-3 rounded-circle bg-success-subtle text-success fs-4" style="width: 52px; height: 52px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <div class="text-muted small uppercase fw-bold" style="font-size: 0.72rem;">{{ __('Confirmed Pilgrims') }}</div>
                <h3 class="fw-bold text-success mb-0">{{ $stats['total_bookings'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="p-3 bg-white rounded-4 border shadow-xs h-100 d-flex align-items-center gap-3">
            <div class="p-3 rounded-circle bg-warning-subtle text-warning fs-4" style="width: 52px; height: 52px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-coins"></i>
            </div>
            <div>
                <div class="text-muted small uppercase fw-bold" style="font-size: 0.72rem;">{{ __('Total Trip Revenue') }}</div>
                <h3 class="fw-bold text-dark mb-0">{{ currency_symbol() }}{{ number_format($stats['total_revenue'], 2) }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="p-3 bg-white rounded-4 border shadow-xs mb-4">
    <form action="{{ route('travel.trips.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="{{ __('Search trip title, code, bus, city...') }}" value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">{{ __('All Statuses') }}</option>
                <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>{{ __('Upcoming') }}</option>
                <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>{{ __('Ongoing') }}</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
            </select>
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary-modern flex-grow-1">
                <i class="fas fa-filter me-1"></i> {{ __('Filter') }}
            </button>
            <a href="{{ route('travel.trips.index') }}" class="btn btn-light border">
                {{ __('Reset') }}
            </a>
        </div>
    </form>
</div>

<!-- Trips Cards Grid -->
<div class="row g-4">
    @forelse($trips as $trip)
        @php
            $bookedCount = $trip->confirmed_bookings_count ?? $trip->bookedSeatsCount();
            $totalSeats = $trip->total_seats;
            $availableSeats = max(0, $totalSeats - $bookedCount);
            $occupancyPercent = $totalSeats > 0 ? round(($bookedCount / $totalSeats) * 100) : 0;
            $statusClass = match($trip->status) {
                'upcoming' => 'badge-soft-info',
                'ongoing' => 'badge-soft-warning',
                'completed' => 'badge-soft-success',
                'cancelled' => 'badge-soft-danger',
                default => 'badge-soft-secondary'
            };
        @endphp
        <div class="col-lg-6 col-xl-4">
            <div class="card h-100 border rounded-4 shadow-xs overflow-hidden">
                <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge bg-light text-primary border fw-bold">{{ $trip->trip_code }}</span>
                        <span class="badge {{ $statusClass }} ms-1">{{ __(ucfirst($trip->status)) }}</span>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light border p-1 px-2" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border">
                            <li><a class="dropdown-item" href="{{ route('travel.trips.show', $trip->id) }}"><i class="fas fa-bus text-primary me-2"></i> {{ __('Seat Layout & Roster') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('travel.bookings.create', ['trip_id' => $trip->id]) }}"><i class="fas fa-ticket-alt text-success me-2"></i> {{ __('Book Passenger') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('travel.trips.edit', $trip->id) }}"><i class="fas fa-edit text-warning me-2"></i> {{ __('Edit Trip') }}</a></li>
                            @if($bookedCount == 0)
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('travel.trips.destroy', $trip->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this trip?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash me-2"></i> {{ __('Delete') }}</button>
                                    </form>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                <div class="card-body p-3">
                    <h5 class="fw-bold text-dark mb-2">
                        <a href="{{ route('travel.trips.show', $trip->id) }}" class="text-decoration-none text-dark">
                            {{ $trip->title }}
                        </a>
                    </h5>

                    <div class="d-flex align-items-center gap-2 mb-3 text-muted small">
                        <i class="fas fa-route text-primary"></i>
                        <strong>{{ $trip->departure_city }}</strong>
                        <i class="fas fa-arrow-right text-muted mx-1"></i>
                        <strong>{{ $trip->destination_city }}</strong>
                        <span class="badge bg-light text-dark border ms-auto">{{ $trip->duration_days }} {{ __('Days') }}</span>
                    </div>

                    <!-- Bus Info -->
                    <div class="p-2 bg-light rounded-3 mb-3 d-flex justify-content-between align-items-center small">
                        <div>
                            <i class="fas fa-bus text-secondary me-1"></i>
                            <span class="fw-semibold text-dark">{{ $trip->bus_name }}</span>
                            @if($trip->bus_number)
                                <span class="text-muted">({{ $trip->bus_number }})</span>
                            @endif
                        </div>
                        <span class="badge bg-white text-dark border">{{ $totalSeats }} {{ __('Total Seats') }}</span>
                    </div>

                    <!-- Departure & Return Dates -->
                    <div class="row g-2 mb-3 extra-small" style="font-size: 0.78rem;">
                        <div class="col-6">
                            <span class="text-muted d-block">{{ __('Departure:') }}</span>
                            <strong class="text-dark"><i class="fas fa-plane-departure text-success me-1"></i>{{ $trip->departure_date ? $trip->departure_date->format('d M, Y - h:i A') : 'N/A' }}</strong>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block">{{ __('Return:') }}</span>
                            <strong class="text-dark"><i class="fas fa-plane-arrival text-info me-1"></i>{{ $trip->return_date ? $trip->return_date->format('d M, Y') : __('Open') }}</strong>
                        </div>
                    </div>

                    <!-- Seat Occupancy Progress -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>
                                <strong>{{ $bookedCount }}</strong> / {{ $totalSeats }} {{ __('Seats Booked') }}
                            </span>
                            <span class="fw-bold {{ $availableSeats > 0 ? 'text-success' : 'text-danger' }}">
                                {{ $availableSeats }} {{ __('Seats Available') }}
                            </span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar {{ $occupancyPercent >= 90 ? 'bg-danger' : ($occupancyPercent >= 60 ? 'bg-warning' : 'bg-success') }}" 
                                 role="progressbar" style="width: {{ $occupancyPercent }}%;"></div>
                        </div>
                    </div>

                    <!-- Package Pricing Comparison -->
                    <div class="border rounded-3 p-2 bg-light-subtle d-flex justify-content-between align-items-center small">
                        <div>
                            <span class="text-muted extra-small d-block">{{ __('Travel Only (No Hotel):') }}</span>
                            <strong class="text-dark fs-6">{{ currency_symbol() }}{{ number_format($trip->price_without_hotel, 2) }}</strong>
                        </div>
                        <div class="text-end">
                            <span class="text-muted extra-small d-block">{{ __('Complete (With Hotel):') }}</span>
                            <strong class="text-success fs-6">{{ currency_symbol() }}{{ number_format($trip->price_with_hotel, 2) }}</strong>
                        </div>
                    </div>

                    @if($trip->hotel_name)
                        <div class="mt-2 text-muted extra-small" style="font-size: 0.75rem;">
                            <i class="fas fa-hotel text-warning me-1"></i> {{ __('Accommodation:') }} <strong class="text-dark">{{ $trip->hotel_name }}</strong> ({{ $trip->hotel_rating ?: __('Standard') }})
                        </div>
                    @endif
                </div>

                <div class="card-footer bg-white border-top p-3 d-flex gap-2">
                    <a href="{{ route('travel.trips.show', $trip->id) }}" class="btn btn-sm btn-outline-primary flex-grow-1">
                        <i class="fas fa-th me-1"></i> {{ __('View Bus Layout') }}
                    </a>
                    @if($availableSeats > 0)
                        <a href="{{ route('travel.bookings.create', ['trip_id' => $trip->id]) }}" class="btn btn-sm btn-primary-modern flex-grow-1">
                            <i class="fas fa-ticket-alt me-1"></i> {{ __('Book Seat') }}
                        </a>
                    @else
                        <button class="btn btn-sm btn-secondary flex-grow-1" disabled>
                            <i class="fas fa-ban me-1"></i> {{ __('Sold Out') }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="p-5 text-center bg-white rounded-4 border shadow-xs">
                <div class="mb-3">
                    <i class="fas fa-bus-alt text-muted fa-4x opacity-50"></i>
                </div>
                <h4 class="fw-bold text-dark">{{ __('No trips recorded yet') }}</h4>
                <p class="text-muted">{{ __('Create your first Umrah trip or luxury bus route to begin reserving seats and registering passengers.') }}</p>
                <a href="{{ route('travel.trips.create') }}" class="btn btn-primary-modern mt-2">
                    <i class="fas fa-plus-circle me-1"></i> {{ __('Create First Trip') }}
                </a>
            </div>
        </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $trips->links() }}
</div>
@endsection
