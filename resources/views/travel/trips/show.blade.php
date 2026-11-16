@extends('admin.layouts.app')

@section('title', $trip->title . ' - ' . __('Bus Seat Layout'))

@section('content')
<!-- Header & Navigation -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-light text-primary border fw-bold">{{ $trip->trip_code }}</span>
            <span class="badge {{ $trip->status === 'upcoming' ? 'badge-soft-info' : ($trip->status === 'completed' ? 'badge-soft-success' : 'badge-soft-warning') }}">
                {{ __(ucfirst($trip->status)) }}
            </span>
            <span class="badge bg-white text-dark border">
                <i class="fas fa-calendar-day text-primary me-1"></i> {{ $trip->duration_days }} {{ __('Days Trip') }}
            </span>
        </div>
        <h2 class="fw-bold text-dark mb-1">{{ $trip->title }}</h2>
        <p class="text-muted small mb-0">
            <i class="fas fa-route text-primary me-1"></i> {{ $trip->departure_city }} → {{ $trip->destination_city }}
            &bull; <i class="fas fa-bus text-secondary ms-2 me-1"></i> {{ $trip->bus_name }} ({{ $trip->bus_number ?: __('Reg. Pending') }})
        </p>
    </div>

    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('travel.trips.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Trips') }}
        </a>
        <a href="{{ route('travel.trips.edit', $trip->id) }}" class="btn btn-light border">
            <i class="fas fa-edit me-1 text-warning"></i> {{ __('Edit Trip') }}
        </a>
        @if($trip->availableSeatsCount() > 0)
            <a href="{{ route('travel.bookings.create', ['trip_id' => $trip->id]) }}" class="btn btn-primary-modern">
                <i class="fas fa-plus-circle me-1"></i> {{ __('Book Passenger Seat') }}
            </a>
        @endif
    </div>
</div>

