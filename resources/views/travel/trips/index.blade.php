@extends('admin.layouts.app')

@section('title', __('Umrah & Travel Trips'))

@section('content')
<!-- Page Header (Matching Job Orders, Quotes & Invoices) -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark mb-0">{{ __('Umrah & Bus Travel Trips') }}</h2>
    <div class="d-flex gap-2">
        <form action="{{ url()->current() }}" method="GET" class="d-flex gap-2 mb-0">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0" placeholder="{{ __('Search Trip, Route, Code...') }}" onkeypress="if(event.keyCode == 13) this.form.submit()">
            </div>
            <select name="status" class="form-select" style="max-width: 170px;" onchange="this.form.submit()">
                <option value="">{{ __('All Statuses') }}</option>
                <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>{{ __('Upcoming') }}</option>
                <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>{{ __('Ongoing') }}</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
            </select>
            @if(request()->has('search') || request()->has('status'))
                <a href="{{ url()->current() }}" class="btn btn-light border" title="{{ __('Clear Filters') }}"><i class="fas fa-times"></i></a>
            @endif
        </form>
        <a href="{{ route('travel.bookings.index') }}" class="btn btn-outline-secondary text-nowrap">
            <i class="fas fa-ticket-alt me-1"></i> {{ __('All Bookings') }}
        </a>
        <a href="{{ route('travel.trips.create') }}" class="btn btn-primary-modern text-nowrap">
            <i class="fas fa-plus me-2"></i> {{ __('Schedule New Trip') }}
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

    @if(!$isEmployee)
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
    @else
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-success shadow-xs" 
                     style="width: 52px; height: 52px; background: rgba(16, 185, 129, 0.12); font-size: 1.35rem; flex-shrink: 0;">
                    <i class="fas fa-chair"></i>
                </div>
                <div>
                    <div class="text-muted extra-small text-uppercase fw-bold">{{ __('Available Seats') }}</div>
                    <h3 class="fw-bold text-success mb-0">{{ $stats['available_seats'] }}</h3>
                    <span class="extra-small text-muted">{{ __('Total vacant seats for booking') }}</span>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Trips ERP Table Card (Exact look & feel of Job Orders & Quotes) -->
