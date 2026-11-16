@extends('admin.layouts.app')

@section('title', __('Umrah & Travel Trips'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="fw-bold text-dark mb-0">
                <i class="fas fa-kaaba text-success me-2"></i>{{ __('Umrah & Bus Travel Trips') }}
            </h2>
            <span class="badge bg-primary text-white rounded-pill px-3 py-1">
                {{ __('Coach Fleet Operations') }}
            </span>
        </div>
        <p class="text-muted small mb-0">{{ __('Manage bus fleet schedules, 49-seat coach occupancy, hotel accommodation packages, and passenger boarding rosters.') }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('travel.bookings.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-ticket-alt me-1"></i> {{ __('All Bookings') }}
        </a>
        <a href="{{ route('travel.trips.create') }}" class="btn btn-primary-modern shadow-sm">
            <i class="fas fa-plus me-1"></i> {{ __('Schedule New Trip') }}
        </a>
    </div>
</div>

<!-- 4 ERP KPI Metric Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-primary shadow-xs" 
                     style="width: 52px; height: 52px; background: rgba(67, 97, 238, 0.12); font-size: 1.35rem; flex-shrink: 0;">
                    <i class="fas fa-bus-alt"></i>
                </div>
                <div>
                    <div class="text-muted extra-small text-uppercase fw-bold">{{ __('Total Trips') }}</div>
                    <h3 class="fw-bold text-dark mb-0">{{ $stats['total_trips'] }}</h3>
                    <span class="extra-small text-muted">{{ __('Scheduled coach bus routes') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-info shadow-xs" 
                     style="width: 52px; height: 52px; background: rgba(14, 165, 233, 0.12); font-size: 1.35rem; flex-shrink: 0;">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div>
                    <div class="text-muted extra-small text-uppercase fw-bold">{{ __('Upcoming Departures') }}</div>
                    <h3 class="fw-bold text-info mb-0">{{ $stats['upcoming_trips'] }}</h3>
                    <span class="extra-small text-muted">{{ __('Ready for passenger boarding') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-success shadow-xs" 
                     style="width: 52px; height: 52px; background: rgba(16, 185, 129, 0.12); font-size: 1.35rem; flex-shrink: 0;">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <div class="text-muted extra-small text-uppercase fw-bold">{{ __('Confirmed Pilgrims') }}</div>
                    <h3 class="fw-bold text-success mb-0">{{ $stats['total_bookings'] }}</h3>
                    <span class="extra-small text-muted">{{ __('Booked travelers & tickets') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-warning shadow-xs" 
                     style="width: 52px; height: 52px; background: rgba(245, 158, 11, 0.12); font-size: 1.35rem; flex-shrink: 0;">
                    <i class="fas fa-coins"></i>
                </div>
                <div>
                    <div class="text-muted extra-small text-uppercase fw-bold">{{ __('Total Revenue') }}</div>
                    <h3 class="fw-bold text-dark mb-0">{{ format_currency($stats['total_revenue']) }}</h3>
                    <span class="extra-small text-success fw-semibold"><i class="fas fa-arrow-up me-1"></i>{{ __('Total Ticket Sales') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Bar Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
    <div class="card-body p-3">
        <form action="{{ route('travel.trips.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="{{ __('Search trip headline, code, bus model, departure city...') }}" value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">{{ __('All Trip Statuses') }}</option>
                    <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>{{ __('Upcoming Departures') }}</option>
                    <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>{{ __('Ongoing On-Road') }}</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>{{ __('Completed Trips') }}</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary-modern flex-grow-1">
                    <i class="fas fa-filter me-1"></i> {{ __('Filter') }}
                </button>
                @if(request()->has('search') || request()->has('status'))
                    <a href="{{ route('travel.trips.index') }}" class="btn btn-light border" title="{{ __('Clear Filters') }}">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Trips Cards Grid -->
<div class="row g-4">
    @forelse($trips as $trip)
        @php
            $bookedCount = $trip->confirmed_bookings_count ?? $trip->bookedSeatsCount();
            $totalSeats = max(1, $trip->total_seats ?: 49);
            $availableSeats = max(0, $totalSeats - $bookedCount);
            $occupancyPercent = min(100, round(($bookedCount / $totalSeats) * 100));
            $statusBadge = match($trip->status) {
                'upcoming' => 'badge-soft-info',
                'ongoing' => 'badge-soft-warning',
                'completed' => 'badge-soft-success',
                'cancelled' => 'badge-soft-danger',
                default => 'badge-soft-secondary'
            };
            $progressColor = $occupancyPercent >= 90 ? 'bg-danger' : ($occupancyPercent >= 60 ? 'bg-warning' : 'bg-primary');
        @endphp
        <div class="col-lg-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden bg-white d-flex flex-column">
                <!-- Card Header -->
                <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary text-white fw-bold font-monospace">{{ $trip->trip_code }}</span>
                        <span class="badge-soft {{ $statusBadge }}">{{ __(ucfirst($trip->status)) }}</span>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light border p-1 px-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v text-muted"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border">
                            <li><a class="dropdown-item" href="{{ route('travel.trips.show', $trip->id) }}"><i class="fas fa-bus text-primary me-2"></i> {{ __('Seat Layout & Roster') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('travel.bookings.create', ['trip_id' => $trip->id]) }}"><i class="fas fa-ticket-alt text-success me-2"></i> {{ __('Book Passenger Seat') }}</a></li>
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

                <!-- Card Body -->
                <div class="card-body p-3 d-flex flex-column flex-grow-1">
                    <h5 class="fw-bold text-dark mb-2">
                        <a href="{{ route('travel.trips.show', $trip->id) }}" class="text-decoration-none text-dark hover-primary">
                            {{ $trip->title }}
                        </a>
                    </h5>

                    <!-- Route Headline -->
                    <div class="d-flex align-items-center gap-2 mb-3 text-muted small">
                        <i class="fas fa-map-marker-alt text-danger"></i>
                        <strong class="text-dark">{{ $trip->departure_city }}</strong>
                        <i class="fas fa-arrow-right text-muted mx-1"></i>
                        <strong class="text-primary">{{ $trip->destination_city }}</strong>
                        <span class="badge bg-light text-muted border ms-auto">{{ $trip->duration_days }} {{ __('Days') }}</span>
                    </div>

                    <!-- Bus Fleet Info Strip -->
                    <div class="p-2 bg-light rounded-3 mb-3 d-flex justify-content-between align-items-center small border">
                        <div>
                            <i class="fas fa-bus text-primary me-1"></i>
                            <span class="fw-semibold text-dark">{{ $trip->bus_name }}</span>
                            @if($trip->bus_number)
                                <span class="text-muted">({{ $trip->bus_number }})</span>
                            @endif
                        </div>
                        <span class="badge bg-white text-dark border">{{ $totalSeats }} {{ __('Total Seats') }}</span>
                    </div>

                    <!-- Departure & Return Dates -->
                    <div class="row g-2 mb-3 extra-small">
                        <div class="col-6">
                            <span class="text-muted d-block uppercase fw-semibold">{{ __('Departure') }}:</span>
                            <strong class="text-dark d-block text-truncate">
                                <i class="fas fa-calendar-alt text-success me-1"></i>{{ $trip->departure_date ? $trip->departure_date->format('d M, Y - h:i A') : 'N/A' }}
                            </strong>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block uppercase fw-semibold">{{ __('Return') }}:</span>
                            <strong class="text-dark d-block text-truncate">
                                <i class="fas fa-calendar-check text-info me-1"></i>{{ $trip->return_date ? $trip->return_date->format('d M, Y') : __('Open Return') }}
                            </strong>
                        </div>
                    </div>

                    <!-- Live 49-Seat Occupancy Progress Bar -->
                    <div class="mb-3 mt-auto">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>
                                <strong class="text-dark">{{ $bookedCount }}</strong> / {{ $totalSeats }} {{ __('Seats Booked') }}
                            </span>
                            <span class="fw-bold {{ $availableSeats > 0 ? 'text-success' : 'text-danger' }}">
                                {{ $availableSeats }} {{ __('Seats Free') }} ({{ $occupancyPercent }}%)
                            </span>
                        </div>
                        <div class="progress" style="height: 7px; border-radius: 4px; background: #eef2f6;">
                            <div class="progress-bar {{ $progressColor }}" 
                                 role="progressbar" style="width: {{ $occupancyPercent }}%;" 
                                 aria-valuenow="{{ $occupancyPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <!-- Package Pricing Comparison -->
                    <div class="border rounded-3 p-2 bg-light d-flex justify-content-between align-items-center small">
                        <div>
                            <span class="text-muted extra-small d-block">{{ __('Fare Only (No Hotel):') }}</span>
                            <strong class="text-dark fs-6 font-monospace">{{ format_currency($trip->price_without_hotel) }}</strong>
                        </div>
                        <div class="text-end">
                            <span class="text-muted extra-small d-block">{{ __('Full Package (With Hotel):') }}</span>
                            <strong class="text-success fs-6 font-monospace">{{ format_currency($trip->price_with_hotel) }}</strong>
                        </div>
                    </div>

                    @if($trip->hotel_name)
                        <div class="mt-2 text-muted extra-small">
                            <i class="fas fa-hotel text-warning me-1"></i> {{ __('Hotel:') }} <strong class="text-dark">{{ $trip->hotel_name }}</strong> ({{ $trip->hotel_rating ?: __('5-Star') }})
                        </div>
                    @endif
                </div>

                <!-- Card Footer Actions -->
                <div class="card-footer bg-white border-top p-3 d-flex gap-2">
                    <a href="{{ route('travel.trips.show', $trip->id) }}" class="btn btn-sm btn-outline-primary flex-grow-1">
                        <i class="fas fa-th-large me-1"></i> {{ __('View Bus Layout') }}
                    </a>
                    @if($availableSeats > 0)
                        <a href="{{ route('travel.bookings.create', ['trip_id' => $trip->id]) }}" class="btn btn-sm btn-primary-modern flex-grow-1 shadow-sm">
                            <i class="fas fa-plus me-1"></i> {{ __('Book Seat') }}
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
            <div class="p-5 text-center bg-white rounded-4 border-0 shadow-sm">
                <i class="fas fa-bus-alt text-muted fa-4x opacity-25 mb-3"></i>
                <h4 class="fw-bold text-dark">{{ __('No Trips Scheduled Yet') }}</h4>
                <p class="text-muted small">{{ __('Create your first Umrah trip or luxury coach bus route to begin reserving seats and registering pilgrims.') }}</p>
                <a href="{{ route('travel.trips.create') }}" class="btn btn-primary-modern mt-2">
                    <i class="fas fa-plus me-1"></i> {{ __('Schedule First Trip') }}
                </a>
            </div>
        </div>
    @endforelse
</div>

@if($trips->hasPages())
    <div class="mt-4">
        {{ $trips->links() }}
    </div>
@endif
@endsection