<!-- Trip Summary Bar -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">{{ __('Departure Schedule') }}</span>
                <div class="d-inline-flex p-2 rounded-3 bg-primary-subtle text-primary">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <strong class="text-dark fs-6 d-block">{{ $trip->departure_date ? $trip->departure_date->format('d M, Y - h:i A') : 'N/A' }}</strong>
            <span class="text-muted extra-small mt-auto">{{ __('Return:') }} {{ $trip->return_date ? $trip->return_date->format('d M, Y') : __('Open') }}</span>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">{{ __('Seat Occupancy') }}</span>
                <div class="d-inline-flex p-2 rounded-3 bg-success-subtle text-success">
                    <i class="fas fa-chair"></i>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-baseline">
                <h4 class="fw-bold text-dark mb-0">{{ $trip->bookedSeatsCount() }} / {{ $trip->total_seats }}</h4>
                <span class="badge {{ $trip->availableSeatsCount() > 0 ? 'badge-soft-success' : 'badge-soft-danger' }}">
                    {{ $trip->availableSeatsCount() }} {{ __('Available') }}
                </span>
            </div>
            <div class="progress mt-2" style="height: 6px; border-radius: 4px;">
                <div class="progress-bar bg-success" style="width: {{ round(($trip->bookedSeatsCount() / max(1, $trip->total_seats)) * 100) }}%;"></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">{{ __('Package Fares') }}</span>
                <div class="d-inline-flex p-2 rounded-3 bg-info-subtle text-info">
                    <i class="fas fa-tags"></i>
                </div>
            </div>
            <div class="d-flex justify-content-between small">
                <span class="text-muted">{{ __('Travel Only:') }}</span>
                <strong class="text-dark">{{ format_currency($trip->price_without_hotel) }}</strong>
            </div>
            <div class="d-flex justify-content-between small text-success mt-1">
                <span>{{ __('With Hotel:') }}</span>
                <strong>{{ format_currency($trip->price_with_hotel) }}</strong>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold">{{ __('Revenue Realized') }}</span>
                <div class="d-inline-flex p-2 rounded-3 bg-warning-subtle text-warning">
                    <i class="fas fa-coins"></i>
                </div>
            </div>
            <h4 class="fw-bold text-success mb-0">{{ format_currency($trip->totalRevenue()) }}</h4>
            <span class="text-muted extra-small mt-auto">{{ __('Across') }} {{ $trip->bookedSeatsCount() }} {{ __('confirmed bookings') }}</span>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Visual Bus Layout Card -->
    <div class="col-xl-5 col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="fas fa-bus text-primary me-2"></i>{{ __('Interactive 49-Seat Bus Layout') }}
                    </h5>
                    <span class="text-muted extra-small">{{ __('Click any green seat to book passenger') }}</span>
                </div>
                <!-- Legend -->
                <div class="d-flex gap-2">
                    <span class="badge bg-success-subtle text-success border border-success-subtle extra-small"><i class="fas fa-check-circle me-1"></i> {{ __('Available') }}</span>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle extra-small"><i class="fas fa-user me-1"></i> {{ __('Booked') }}</span>
                </div>
            </div>

            <div class="card-body p-3">
                <!-- Bus Frame Graphic Container -->
                <div class="bus-container mx-auto p-3 rounded-4 shadow-sm" style="max-width: 380px; background: #F8FAFC; border: 2px solid #E2E8F0;">
                    
                    <!-- Front Cabin / Driver Section -->
                    <div class="bus-front p-2 mb-3 rounded-3 d-flex justify-content-between align-items-center text-white" style="background: linear-gradient(135deg, #1E293B, #0F172A);">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-circle-notch fa-spin text-info fs-5"></i>
                            <span class="small fw-bold">{{ __('Driver Cabin') }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-1 extra-small text-warning">
                            <i class="fas fa-door-open"></i>
                            <span>{{ __('Entrance Door') }}</span>
                        </div>
                    </div>

                    <!-- Windshield Line -->
                    <div class="w-100 mb-3" style="height: 4px; background: rgba(59, 130, 246, 0.4); border-radius: 4px;"></div>

                    <!-- Seats Grid -->
                    <div class="bus-seats-body">
                        @php
                            $totalSeats = (int) $trip->total_seats;
                            $standardRows = 11; // 11 rows x 4 = 44 seats
                            $seatCounter = 1;
                        @endphp

                        <!-- Rows 1 to 11 (2x2 with central aisle) -->
                        @for($r = 1; $r <= $standardRows && $seatCounter <= $totalSeats - 5; $r++)
                            <div class="bus-row d-flex align-items-center justify-content-between mb-2">
                                <!-- Left Pair (A & B) -->
                                <div class="d-flex gap-2">
                                    @for($pos = 0; $pos < 2; $pos++)
                                        @php
                                            $sNum = $seatCounter++;
                                            $isBooked = in_array($sNum, $bookedSeats, true);
                                            $b = $bookingsBySeat->get($sNum);
                                        @endphp
                                        <div class="seat-wrapper position-relative">
                                            @if($isBooked)
                                                <a href="{{ route('travel.bookings.show', $b->id) }}" 
                                                   class="btn-seat seat-booked text-decoration-none"
                                                   data-bs-toggle="tooltip" 
                                                   data-bs-html="true"
                                                   title="<strong>{{ __('Seat') }} #{{ $sNum }}</strong><br>{{ $b->passenger_name }}<br>{{ $b->passenger_phone }}<br><span class='badge bg-warning'>{{ $b->package_type === 'with_hotel' ? __('With Hotel') : __('No Hotel') }}</span>">
                                                    <span class="seat-num">{{ $sNum }}</span>
                                                    <i class="fas fa-user seat-icon"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('travel.bookings.create', ['trip_id' => $trip->id, 'seat' => $sNum]) }}" 
                                                   class="btn-seat seat-available text-decoration-none"
                                                   data-bs-toggle="tooltip"
                                                   title="{{ __('Seat #') }}{{ $sNum }} - {{ __('Available (Click to Book)') }}">
                                                    <span class="seat-num">{{ $sNum }}</span>
                                                    <i class="fas fa-chair seat-icon"></i>
                                                </a>
                                            @endif
                                        </div>
                                    @endfor
                                </div>

                                <!-- Central Aisle Indicator -->
                                <div class="bus-aisle text-muted extra-small opacity-50 px-2" style="font-size: 0.65rem;">
                                    {{ $r }}
                                </div>

                                <!-- Right Pair (C & D) -->
                                <div class="d-flex gap-2">
                                    @for($pos = 0; $pos < 2; $pos++)
                                        @php
                                            $sNum = $seatCounter++;
                                            $isBooked = in_array($sNum, $bookedSeats, true);
                                            $b = $bookingsBySeat->get($sNum);
                                        @endphp
                                        <div class="seat-wrapper position-relative">
                                            @if($isBooked)
                                                <a href="{{ route('travel.bookings.show', $b->id) }}" 
                                                   class="btn-seat seat-booked text-decoration-none"
                                                   data-bs-toggle="tooltip" 
                                                   data-bs-html="true"
                                                   title="<strong>{{ __('Seat') }} #{{ $sNum }}</strong><br>{{ $b->passenger_name }}<br>{{ $b->passenger_phone }}<br><span class='badge bg-warning'>{{ $b->package_type === 'with_hotel' ? __('With Hotel') : __('No Hotel') }}</span>">
                                                    <span class="seat-num">{{ $sNum }}</span>
                                                    <i class="fas fa-user seat-icon"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('travel.bookings.create', ['trip_id' => $trip->id, 'seat' => $sNum]) }}" 
                                                   class="btn-seat seat-available text-decoration-none"
                                                   data-bs-toggle="tooltip"
                                                   title="{{ __('Seat #') }}{{ $sNum }} - {{ __('Available (Click to Book)') }}">
                                                    <span class="seat-num">{{ $sNum }}</span>
                                                    <i class="fas fa-chair seat-icon"></i>
                                                </a>
                                            @endif
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        @endfor

                        <!-- Back Row (5 seats continuous across the back bench) -->
                        <div class="bus-back-row mt-3 pt-2 border-top">
                            <div class="text-center extra-small text-muted mb-1" style="font-size: 0.7rem;">{{ __('Back Bench Row') }}</div>
                            <div class="d-flex justify-content-between gap-1">
                                @while($seatCounter <= $totalSeats)
                                    @php
                                        $sNum = $seatCounter++;
                                        $isBooked = in_array($sNum, $bookedSeats, true);
                                        $b = $bookingsBySeat->get($sNum);
                                    @endphp
                                    <div class="seat-wrapper flex-grow-1 text-center">
                                        @if($isBooked)
                                            <a href="{{ route('travel.bookings.show', $b->id) }}" 
                                               class="btn-seat seat-booked text-decoration-none w-100"
                                               data-bs-toggle="tooltip" 
                                               data-bs-html="true"
                                               title="<strong>{{ __('Seat') }} #{{ $sNum }}</strong><br>{{ $b->passenger_name }}<br><span class='badge bg-warning'>{{ $b->package_type === 'with_hotel' ? __('With Hotel') : __('No Hotel') }}</span>">
                                                <span class="seat-num">{{ $sNum }}</span>
                                                <i class="fas fa-user seat-icon"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('travel.bookings.create', ['trip_id' => $trip->id, 'seat' => $sNum]) }}" 
                                               class="btn-seat seat-available text-decoration-none w-100"
                                               data-bs-toggle="tooltip"
                                               title="{{ __('Seat #') }}{{ $sNum }} - {{ __('Available (Click to Book)') }}">
                                                <span class="seat-num">{{ $sNum }}</span>
                                                <i class="fas fa-chair seat-icon"></i>
                                            </a>
                                        @endif
                                    </div>
                                @endwhile
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right: Passenger Manifest Table & Action Roster -->
    <div class="col-xl-7 col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="fas fa-clipboard-list text-success me-2"></i>{{ __('Confirmed Passenger Manifest') }}
                    </h5>
                    <span class="text-muted extra-small">{{ $trip->bookedSeatsCount() }} {{ __('passengers registered on this bus') }}</span>
                </div>
                @if($trip->availableSeatsCount() > 0)
                    <a href="{{ route('travel.bookings.create', ['trip_id' => $trip->id]) }}" class="btn btn-sm btn-primary-modern">
                        <i class="fas fa-plus-circle me-1"></i> {{ __('Book Passenger') }}
                    </a>
                @endif
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="small text-muted text-uppercase">
                                <th style="width: 70px;">{{ __('Seat') }}</th>
                                <th>{{ __('Passenger Info') }}</th>
                                <th>{{ __('Package Type') }}</th>
                                <th>{{ __('Fare & Total') }}</th>
                                <th>{{ __('Payment') }}</th>
                                <th class="text-end" style="width: 100px;">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($trip->confirmedBookings->sortBy('seat_number') as $booking)
                                <tr>
                                    <td>
                                        <div class="p-2 rounded-3 text-center fw-bold bg-primary text-white" style="width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                                            #{{ $booking->seat_number }}
                                        </div>
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
                                        <span class="badge bg-light text-muted border extra-small mt-1">{{ $booking->booking_reference }}</span>
                                    </td>
                                    <td>
                                        @if($booking->package_type === 'with_hotel')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                <i class="fas fa-hotel me-1"></i> {{ __('With Hotel') }}
                                            </span>
                                            @if($booking->hotel_name)
                                                <div class="text-muted extra-small mt-1" style="font-size: 0.72rem;">{{ $booking->hotel_name }} ({{ ucfirst(str_replace('_', ' ', $booking->room_type)) }})</div>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                                <i class="fas fa-bus me-1"></i> {{ __('Seat Only') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong class="text-dark d-block">{{ format_currency($booking->total_amount) }}</strong>
                                        @if($booking->balance_due > 0)
                                            <span class="text-danger extra-small">{{ __('Due:') }} {{ format_currency($booking->balance_due) }}</span>
                                        @else
                                            <span class="text-success extra-small">{{ __('Paid in Full') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $booking->paymentBadgeClass() }}">
                                            {{ __(ucfirst($booking->payment_status)) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="{{ route('travel.bookings.voucher', $booking->id) }}" target="_blank" class="btn btn-sm btn-light border p-1 px-2" title="{{ __('Print Ticket / Voucher') }}">
                                                <i class="fas fa-print text-primary"></i>
                                            </a>
                                            <a href="{{ route('travel.bookings.show', $booking->id) }}" class="btn btn-sm btn-light border p-1 px-2" title="{{ __('View Details') }}">
                                                <i class="fas fa-eye text-info"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fas fa-chair fa-3x opacity-25 mb-2 d-block"></i>
                                        {{ __('No seats booked yet for this trip.') }}
                                        <div class="mt-2">
                                            <a href="{{ route('travel.bookings.create', ['trip_id' => $trip->id]) }}" class="btn btn-sm btn-primary-modern">
                                                <i class="fas fa-plus-circle me-1"></i> {{ __('Book First Seat') }}
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
</div>

<style>
/* Bus Seat Layout Custom Styles */
.btn-seat {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}

.seat-available {
    background-color: #E8FBF4;
    border: 1.5px solid #10B981;
    color: #047857;
}

.seat-available:hover {
    background-color: #10B981;
    color: #FFFFFF;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(16, 185, 129, 0.35);
}

.seat-booked {
    background-color: #FEE2E2;
    border: 1.5px solid #EF4444;
    color: #B91C1C;
    cursor: pointer;
}

.seat-booked:hover {
    background-color: #EF4444;
    color: #FFFFFF;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(239, 68, 68, 0.35);
}

.seat-icon {
    font-size: 0.72rem;
    margin-top: 2px;
}
</style>

@push('scripts')
<script>
    // Initialize Bootstrap tooltips
    document.addEventListener("DOMContentLoaded", function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
@endsection