<x-card :padding="false">
    @if($trips->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-bus-alt fa-3x text-muted mb-3 opacity-25"></i>
            <h5 class="text-muted fw-bold">{{ __('No Trips Scheduled Yet') }}</h5>
            <p class="text-muted small">{{ __('Create your first Umrah trip or luxury coach bus route to begin reserving seats and registering pilgrims.') }}</p>
            <a href="{{ route('travel.trips.create') }}" class="btn btn-primary-modern mt-2">
                <i class="fas fa-plus me-1"></i> {{ __('Schedule First Trip') }}
            </a>
        </div>
    @else
        <x-table>
            <x-slot name="thead">
                <th class="text-nowrap" style="width: 100px;">{{ __('Trip Code') }}</th>
                <th style="min-width: 190px;">{{ __('Trip / Route') }}</th>
                <th class="text-nowrap" style="min-width: 150px;">{{ __('Coach Bus') }}</th>
                <th class="text-nowrap" style="min-width: 165px;">{{ __('Departure & Duration') }}</th>
                <th style="min-width: 140px;">{{ __('Seat Occupancy') }}</th>
                <th class="text-nowrap" style="min-width: 130px;">{{ __('Counter Access') }}</th>
                <th class="text-nowrap">{{ __('Fares') }}</th>
                <th class="text-nowrap">{{ __('Status') }}</th>
                <th class="text-end pe-3 text-nowrap" style="width: 70px;">{{ __('Action') }}</th>
            </x-slot>

            @foreach($trips as $trip)
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
                <tr class="align-middle">
                    <td class="text-nowrap">
                        <span class="badge-soft badge-soft-primary fw-bold font-monospace">
                            {{ $trip->trip_code }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('travel.trips.show', $trip->id) }}" class="fw-semibold text-dark text-decoration-none d-block">
                            {{ tdb($trip->title) }}
                        </a>
                        <div class="text-muted extra-small mt-1">
                            <i class="fas fa-route text-primary me-1"></i>{{ tdb($trip->departure_city) }} &rarr; {{ tdb($trip->destination_city) }}
                            @if($trip->hotel_name)
                                &bull; <i class="fas fa-hotel text-warning ms-1 me-1"></i>{{ tdb($trip->hotel_name) }}
                            @endif
                        </div>
                    </td>
                    <td class="text-nowrap">
                        <div class="fw-semibold text-dark small text-truncate" style="max-width: 170px;" title="{{ $trip->bus_name }}">
                            <i class="fas fa-bus text-primary me-1"></i>{{ tdb($trip->bus_name) }}
                        </div>
                        @if($trip->bus_number)
                            <div class="extra-small text-muted font-monospace">{{ $trip->bus_number }}</div>
                        @endif
                    </td>
                    <td class="text-nowrap">
                        <span class="d-none">{{ $trip->departure_date ? $trip->departure_date->format('Y-m-d') : '' }}</span>
                        <div class="fw-bold text-dark fs-6">
                            <i class="far fa-calendar-alt text-primary me-1"></i>{{ $trip->departure_date ? $trip->departure_date->format('d-M-Y') : 'N/A' }}
                        </div>
                        <div class="small text-muted fw-bold mt-1">
                            <i class="far fa-clock me-1 text-secondary"></i>{{ $trip->departure_date ? $trip->departure_date->format('h:i A') : '' }} 
                            &bull; <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold px-2 py-1">{{ $trip->duration_days }} {{ __('Days') }}</span>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex justify-content-between extra-small mb-1">
                            <span class="fw-bold text-dark">{{ $bookedCount }} / {{ $totalSeats }}</span>
                            <span class="fw-bold {{ $availableSeats > 0 ? 'text-success' : 'text-danger' }}">
                                {{ $availableSeats }} {{ __('Free') }}
                            </span>
                        </div>
                        <div class="progress" style="height: 6px; border-radius: 4px; background: #eef2f6;">
                            <div class="progress-bar {{ $progressColor }}" role="progressbar" style="width: {{ $occupancyPercent }}%;"></div>
                        </div>
                    </td>
                    <td class="text-nowrap">
                        <span class="badge bg-light text-success border fw-medium px-2 py-1" title="{{ __('All agency counters and branches can sell tickets for this trip') }}">
                            <i class="fas fa-users me-1 text-success"></i>{{ __('All Counters & Staff') }}
                        </span>
                    </td>
                    <td class="text-nowrap">
                        <div class="small fw-semibold text-dark">{{ format_currency($trip->price_without_hotel) }}</div>
                        @if($trip->price_with_hotel > 0)
                            <div class="extra-small text-success">{{ __('Hotel:') }} {{ format_currency($trip->price_with_hotel) }}</div>
                        @else
                            <div class="extra-small text-muted">{{ __('Seat Only (No Hotel)') }}</div>
                        @endif
                    </td>
                    <td class="text-nowrap">
                        <span class="badge-soft {{ $statusBadge }}">{{ __(ucfirst(str_replace('_', ' ', $trip->status))) }}</span>
                    </td>
                    <td class="text-end pe-3">
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm rounded-circle shadow-none" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v text-muted"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 py-2" style="min-width: 220px;">
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('travel.trips.show', $trip->id) }}">
                                        <i class="fas fa-bus me-2 text-primary"></i> {{ __('Seat Layout & Roster') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('travel.trips.manifest', $trip->id) }}" target="_blank">
                                        <i class="fas fa-clipboard-list me-2 text-info"></i> {{ __('Trip Manifest (بيان الرحلة)') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('travel.trips.manifest_pdf', $trip->id) }}" target="_blank">
                                        <i class="fas fa-file-pdf me-2 text-danger"></i> {{ __('Download Manifest PDF') }}
                                    </a>
                                </li>
                                @if($availableSeats > 0)
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('travel.bookings.create', ['trip_id' => $trip->id]) }}">
                                            <i class="fas fa-ticket-alt me-2 text-success"></i> {{ __('Book Passenger Seat') }}
                                        </a>
                                    </li>
                                @endif
                                @if(!$isEmployee)
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('travel.trips.edit', $trip->id) }}">
                                            <i class="fas fa-edit me-2 text-warning"></i> {{ __('Edit Trip') }}
                                        </a>
                                    </li>
                                    @if($bookedCount == 0)
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            <form action="{{ route('travel.trips.destroy', $trip->id) }}" method="POST" class="delete-form" onsubmit="return confirm('{{ __('Are you sure you want to delete this trip?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item py-2 text-danger">
                                                    <i class="fas fa-trash me-2"></i> {{ __('Delete') }}
                                                </button>
                                            </form>
                                        </li>
                                    @endif
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-table>

        @if($trips->hasPages())
            <div class="p-3 border-top">
                {{ $trips->links() }}
            </div>
        @endif
    @endif
</x-card>
@endsection
