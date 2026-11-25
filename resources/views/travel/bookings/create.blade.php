@extends('admin.layouts.app')

@section('title', __('Book Umrah Bus Seat'))

@section('styles')
<style>
    /* Filterable Search Dropdowns (Matching Trips & Quotes) */
    .filter-dropdown { position: relative; }
    .filter-trigger {
        cursor: pointer;
        background-color: #fff !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
        background-repeat: no-repeat !important;
        background-position: right 0.75rem center !important;
        background-size: 16px 12px !important;
        padding-right: 2.25rem !important;
        font-weight: 500;
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
        display: none; position: absolute; top: calc(100% + 4px); left: 0; right: 0;
        z-index: 1060; background: #fff; border: 1px solid #dee2e6;
        border-radius: 0.5rem; box-shadow: 0 8px 24px rgba(0,0,0,.14); overflow: hidden;
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
    .filter-optgroup-label {
        padding: 6px 14px 4px;
        font-size: .75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #6c757d;
        background: #f8f9fa;
        border-bottom: 1px solid #f1f3f5;
        border-top: 1px solid #f1f3f5;
    }
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
@endsection

@section('content')
@php
    $isAdmin = $isAdmin ?? (auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->isAdmin()));
@endphp
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        <a href="{{ route('travel.bookings.index') }}" class="btn btn-light btn-sm rounded-circle me-3 shadow-sm">
            <i class="fas fa-arrow-left text-muted"></i>
        </a>
        <div>
            <h2 class="fw-bold text-dark mb-0">{{ __('Book Umrah Bus Seat') }}</h2>
            <p class="text-muted small mb-0">{{ __('Select trip, pick an available seat on the interactive bus layout, and register passenger details.') }}</p>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('travel.bookings.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-list me-1"></i> {{ __('All Bookings') }}
        </a>
    </div>
</div>

<form action="{{ route('travel.bookings.store') }}" method="POST" id="seatBookingForm">
    @csrf

    <div class="row g-4">
        <!-- Left: Trip Selection & Interactive Visual Bus Seat Map -->
        <div class="col-xl-5 col-lg-6">
            <x-card :title="__('1. Select Trip & Choose Seat Number')">
                <!-- Trip Departure Date Filter & Calendar Control -->
                <div class="p-2 mb-2 bg-white rounded-3 border">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="tripDepartureDateFilter" class="form-label fw-bold text-dark small mb-0">
                            <i class="fas fa-calendar-alt text-primary me-1"></i> {{ __('Filter / Select Departure Date (تاريخ المغادرة)') }}
                        </label>
                        <span class="badge bg-light text-muted border extra-small" id="tripsCountBadge">
                            {{ count($trips) }} {{ __('Available Trips') }}
                        </span>
                    </div>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-primary border-end-0"><i class="fas fa-calendar-day"></i></span>
                        <input type="date" id="tripDepartureDateFilter" class="form-control" 
                               value="{{ $selectedTrip && $selectedTrip->departure_date ? $selectedTrip->departure_date->format('Y-m-d') : '' }}"
                               placeholder="YYYY-MM-DD" title="{{ __('Pick a departure date to filter trips') }}">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="clearTripDateFilterBtn" title="{{ __('Show all scheduled trips') }}">
                            <i class="fas fa-calendar-check me-1"></i> {{ __('All Dates') }}
                        </button>
                    </div>
                    <div id="noTripsForDateAlert" class="extra-small text-danger mt-1" style="display: none;">
                        <i class="fas fa-info-circle me-1"></i> {{ __('No trips scheduled on this date. Showing all available trips.') }}
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark small">{{ __('Scheduled Trip / Route *') }}</label>
                    <select name="travel_trip_id" id="tripSelect" class="form-select @error('travel_trip_id') is-invalid @enderror" required onchange="onTripSelectChanged(this.value)">
                        @foreach($trips as $t)
                            @php
                                $depDateFormatted = $t->departure_date ? $t->departure_date->format('d M Y (h:i A)') : __('No Date');
                                $depDateYmd = $t->departure_date ? $t->departure_date->format('Y-m-d') : '';
                                $retDateFormatted = $t->return_date ? $t->return_date->format('d M Y (h:i A)') : __('No Date');
                                $freeSeats = $t->availableSeatsCount();
                            @endphp
                            <option value="{{ $t->id }}" 
                                    data-seats="{{ $t->total_seats }}" 
                                    data-available-seats="{{ $freeSeats }}"
                                    data-fare-no-hotel="{{ $t->price_without_hotel }}"
                                    data-fare-with-hotel="{{ $t->price_with_hotel }}"
                                    data-hotel="{{ $t->hotel_name }}"
                                    data-hotel-address="{{ $t->hotel_address }}"
                                    data-duration="{{ $t->duration_days }}"
                                    data-departure-date="{{ $depDateYmd }}"
                                    data-departure-time="{{ $t->departure_date ? $t->departure_date->format('h:i A') : '' }}"
                                    data-departure-datetime="{{ $depDateFormatted }}"
                                    data-return-datetime="{{ $retDateFormatted }}"
                                    data-trip-code="{{ $t->trip_code }}"
                                    data-trip-title="{{ tdb($t->title) }}"
                                    data-departure-city="{{ tdb($t->departure_city) }}"
                                    data-destination-city="{{ tdb($t->destination_city) }}"
                                    {{ $selectedTrip && $selectedTrip->id == $t->id ? 'selected' : '' }}>
                                [{{ $depDateFormatted }}] &bull; {{ $t->trip_code }} — {{ tdb($t->title) }} ({{ $freeSeats }} {{ __('Seats Free') }})
                            </option>
                        @endforeach
                    </select>
                    @error('travel_trip_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Selected Trip Live Schedule Details Card -->
                <div id="tripScheduleCard" class="card border-0 shadow-xs rounded-3 mb-3" style="background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%); border: 1.5px solid #cbd5e1 !important; {{ $selectedTrip ? '' : 'display:none;' }}">
                    <div class="card-body p-2 p-md-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary text-white font-monospace" id="tripScheduleCode">
                                    {{ $selectedTrip ? $selectedTrip->trip_code : '' }}
                                </span>
                                <span class="fw-bold text-dark small" id="tripScheduleTitle">
                                    {{ $selectedTrip ? tdb($selectedTrip->title) : '' }}
                                </span>
                            </div>
                            <span class="badge bg-success text-white extra-small px-2 py-1" id="tripScheduleFreeSeats">
                                <i class="fas fa-chair me-1"></i> {{ $selectedTrip ? $selectedTrip->availableSeatsCount() : 0 }} {{ __('Seats Free') }}
                            </span>
                        </div>

                        <div class="row g-2 pt-2 border-top border-secondary-subtle">
                            <div class="col-6">
                                <div class="p-2 bg-white rounded-2 border h-100">
                                    <span class="text-muted extra-small d-block text-uppercase fw-bold">
                                        <i class="fas fa-plane-departure text-success me-1"></i> {{ __('Departure Date & Time') }}
                                    </span>
                                    <span class="fw-bold text-dark small d-block mt-1" id="tripScheduleDeparture">
                                        {{ $selectedTrip && $selectedTrip->departure_date ? $selectedTrip->departure_date->format('d M Y — h:i A') : __('Not Set') }}
                                    </span>
                                    <span class="text-muted extra-small d-block mt-1" id="tripScheduleFromCity">
                                        <i class="fas fa-map-marker-alt text-danger me-1"></i> {{ $selectedTrip ? tdb($selectedTrip->departure_city) : '' }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 bg-white rounded-2 border h-100">
                                    <span class="text-muted extra-small d-block text-uppercase fw-bold">
                                        <i class="fas fa-plane-arrival text-danger me-1"></i> {{ __('Return Date & Time') }}
                                    </span>
                                    <span class="fw-bold text-dark small d-block mt-1" id="tripScheduleReturn">
                                        {{ $selectedTrip && $selectedTrip->return_date ? $selectedTrip->return_date->format('d M Y — h:i A') : __('Not Set') }}
                                    </span>
                                    <span class="text-muted extra-small d-block mt-1" id="tripScheduleToCity">
                                        <i class="fas fa-map-marker-alt text-primary me-1"></i> {{ $selectedTrip ? tdb($selectedTrip->destination_city) : '' }}
                                        @if($selectedTrip && $selectedTrip->duration_days)
                                            &bull; <span class="badge bg-light text-dark border extra-small">{{ $selectedTrip->duration_days }} {{ __('Days') }}</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Selected Seats Banner -->
                <div class="p-3 rounded-3 mb-3 border shadow-xs" style="background: #F0FDF4; border-color: #86EFAC !important;">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <span class="text-muted extra-small d-block uppercase fw-bold mb-1">{{ __('Allocated Seat Numbers') }}</span>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <div id="selectedSeatDisplay" class="d-flex flex-wrap gap-1 align-items-center">
                                    @if($selectedSeat)
                                        <span class="badge bg-primary fs-6">#{{ $selectedSeat }}</span>
                                    @else
                                        <span class="text-muted fs-6">{{ __('Click one or more seats below') }}</span>
                                    @endif
                                </div>
                                <span class="badge bg-success text-white extra-small" id="seatConfirmedBadge" style="{{ $selectedSeat ? '' : 'display:none;' }}">
                                    <i class="fas fa-check-circle me-1"></i> <span id="seatsCountBadgeText">{{ $selectedSeat ? '1 ' . __('Seat Selected') : '' }}</span>
                                </span>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2 extra-small mb-1" id="clearSeatsBtn" style="{{ $selectedSeat ? '' : 'display:none;' }}" onclick="clearAllSelectedSeats()">
                                <i class="fas fa-undo me-1"></i> {{ __('Clear Selected Seats') }}
                            </button>
                            <span class="badge bg-danger-subtle text-danger border extra-small d-block">
                                <i class="fas fa-lock me-1"></i> {{ __('Anti-Collision Protected') }}
                            </span>
                        </div>
                    </div>
                    <input type="hidden" name="seat_numbers" id="seatNumbersInput" value="{{ old('seat_numbers', $selectedSeat ? (string)$selectedSeat : '') }}">
                    <input type="hidden" name="seat_number" id="seatNumberInput" value="{{ old('seat_number', $selectedSeat) }}">
                </div>
                @error('seat_number') <div class="alert alert-danger py-2 small mb-3">{{ $message }}</div> @enderror
                @error('seat_numbers') <div class="alert alert-danger py-2 small mb-3">{{ $message }}</div> @enderror

                <!-- Visual 49-Seat Bus Grid Picker -->
                <div class="border rounded-4 p-3 bg-light" style="border: 2px solid #E2E8F0 !important;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold text-dark small"><i class="fas fa-bus text-primary me-1"></i> {{ __('Bus Seat Layout') }}</span>
                        <div class="d-flex gap-1 flex-wrap">
                            <span class="badge bg-warning text-dark border border-warning-subtle extra-small"><i class="fas fa-crown me-1 text-dark"></i> {{ __('VIP') }}</span>
                            <span class="badge bg-primary text-white extra-small"><i class="fas fa-briefcase me-1"></i> {{ __('Business') }}</span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle extra-small"><i class="fas fa-chair me-1"></i> {{ __('Economy') }}</span>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle extra-small"><i class="fas fa-lock me-1"></i> {{ __('Booked') }}</span>
                        </div>
                    </div>

                    <!-- Live Real-Time Collision Notification Alert -->
                    <div id="liveSeatAlertContainer" class="mb-2" style="display: none;"></div>

                    <!-- Bus Cabin Header with Live Sync Status -->
                    <div class="p-2 mb-3 rounded-3 d-flex justify-content-between align-items-center text-white" style="background: linear-gradient(135deg, #1E293B, #0F172A);">
                        <div class="d-flex align-items-center gap-2 small fw-bold">
                            <i class="fas fa-circle-notch fa-spin text-info"></i>
                            <span>{{ __('Driver Cabin') }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success bg-opacity-25 text-success border border-success-subtle extra-small d-flex align-items-center gap-1" title="{{ __('Real-time seat auto sync across all counters & offices without page refresh') }}">
                                <span class="live-dot-pulse"></span>
                                <span id="liveSyncStatusText">{{ __('Live Sync Active') }}</span>
                            </span>
                            <span class="extra-small text-warning"><i class="fas fa-door-open me-1"></i>{{ __('Door') }}</span>
                        </div>
                    </div>

                    <!-- Interactive Seat Buttons Container (populated dynamically) -->
                    <div id="interactiveSeatsContainer" class="d-flex flex-column gap-2">
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-spinner fa-spin fa-2x mb-2 text-primary"></i>
                            <div class="small fw-semibold">{{ __('Loading bus seat layout...') }}</div>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Right: Passenger Info, Package Options & Financial Calculation -->
        <div class="col-xl-7 col-lg-6">
            <!-- 2. Passenger Identification Card -->
            <x-card :title="__('2. Passenger Identification & Contact')">
                <div class="row g-3">
                    <div class="col-md-7">
                        <label class="form-label fw-bold text-dark">{{ __('Passenger Full Name *') }}</label>
                        <input type="text" name="passenger_name" class="form-control @error('passenger_name') is-invalid @enderror" placeholder="{{ __('Muhammad Abdullah') }}" value="{{ old('passenger_name') }}" required>
                        @error('passenger_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-5">
                        <label class="form-label fw-bold text-dark">{{ __('Gender *') }}</label>
                        <select name="passenger_gender" class="form-select" required>
                            <option value="male" {{ old('passenger_gender') === 'male' ? 'selected' : '' }}>{{ __('Male (ذكر)') }}</option>
                            <option value="female" {{ old('passenger_gender') === 'female' ? 'selected' : '' }}>{{ __('Female (أنثى)') }}</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">{{ __('Mobile / WhatsApp Phone *') }}</label>
                        <x-country-phone-picker id="passenger_phone" name="passenger_phone" codeName="country_code" :required="true" placeholder="50 123 4567" :error="$errors->first('passenger_phone')" />
                        @error('passenger_phone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">{{ __('National ID / Iqama / Passport #') }}</label>
                        <input type="text" name="passenger_id_number" id="passengerIdInput" class="form-control @error('passenger_id_number') is-invalid @enderror" placeholder="{{ __('2489012345 or Passport #') }}" value="{{ old('passenger_id_number') }}">
                        @error('passenger_id_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">{{ __('Nationality (الجنسية)') }}</label>
                        <input type="text" name="passenger_nationality" id="passengerNatInput" class="form-control @error('passenger_nationality') is-invalid @enderror" placeholder="{{ __('PAK, IND, BAN, SAU, EGY, SRI') }}" value="{{ old('passenger_nationality', 'PAK') }}">
                        @error('passenger_nationality') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        @php
                            $isAdmin = auth()->user()->isSuperAdmin() || auth()->user()->isAdmin();
                            $defaultPickupLocation = $currentEmployee?->name 
                                ?: (auth()->user()?->employee?->name 
                                ?: (auth()->user()?->name 
                                ?: (current_shop()?->name ?? 'Office')));
                        @endphp
                        <label class="form-label fw-bold text-dark d-flex justify-content-between align-items-center">
                            <span>{{ __('Pickup / Boarding Office Location (موقع الركوب)') }}</span>
                            @if(!$isAdmin)
                                <span class="badge bg-light text-muted border extra-small" title="{{ __('Locked: Only Admin can change boarding location') }}">
                                    <i class="fas fa-lock text-warning me-1"></i>{{ __('Locked') }}
                                </span>
                            @endif
                        </label>
                        <input type="text" 
                               name="pickup_location" 
                               id="pickupLocationInput" 
                               class="form-control @error('pickup_location') is-invalid @enderror" 
                               placeholder="{{ __('Main Office, Batha, Villa 12, Nesto') }}" 
                               value="{{ old('pickup_location', $defaultPickupLocation) }}"
                               @if(!$isAdmin) readonly style="background-color: #f8fafc; cursor: not-allowed;" title="{{ __('Only Admin can change boarding location') }}" @endif>
                        @error('pickup_location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        @if(!$isAdmin)
                            <div class="form-text extra-small text-muted">
                                <i class="fas fa-lock me-1 text-secondary"></i>{{ __('Locked: Boarding office is assigned automatically. Only Admin can edit.') }}
                            </div>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">{{ __('Outbound Destination Drop-off (موقع النزول في الذهاب)') }}</label>
                        <input type="text" name="dropoff_location" id="dropoffLocationInput" class="form-control @error('dropoff_location') is-invalid @enderror" placeholder="{{ __('Makkah Haram, Kudai, Taif, Hotel, Highway Stop') }}" value="{{ old('dropoff_location') }}">
                        @error('dropoff_location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="d-flex gap-1 flex-wrap mt-1">
                            <span class="badge bg-light text-secondary border extra-small" style="cursor: pointer;" onclick="setDropoffVal('Makkah Haram')">Makkah Haram</span>
                            <span class="badge bg-light text-secondary border extra-small" style="cursor: pointer;" onclick="setDropoffVal('Kudai Parking')">Kudai Parking</span>
                            <span class="badge bg-light text-secondary border extra-small" style="cursor: pointer;" onclick="setDropoffVal('Madinah Station')">Madinah Station</span>
                            <span class="badge bg-light text-secondary border extra-small" style="cursor: pointer;" onclick="setDropoffVal('Hotel Drop')">Hotel Drop</span>
                            <span class="badge bg-light text-secondary border extra-small" style="cursor: pointer;" onclick="setDropoffVal('Taif / Highway')">Taif / Highway</span>
                        </div>
                    </div>

                    <!-- Return Journey Drop-off / Midway Stop (for Round Trip) -->
                    <div class="col-12" id="returnDropoffGroup">
                        <div class="p-3 rounded-3 bg-light border border-success-subtle">
                            <label class="form-label fw-bold text-dark small mb-1 d-flex align-items-center justify-content-between">
                                <span><i class="fas fa-undo-alt text-success me-1"></i> {{ __('Return Journey Drop-off / Midway Stop (موقع النزول في طريق العودة)') }}</span>
                                <span class="badge bg-success text-white extra-small">{{ __('Round-Trip Return Stop') }}</span>
                            </label>
                            <input type="text" name="return_dropoff_location" id="returnDropoffLocationInput" class="form-control form-control-sm @error('return_dropoff_location') is-invalid @enderror" placeholder="{{ __('Select employee point below or type custom return stop') }}" value="{{ old('return_dropoff_location') }}">
                            @error('return_dropoff_location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="d-flex gap-1 flex-wrap mt-2 align-items-center">
                                <span class="extra-small text-muted me-1"><i class="fas fa-users text-primary me-1"></i>{{ __('Quick Presets (Employees):') }}</span>
                                <span class="badge bg-white text-secondary border extra-small" style="cursor: pointer;" onclick="setReturnDropoffVal('Same as Boarding Office')">{{ __('Same as Boarding Office') }}</span>
                                @if(isset($employees) && $employees->isNotEmpty())
                                    @php
                                        $uniqueEmpNames = $employees->pluck('name')->unique()->filter();
                                    @endphp
                                    @foreach($uniqueEmpNames as $empName)
                                        <span class="badge bg-white text-dark border border-primary-subtle extra-small shadow-sm" style="cursor: pointer;" onclick="setReturnDropoffVal(@js($empName))">
                                            <i class="fas fa-user-tie text-primary me-1"></i>{{ $empName }}
                                        </span>
                                    @endforeach
                                @endif
                                <span class="badge bg-white text-danger border border-danger-subtle extra-small" style="cursor: pointer;" onclick="clearReturnDropoffVal()" title="{{ __('Clear to type custom manually') }}"><i class="fas fa-times me-1"></i>{{ __('Clear') }}</span>
                            </div>
                            <div class="form-text extra-small text-muted mt-1">
                                <i class="fas fa-info-circle text-primary me-1"></i>{{ __('Specify if the passenger requires a midway drop-off along the highway or a specific return stop (printed on the driver manifest).') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Multiple Passengers per Seat Details Container -->
                <div id="multiplePassengersSection" class="mt-4 pt-3 border-top" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-bold text-primary mb-0">
                            <i class="fas fa-users me-1"></i> {{ __('Passenger Names per Allocated Seat') }}
                        </label>
                        <span class="badge bg-primary text-white" id="multiPassCountBadge"></span>
                    </div>
                    <p class="text-muted extra-small mb-3">
                        {{ __('Enter individual passenger name, gender, and ID/Iqama for each allocated seat.') }}
                    </p>
                    <div id="passengerSeatsList" class="d-flex flex-column gap-2"></div>
                </div>
            </x-card>

            <!-- 3. Journey Type & Package Selection -->
            <x-card :title="__('3. Journey Type & Accommodation Package')" class="mt-4">
                <!-- Journey Type: Round Trip vs One Way -->
                <div class="mb-4 pb-3 border-bottom">
                    <label class="form-label fw-bold text-dark small mb-2 d-flex align-items-center gap-1">
                        <i class="fas fa-route text-primary"></i> {{ __('Journey Direction / Type (نوع الرحلة) *') }}
                    </label>
                    @php
                        $jType = old('journey_type', 'round_trip');
                    @endphp
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="border rounded-3 p-3 d-flex align-items-start gap-3 h-100 cursor-pointer shadow-xs bg-white {{ $jType === 'round_trip' ? 'border-primary bg-primary-subtle' : '' }}" 
                                   id="labelRoundTrip" for="journey_round_trip" style="cursor: pointer;">
                                <input type="radio" name="journey_type" id="journey_round_trip" value="round_trip" class="form-check-input mt-1" 
                                       {{ $jType === 'round_trip' ? 'checked' : '' }} onchange="onJourneyTypeChanged('round_trip')">
                                <div>
                                    <strong class="text-dark d-block mb-1">
                                        <i class="fas fa-exchange-alt text-primary me-1"></i> {{ __('Two-Way / Round Trip (ذهاب وعودة)') }}
                                    </strong>
                                    <p class="text-muted extra-small mb-0">{{ __('Passenger travels with the group to destination and returns with the bus.') }}</p>
                                </div>
                            </label>
                        </div>

                        <div class="col-md-6">
                            <label class="border rounded-3 p-3 d-flex align-items-start gap-3 h-100 cursor-pointer shadow-xs bg-white {{ $jType === 'one_way' ? 'border-info bg-info-subtle' : '' }}" 
                                   id="labelOneWay" for="journey_one_way" style="cursor: pointer;">
                                <input type="radio" name="journey_type" id="journey_one_way" value="one_way" class="form-check-input mt-1" 
                                       {{ $jType === 'one_way' ? 'checked' : '' }} onchange="onJourneyTypeChanged('one_way')">
                                <div>
                                    <strong class="text-dark d-block mb-1">
                                        <i class="fas fa-arrow-right text-info me-1"></i> {{ __('One-Way Only (ذهاب فقط / اتجاه واحد)') }}
                                    </strong>
                                    <p class="text-muted extra-small mb-0">{{ __('Passenger only travels one way or drops off midway without returning.') }}</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Direction Selector for One-Way -->
                    <div id="oneWayDirectionSection" class="mt-3 p-3 rounded-3 bg-light border border-info-subtle" style="{{ $jType === 'one_way' ? '' : 'display: none;' }}">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small mb-1">{{ __('Travel Direction (اتجاه السفر)') }}</label>
                                <select name="one_way_direction" id="oneWayDirectionSelect" class="form-select form-select-sm" onchange="onOneWayDirectionChanged(this.value)">
                                    <option value="outbound" {{ old('one_way_direction', 'outbound') === 'outbound' ? 'selected' : '' }} id="dirOutboundOption">
                                        {{ __('Outbound (ذهاب): Departure → Destination') }}
                                    </option>
                                    <option value="return" {{ old('one_way_direction') === 'return' ? 'selected' : '' }} id="dirReturnOption">
                                        {{ __('Inbound (عودة): Destination → Departure') }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <span class="badge bg-info text-white extra-small mb-1 d-block w-auto"><i class="fas fa-tag me-1"></i>{{ __('One-Way Ticket Rate') }}</span>
                                <span class="extra-small text-muted" id="oneWayFareNotice">
                                    {{ __('One-way ticket rate is applied automatically.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    @php
                        $pkgType = old('package_type', 'with_hotel');
                    @endphp
                    <div class="col-md-6">
                        <label class="border rounded-3 p-3 d-flex align-items-start gap-3 h-100 cursor-pointer shadow-xs bg-white {{ $pkgType === 'with_hotel' ? 'border-success bg-success-subtle' : '' }}" 
                               id="labelWithHotel" for="pkg_with_hotel" style="cursor: pointer;">
                            <input type="radio" name="package_type" id="pkg_with_hotel" value="with_hotel" class="form-check-input mt-1" 
                                   {{ $pkgType === 'with_hotel' ? 'checked' : '' }} onchange="onPackageTypeChanged('with_hotel')">
                            <div>
                                <strong class="text-dark d-block mb-1"><i class="fas fa-hotel text-success me-1"></i> {{ __('Complete Package (With Hotel)') }}</strong>
                                <p class="text-muted extra-small mb-0">{{ __('Includes bus seat ticket plus hotel stay in Makkah/Madinah.') }}</p>
                            </div>
                        </label>
                    </div>

                    <div class="col-md-6">
                        <label class="border rounded-3 p-3 d-flex align-items-start gap-3 h-100 cursor-pointer shadow-xs bg-white {{ $pkgType === 'without_hotel' ? 'border-primary bg-primary-subtle' : '' }}" 
                               id="labelWithoutHotel" for="pkg_without_hotel" style="cursor: pointer;">
                            <input type="radio" name="package_type" id="pkg_without_hotel" value="without_hotel" class="form-check-input mt-1" 
                                   {{ $pkgType === 'without_hotel' ? 'checked' : '' }} onchange="onPackageTypeChanged('without_hotel')">
                            <div>
                                <strong class="text-dark d-block mb-1"><i class="fas fa-bus text-primary me-1"></i> {{ __('Seat Fare Only (Without Hotel)') }}</strong>
                                <p class="text-muted extra-small mb-0">{{ __('Passenger handles their own accommodation; bus travel only.') }}</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Hotel Configuration Fields (toggled by package type) -->
                <div id="hotelConfigSection" style="{{ $pkgType === 'without_hotel' ? 'display:none;' : '' }}">
                    <div class="row g-3 p-3 rounded-3 bg-light border border-success-subtle">
                        {{-- Row 1: Hotel Name & Address (50% / 50% balanced) --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small mb-1">
                                <i class="fas fa-hotel text-primary me-1"></i>{{ __('Hotel Accommodation Partner') }}
                            </label>
                            <input type="text" name="hotel_name" id="hotelNameInput" class="form-control" placeholder="{{ __('Swissôtel Makkah') }}" value="{{ old('hotel_name', $selectedTrip?->hotel_name) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small mb-1">
                                <i class="fas fa-map-marker-alt text-danger me-1"></i>{{ __('Hotel Address / Location in Makkah / Madinah') }}
                            </label>
                            <input type="text" name="hotel_address" id="hotelAddressInput" class="form-control" placeholder="{{ __('Ibrahim Al Khalil Road, Makkah / Near Clock Tower') }}" value="{{ old('hotel_address', $selectedTrip?->hotel_address) }}">
                        </div>

                        {{-- Row 2: Room Category & Room Price (50% / 50%) --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small mb-1">
                                <i class="fas fa-bed text-primary me-1"></i>{{ __('Room Category / Type') }}
                            </label>
                            <select name="room_type" id="roomTypeSelect" class="form-select" onchange="onRoomTypeChanged()">
                                <option value="share_room" {{ old('room_type', 'share_room') === 'share_room' ? 'selected' : '' }}>{{ __('1- Share room (غرفة مشتركة)') }}</option>
                                <option value="private_room" {{ old('room_type') === 'private_room' ? 'selected' : '' }}>{{ __('2- Private room (غرفة خاصة)') }}</option>
                                <option value="bridal_room" {{ old('room_type') === 'bridal_room' ? 'selected' : '' }}>{{ __('3- Bridle room (غرفة عروسين)') }}</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-bold text-dark small mb-0 text-success">
                                    <i class="fas fa-tag me-1"></i>{{ __('Room / Hotel Price') }}
                                </label>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-success-subtle text-success fw-bold">{{ currency_symbol() }}</span>
                                <input type="number" step="0.01" min="0" id="roomCategoryPriceInput" class="form-control font-monospace fw-bold text-success" placeholder="0.00" value="{{ old('hotel_expense', '0.00') }}" oninput="onRoomCategoryPriceInput(this.value)">
                            </div>
                        </div>

                        {{-- Row 3: Hotel Nights Breakdown (33.3% / 33.3% / 33.3%) --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark small mb-1">
                                <i class="fas fa-moon text-warning me-1"></i>{{ __('Extra Hotel Nights') }}
                            </label>
                            <input type="number" name="nights_count" id="nightsCountInput" class="form-control font-monospace" min="0" max="90" placeholder="0" value="{{ old('nights_count', 0) }}" oninput="syncCreateHotelNights('total')">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark small mb-1">
                                <i class="fas fa-kaaba text-success me-1"></i>{{ __('Makkah Nights') }}
                            </label>
                            <input type="number" name="makkah_nights" id="makkahNightsInput" class="form-control font-monospace" min="0" max="90" placeholder="0" value="{{ old('makkah_nights', 0) }}" oninput="syncCreateHotelNights('makkah')">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark small mb-1">
                                <i class="fas fa-mosque text-success me-1"></i>{{ __('Madinah Nights') }}
                            </label>
                            <input type="number" name="madinah_nights" id="madinahNightsInput" class="form-control font-monospace" min="0" max="90" placeholder="0" value="{{ old('madinah_nights', 0) }}" oninput="syncCreateHotelNights('madinah')">
                            <input type="hidden" name="hotel_city" id="hotelCityInput" value="{{ old('hotel_city', 'Makkah') }}">
                        </div>
                    </div>
                </div>
            </x-card>

            <!-- 4. Expense Breakdown & Payment -->
            <x-card :title="__('4. Expense Breakdown & Cash Realization')" class="mt-4">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark small">{{ __('Travel Bus Fare') }} *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold">{{ currency_symbol() }}</span>
                            @php
                                $initialFare = $selectedSeat ? ($selectedTrip?->getSeatFare($selectedSeat) ?? 100) : 0;
                            @endphp
                            <input type="number" step="0.01" name="travel_fare" id="travelFareInput" class="form-control" value="{{ old('travel_fare', number_format($initialFare, 2, '.', '')) }}" required oninput="recalcBooking()">
                        </div>
                        <small class="text-primary extra-small fw-semibold d-block mt-1" id="fareMultiplierNote"></small>
                    </div>

                    <div class="col-md-6" id="hotelExpenseCol">
                        <label class="form-label fw-bold text-dark small">{{ __('Hotel Accommodation Expense') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold">{{ currency_symbol() }}</span>
                            @php
                                $defHotelExp = ($pkgType === 'with_hotel' && $selectedSeat && $selectedTrip) 
                                    ? max(0, ($selectedTrip->price_with_hotel ?? 0) - ($selectedTrip->price_without_hotel ?? 0))
                                    : 0;
                            @endphp
                            <input type="number" step="0.01" name="hotel_expense" id="hotelExpenseInput" class="form-control" value="{{ old('hotel_expense', number_format($defHotelExp, 2, '.', '')) }}" oninput="recalcBooking()">
                        </div>
                        <small class="text-success extra-small fw-semibold d-block mt-1" id="hotelMultiplierNote"></small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small">{{ __('Extra / Pickup Charges') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold">{{ currency_symbol() }}</span>
                            <input type="number" step="0.01" name="extra_charges" id="extraChargesInput" class="form-control" value="{{ old('extra_charges', '0.00') }}" oninput="recalcBooking()">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small">{{ __('Discount') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold">{{ currency_symbol() }}</span>
                            <input type="number" step="0.01" name="discount" id="discountInput" class="form-control" value="{{ old('discount', '0.00') }}" oninput="recalcBooking()">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small">{{ __('VAT / Tax') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold">{{ currency_symbol() }}</span>
                            <input type="number" step="0.01" name="tax_amount" id="taxInput" class="form-control" value="{{ old('tax_amount', '0.00') }}" oninput="recalcBooking()">
                        </div>
                    </div>
                </div>

                <!-- Financial Summary Box -->
                <div class="p-3 rounded-3 mb-3" style="background: #F8FAFC; border: 1.5px solid #E2E8F0;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold text-dark fs-5">{{ __('Total Package Amount:') }}</span>
                        <span class="fw-bold text-primary fs-4" id="totalAmountDisplay">
                            {{ currency_symbol() }}0.00
                        </span>
                    </div>

                    <div class="row g-2 pt-2 border-top">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-bold text-dark small mb-0">{{ __('Paid Amount (Cash In Hand / Card) *') }}</label>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2" style="font-size: 0.70rem;" onclick="setPaidZero()">{{ __('0 / Unpaid') }}</button>
                                    <button type="button" class="btn btn-xs btn-outline-success py-0 px-2" style="font-size: 0.70rem;" onclick="setPaidFull()">{{ __('Full Paid') }}</button>
                                </div>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-success-subtle text-success fw-bold">{{ currency_symbol() }}</span>
                                <input type="number" step="0.01" min="0" name="paid_amount" id="paidAmountInput" class="form-control fw-bold text-success" value="{{ old('paid_amount', '0.00') }}" required oninput="recalcBooking()">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">{{ __('Payment Method') }} *</label>
                            @php
                                $curPaymentMethod = old('payment_method', 'cash');
                            @endphp
                            <select name="payment_method" class="form-select" required>
                                <option value="cash" {{ in_array($curPaymentMethod, ['cash', 'Cash', 'نقدي']) ? 'selected' : '' }}>{{ __('Cash at Counter (نقدي)') }}</option>
                                <option value="bank_transfer" {{ in_array($curPaymentMethod, ['bank_transfer', 'Bank Transfer', 'تحويل بنكي']) ? 'selected' : '' }}>{{ __('Bank Transfer (تحويل بنكي)') }}</option>
                                <option value="card" {{ in_array($curPaymentMethod, ['card', 'credit_card', 'Credit Card', 'Credit / Debit Card', 'شبكة']) ? 'selected' : '' }}>{{ __('Credit / Debit Card (شبكة)') }}</option>
                                <option value="online" {{ in_array($curPaymentMethod, ['online', 'online_pos', 'mada', 'Online Payment', 'إلكتروني']) ? 'selected' : '' }}>{{ __('Online Payment (إلكتروني)') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top small">
                        <span class="text-muted fw-bold">{{ __('Remaining Balance Due:') }}</span>
                        <strong class="text-danger fs-6" id="balanceDueDisplay">{{ currency_symbol() }}0.00</strong>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top small">
                        <span class="text-muted fw-bold">{{ __('Payment Status (الحالة):') }}</span>
                        <span id="paymentStatusBadge" class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 fw-bold fs-7">
                            {{ __('UNPAID / غير مدفوع') }}
                        </span>
                        <input type="hidden" name="payment_status" id="paymentStatusInput" value="{{ old('payment_status', 'unpaid') }}">
                    </div>
                </div>

                <!-- Checkbox for Invoice Generation -->
                @if(shop_has_module('invoices'))
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="generate_invoice" id="generateInvoiceCheck" value="1" checked>
                        <label class="form-check-label fw-bold text-dark small" for="generateInvoiceCheck">
                            <i class="fas fa-file-invoice-dollar text-primary me-1"></i> {{ __('Automatically generate official sales tax invoice & post to ledger') }}
                        </label>
                    </div>
                @endif

                @if(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin())
                <div class="row g-2 mb-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold text-dark small">{{ __('Assign Employee / Agent (مسؤول الحجز)') }}</label>
                        <select name="employee_id" id="bookingEmployeeSelect" class="form-select @error('employee_id') is-invalid @enderror" onchange="onBookingEmployeeChanged(this)">
                            <option value="" data-name="{{ auth()->user()?->name }}" data-phone="{{ auth()->user()?->phone }}">-- {{ __('Admin / Central Office') }} --</option>
                            @if(isset($employees))
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" 
                                            data-name="{{ $emp->name }}" 
                                            data-phone="{{ $emp->phone }}"
                                            {{ (string)old('employee_id', $currentEmployee->id ?? '') === (string)$emp->id ? 'selected' : '' }}>
                                        {{ $emp->name }} ({{ $emp->designation ?? 'Staff' }})
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('employee_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text extra-small">{{ __('Assign the booking to a sales agent or keep under Admin.') }}</div>
                    </div>
                    <div class="col-md-6 mt-2">
                        <label class="form-label fw-bold text-dark small">{{ __('Salesman / Agent Name') }}</label>
                        <input type="text" name="salesman_name" id="salesmanNameInput" class="form-control" placeholder="{{ __('Salesman Name') }}" value="{{ old('salesman_name', $currentEmployee?->name ?? auth()->user()?->name) }}">
                    </div>
                    <div class="col-md-6 mt-2">
                        <label class="form-label fw-bold text-dark small">{{ __('Salesman Phone') }}</label>
                        <input type="text" name="salesman_phone" id="salesmanPhoneInput" class="form-control" placeholder="{{ __('+966 50 123 4567') }}" value="{{ old('salesman_phone', $currentEmployee?->phone ?: (auth()->user()?->employee?->phone ?: (auth()->user()?->phone ?: ''))) }}">
                    </div>
                </div>
                @else
                    @if($currentEmployee)
                        <input type="hidden" name="employee_id" value="{{ $currentEmployee->id }}">
                        <input type="hidden" name="salesman_name" id="salesmanNameInput" value="{{ $currentEmployee->name }}">
                        <input type="hidden" name="salesman_phone" id="salesmanPhoneInput" value="{{ $currentEmployee->phone }}">
                    @else
                        <input type="hidden" name="salesman_name" id="salesmanNameInput" value="{{ auth()->user()?->name }}">
                        <input type="hidden" name="salesman_phone" id="salesmanPhoneInput" value="{{ auth()->user()?->phone }}">
                    @endif
                @endif

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark small">{{ __('Special Requests / Notes') }}</label>
                    <textarea name="notes" rows="2" class="form-control" placeholder="{{ __('Needs front seat, travelling with family, wheelchair request...') }}">{{ old('notes') }}</textarea>
                </div>

                <div class="mt-4 pt-2 border-top d-flex justify-content-end gap-2">
                    <a href="{{ route('travel.bookings.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary-modern px-4" id="submitBookingBtn">
                        <i class="fas fa-check-circle me-1"></i> {{ __('Confirm Reservation & Print Ticket') }}
                    </button>
                </div>
            </x-card>
        </div>
    </div>
</form>

<style>
.seat-picker-btn {
    width: 44px;
    height: 44px;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.78rem;
    cursor: pointer;
    border: 1.5px solid transparent;
    transition: all 0.15s ease;
}

.seat-state-free {
    background-color: #E8FBF4;
    border-color: #10B981;
    color: #047857;
}

.seat-class-vip {
    background-color: #FEF3C7 !important;
    border-color: #F59E0B !important;
    color: #92400E !important;
}
.seat-class-vip:hover {
    background-color: #F59E0B !important;
    color: #FFFFFF !important;
}

.seat-class-business {
    background-color: #EFF6FF !important;
    border-color: #3B82F6 !important;
    color: #1D4ED8 !important;
}
.seat-class-business:hover {
    background-color: #3B82F6 !important;
    color: #FFFFFF !important;
}

.seat-class-economy {
    background-color: #E8FBF4 !important;
    border-color: #10B981 !important;
    color: #047857 !important;
}
.seat-class-economy:hover {
    background-color: #10B981 !important;
    color: #FFFFFF !important;
}

.seat-state-free:hover {
    background-color: #10B981;
    color: #FFFFFF;
    transform: scale(1.08);
}

.seat-state-booked {
    background-color: #FEE2E2;
    border-color: #EF4444;
    color: #B91C1C;
    cursor: not-allowed;
    opacity: 0.75;
}

.seat-state-selected {
    background-color: #4361EE !important;
    border-color: #2D3748 !important;
    color: #FFFFFF !important;
    transform: scale(1.12);
    box-shadow: 0 4px 12px rgba(67, 97, 238, 0.5) !important;
}

.live-dot-pulse {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background-color: #10B981;
    box-shadow: 0 0 0 rgba(16, 185, 129, 0.7);
    animation: livePulseAnim 1.8s infinite;
}

@keyframes livePulseAnim {
    0% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    }
    70% {
        transform: scale(1.15);
        box-shadow: 0 0 0 7px rgba(16, 185, 129, 0);
    }
    100% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
    }
}
</style>

@push('scripts')
<script>
    let currentTripData = null;
    let selectedSeats = [];
    let liveSyncInterval = null;
    let currentPollingTripId = null;
    let isPolling = false;

    @if($selectedSeat)
        selectedSeats = [{{ (int) $selectedSeat }}];
    @endif

    function onTripSelectChanged(tripId) {
        if (!tripId) return;
        selectedSeats = [];
        const hotelInput = document.getElementById('hotelExpenseInput');
        if (hotelInput) delete hotelInput.dataset.userEdited;
        updateSelectedSeatsUI();
        const alertBox = document.getElementById('liveSeatAlertContainer');
        if (alertBox) alertBox.style.display = 'none';
        updateTripScheduleBanner(tripId);
        fetchTripSeats(tripId);
    }

    function updateTripScheduleBanner(tripId) {
        const select = document.getElementById('tripSelect');
        if (!select) return;
        const opt = select.querySelector(`option[value="${tripId}"]`) || (select.selectedIndex >= 0 ? select.options[select.selectedIndex] : null);
        if (!opt) return;

        // Sync Date Filter input if user is not actively typing in it
        const dateInput = document.getElementById('tripDepartureDateFilter');
        if (dateInput && opt.dataset.departureDate && !dateInput.matches(':focus')) {
            dateInput.value = opt.dataset.departureDate;
        }

        // Update Live Banner
        const cardEl = document.getElementById('tripScheduleCard');
        if (cardEl) cardEl.style.display = 'block';

        const codeEl = document.getElementById('tripScheduleCode');
        const titleEl = document.getElementById('tripScheduleTitle');
        const freeSeatsEl = document.getElementById('tripScheduleFreeSeats');
        const depEl = document.getElementById('tripScheduleDeparture');
        const retEl = document.getElementById('tripScheduleReturn');
        const fromEl = document.getElementById('tripScheduleFromCity');
        const toEl = document.getElementById('tripScheduleToCity');

        if (codeEl) codeEl.textContent = opt.dataset.tripCode || '';
        if (titleEl) titleEl.textContent = opt.dataset.tripTitle || '';
        if (freeSeatsEl) freeSeatsEl.innerHTML = `<i class="fas fa-chair me-1"></i> ${opt.dataset.availableSeats || 0} {{ __('Seats Free') }}`;
        if (depEl) depEl.textContent = opt.dataset.departureDatetime || '{{ __("Not Set") }}';
        if (retEl) retEl.textContent = opt.dataset.returnDatetime || '{{ __("Not Set") }}';
        if (fromEl) fromEl.innerHTML = `<i class="fas fa-map-marker-alt text-danger me-1"></i> ${opt.dataset.departureCity || ''}`;
        if (toEl) {
            let toHtml = `<i class="fas fa-map-marker-alt text-primary me-1"></i> ${opt.dataset.destinationCity || ''}`;
            if (opt.dataset.duration) {
                toHtml += ` &bull; <span class="badge bg-light text-dark border extra-small">${opt.dataset.duration} {{ __('Days') }}</span>`;
            }
            toEl.innerHTML = toHtml;
        }
    }

    function filterTripsByDate(targetDate) {
        const select = document.getElementById('tripSelect');
        const alertBox = document.getElementById('noTripsForDateAlert');
        const badge = document.getElementById('tripsCountBadge');
        if (!select) return;

        let matchingTripId = null;
        let matchCount = 0;
        const total = select.options.length;

        Array.from(select.options).forEach(function(opt) {
            const optDate = opt.dataset.departureDate || '';
            const matches = !targetDate || optDate === targetDate;
            if (matches) {
                matchCount++;
                if (!matchingTripId) matchingTripId = opt.value;
            }
        });

        if (targetDate && matchCount === 0) {
            if (alertBox) alertBox.style.display = 'block';
            if (badge) badge.textContent = `0 / ${total} {{ __('Trips') }}`;
        } else {
            if (alertBox) alertBox.style.display = 'none';
            if (badge) {
                badge.textContent = targetDate ? `${matchCount} / ${total} {{ __('Trips on Date') }}` : `${total} {{ __('Available Trips') }}`;
            }
            if (matchingTripId && select.value !== matchingTripId) {
                select.value = matchingTripId;
                if (select._filterControl) {
                    select._filterControl.syncFromSelect();
                }
                onTripSelectChanged(matchingTripId);
            }
        }

        if (select._filterControl) {
            if (targetDate) {
                select._filterControl.filterOptionsByDate(targetDate);
            } else {
                select._filterControl.filterOptions('');
            }
        }
    }

    function fetchTripSeats(tripId) {
        currentPollingTripId = tripId;
        const container = document.getElementById('interactiveSeatsContainer');
        if (container && (!currentTripData || currentTripData.trip_id != tripId)) {
            container.innerHTML = `
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-spinner fa-spin fa-2x mb-2 text-primary"></i>
                    <div class="small fw-semibold">{{ __('Loading bus seat layout...') }}</div>
                </div>
            `;
        }

        fetch(`{{ url('travel/trips') }}/${tripId}/seats-json`)
            .then(res => {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(data => {
                currentTripData = data;
                renderBusSeats(data);
                startLiveSeatSync(tripId);

                // Update default fares
                const pkgType = document.querySelector('input[name="package_type"]:checked')?.value || 'with_hotel';
                updateFaresFromTrip(data, pkgType);

                if (data.hotel_name) {
                    const hNameInput = document.getElementById('hotelNameInput');
                    if (hNameInput) hNameInput.value = data.hotel_name;
                }
                if (data.hotel_address) {
                    const hAddrInput = document.getElementById('hotelAddressInput');
                    if (hAddrInput) hAddrInput.value = data.hotel_address;
                }
            })
            .catch(err => {
                console.error('Error loading seats:', err);
                if (container) {
                    container.innerHTML = `
                        <div class="text-center py-3 text-danger">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                            <div class="small fw-bold">{{ __('Could not load seats layout. Please retry.') }}</div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="fetchTripSeats(${tripId})">
                                <i class="fas fa-redo me-1"></i> {{ __('Retry') }}
                            </button>
                        </div>
                    `;
                }
            });
    }

    function startLiveSeatSync(tripId) {
        if (liveSyncInterval) {
            clearInterval(liveSyncInterval);
            liveSyncInterval = null;
        }
        currentPollingTripId = tripId;
        if (!tripId) return;

        // Poll every 3 seconds silently in background without page refresh
        liveSyncInterval = setInterval(() => {
            pollSeatsLive(currentPollingTripId);
        }, 3000);
    }

    function pollSeatsLive(tripId) {
        if (!tripId || tripId !== currentPollingTripId || isPolling) return;
        isPolling = true;

        fetch(`{{ url('travel/trips') }}/${tripId}/seats-json`)
            .then(res => res.json())
            .then(data => {
                isPolling = false;
                syncLiveSeatsDOM(data);
            })
            .catch(err => {
                isPolling = false;
            });
    }

    function syncLiveSeatsDOM(data) {
        if (!data || !data.seats || !Array.isArray(data.seats)) return;
        currentTripData = data;
        let collisionOccurred = false;
        let collidedSeats = [];

        data.seats.forEach(s => {
            const btn = document.getElementById('seatBtn_' + s.seat_number);
            if (!btn) return;

            const isNowBooked = !!s.is_booked;
            const wasBooked = btn.classList.contains('seat-state-booked');
            const sClass = s.seat_class || 'economy';
            const sFare = parseFloat(s.seat_fare) || 0;

            btn.dataset.seatClass = sClass;
            btn.dataset.seatFare = sFare;

            if (isNowBooked && !wasBooked) {
                // Seat newly booked from another counter / branch!
                btn.classList.remove('seat-state-free', 'seat-state-selected', 'seat-class-vip', 'seat-class-business', 'seat-class-economy');
                btn.classList.add('seat-state-booked');
                btn.disabled = true;
                btn.title = `{{ __("Seat") }} #${s.seat_number} [${sClass.toUpperCase()}] - {{ __("Booked by") }} ${s.passenger_name || 'Counter'}`;
                btn.innerHTML = `<span>${s.seat_number}</span><i class="fas fa-lock" style="font-size: 0.6rem;"></i>`;
                btn.onclick = null;

                // If currently selected by this user/counter, remove it immediately!
                const idx = selectedSeats.indexOf(s.seat_number);
                if (idx > -1) {
                    selectedSeats.splice(idx, 1);
                    collisionOccurred = true;
                    collidedSeats.push(s.seat_number);
                }
            } else if (!isNowBooked && wasBooked) {
                // Seat was freed / booking cancelled
                btn.classList.remove('seat-state-booked');
                btn.classList.add('seat-state-free', 'seat-class-' + sClass);
                btn.disabled = false;
                const iconClass = (sClass === 'vip') ? 'fa-crown' : (sClass === 'business' ? 'fa-briefcase' : 'fa-chair');
                btn.title = `{{ __("Seat") }} #${s.seat_number} [${sClass.toUpperCase()}] - ${sFare > 0 ? sFare + ' SAR' : '{{ __("Available") }}'}`;
                btn.innerHTML = `<span>${s.seat_number}</span><i class="fas ${iconClass}" style="font-size: 0.6rem;"></i>`;
                btn.onclick = () => toggleSeat(s.seat_number);
            }
        });

        if (collisionOccurred) {
            updateSelectedSeatsUI();
            showLiveCollisionAlert(collidedSeats);
        }
    }

    function showLiveCollisionAlert(seatNums) {
        const box = document.getElementById('liveSeatAlertContainer');
        if (!box) return;
        const formatted = seatNums.map(n => '#' + n).join(', ');
        box.innerHTML = `
            <div class="alert alert-warning alert-dismissible fade show shadow-sm border-warning p-2 mb-2 d-flex align-items-center" role="alert" style="font-size: 0.85rem;">
                <i class="fas fa-exclamation-triangle fa-lg me-2 text-warning flex-shrink-0"></i>
                <div class="flex-grow-1">
                    <strong>{{ __("Live Update:") }}</strong> {{ __("Seat") }} <strong>${formatted}</strong> {{ __("was just booked by another counter/office. It has been locked and removed from your selection automatically.") }}
                </div>
                <button type="button" class="btn-close p-2" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        box.style.display = 'block';
    }

    function renderBusSeats(data) {
        const container = document.getElementById('interactiveSeatsContainer');
        if (!container) return;
        container.innerHTML = '';

        const totalSeats = parseInt(data.total_seats) || 49;
        const seats = data.seats || [];
        const seatsMap = {};
        seats.forEach(s => seatsMap[s.seat_number] = s);

        let counter = 1;
        let rowNum = 1;

        // Render rows of 4 (2 left, aisle, 2 right) as long as more than 5 seats remain
        while ((totalSeats - counter + 1) > 5) {
            const rowDiv = document.createElement('div');
            rowDiv.className = 'd-flex align-items-center justify-content-between mb-1';

            // Left pair
            const leftDiv = document.createElement('div');
            leftDiv.className = 'd-flex gap-2';
            for (let i = 0; i < 2 && counter <= totalSeats; i++) {
                const sNum = counter++;
                leftDiv.appendChild(createSeatElement(sNum, seatsMap[sNum]));
            }

            // Aisle
            const aisleDiv = document.createElement('div');
            aisleDiv.className = 'text-muted extra-small opacity-50 px-2';
            aisleDiv.style.fontSize = '0.65rem';
            aisleDiv.innerText = rowNum++;

            // Right pair
            const rightDiv = document.createElement('div');
            rightDiv.className = 'd-flex gap-2';
            for (let i = 0; i < 2 && counter <= totalSeats; i++) {
                const sNum = counter++;
                rightDiv.appendChild(createSeatElement(sNum, seatsMap[sNum]));
            }

            rowDiv.appendChild(leftDiv);
            rowDiv.appendChild(aisleDiv);
            rowDiv.appendChild(rightDiv);
            container.appendChild(rowDiv);
        }

        // Back Bench Row (remaining seats, up to 5)
        if (counter <= totalSeats) {
            const remainingCount = (totalSeats - counter + 1);
            const backRowContainer = document.createElement('div');
            backRowContainer.className = 'mt-2 pt-2 border-top';

            const backLabel = document.createElement('div');
            backLabel.className = 'text-center extra-small text-muted mb-1';
            backLabel.innerText = `{{ __('Back Row') }} (${remainingCount} {{ __('Seats') }})`;
            backRowContainer.appendChild(backLabel);

            const backSeatsDiv = document.createElement('div');
            backSeatsDiv.className = 'd-flex justify-content-between gap-1';

            while (counter <= totalSeats) {
                const sNum = counter++;
                backSeatsDiv.appendChild(createSeatElement(sNum, seatsMap[sNum]));
            }

            backRowContainer.appendChild(backSeatsDiv);
            container.appendChild(backRowContainer);
        }
    }

    function createSeatElement(seatNum, seatInfo) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.id = 'seatBtn_' + seatNum;
        btn.className = 'seat-picker-btn';

        const isBooked = seatInfo && seatInfo.is_booked;
        const seatClass = seatInfo ? (seatInfo.seat_class || 'economy') : 'economy';
        const seatFare = seatInfo ? (parseFloat(seatInfo.seat_fare) || 0) : 0;
        btn.dataset.seatClass = seatClass;
        btn.dataset.seatFare = seatFare;

        if (isBooked) {
            btn.classList.add('seat-state-booked');
            btn.disabled = true;
            btn.title = `{{ __("Seat") }} #${seatNum} [${seatClass.toUpperCase()}] - {{ __("Booked by") }} ${seatInfo.passenger_name}`;
            btn.innerHTML = `<span>${seatNum}</span><i class="fas fa-lock" style="font-size: 0.6rem;"></i>`;
        } else {
            btn.classList.add('seat-state-free');
            btn.classList.add('seat-class-' + seatClass);
            if (selectedSeats.includes(seatNum)) {
                btn.classList.add('seat-state-selected');
            }
            const iconClass = (seatClass === 'vip') ? 'fa-crown' : (seatClass === 'business' ? 'fa-briefcase' : 'fa-chair');
            btn.title = `{{ __("Seat") }} #${seatNum} [${seatClass.toUpperCase()}] - ${seatFare > 0 ? seatFare + ' SAR' : '{{ __("Available") }}'}`;
            btn.innerHTML = `<span>${seatNum}</span><i class="fas ${iconClass}" style="font-size: 0.6rem;"></i>`;
            btn.onclick = () => toggleSeat(seatNum);
        }

        return btn;
    }

    function toggleSeat(seatNum) {
        const idx = selectedSeats.indexOf(seatNum);
        if (idx > -1) {
            selectedSeats.splice(idx, 1);
        } else {
            selectedSeats.push(seatNum);
        }
        selectedSeats.sort((a, b) => a - b);
        updateSelectedSeatsUI();
    }

    function clearAllSelectedSeats() {
        selectedSeats = [];
        const hotelInput = document.getElementById('hotelExpenseInput');
        if (hotelInput) delete hotelInput.dataset.userEdited;
        updateSelectedSeatsUI();
    }

    function updateSelectedSeatsUI() {
        const inputSeats = document.getElementById('seatNumbersInput');
        const inputSingle = document.getElementById('seatNumberInput');
        const displayEl = document.getElementById('selectedSeatDisplay');
        const badgeEl = document.getElementById('seatConfirmedBadge');
        const badgeCount = document.getElementById('seatsCountBadgeText');
        const clearBtn = document.getElementById('clearSeatsBtn');

        inputSeats.value = selectedSeats.join(',');
        inputSingle.value = selectedSeats.length > 0 ? selectedSeats[0] : '';

        // Update button visual states
        document.querySelectorAll('.seat-picker-btn').forEach(btn => {
            const num = parseInt(btn.id.replace('seatBtn_', ''));
            if (selectedSeats.includes(num)) {
                btn.classList.add('seat-state-selected');
            } else {
                btn.classList.remove('seat-state-selected');
            }
        });

        if (selectedSeats.length === 0) {
            displayEl.innerHTML = '<span class="text-muted fs-6">{{ __("Click one or more seats below") }}</span>';
            badgeEl.style.display = 'none';
            if (clearBtn) clearBtn.style.display = 'none';
        } else {
            const badges = selectedSeats.map(s => {
                const btn = document.getElementById('seatBtn_' + s);
                const sClass = btn?.dataset.seatClass || 'economy';
                const badgeColor = sClass === 'vip' ? 'bg-warning text-dark' : (sClass === 'business' ? 'bg-primary text-white' : 'bg-success text-white');
                return `<span class="badge ${badgeColor} fs-6 me-1">#${s} (${sClass.toUpperCase()})</span>`;
            }).join(' ');
            displayEl.innerHTML = badges;
            badgeEl.style.display = 'inline-block';
            badgeCount.innerText = selectedSeats.length + ' ' + (selectedSeats.length === 1 ? '{{ __("1 Seat Selected") }}' : '{{ __("seats selected") }}');
            if (clearBtn) clearBtn.style.display = 'inline-block';
        }

        recalcAutoFares();
        updateMultiplePassengersUI();
    }

    function updateMultiplePassengersUI() {
        const sec = document.getElementById('multiplePassengersSection');
        const list = document.getElementById('passengerSeatsList');
        const badge = document.getElementById('multiPassCountBadge');
        if (!sec || !list) return;

        if (selectedSeats.length > 1) {
            sec.style.display = 'block';
            badge.innerText = selectedSeats.length + ' {{ __("Seats") }}';

            // Preserve currently entered values in a temporary map
            const currentVals = {};
            list.querySelectorAll('[data-seat-row]').forEach(row => {
                const s = row.getAttribute('data-seat-row');
                currentVals[s] = {
                    name: row.querySelector(`[name="passengers[${s}][name]"]`)?.value || '',
                    gender: row.querySelector(`[name="passengers[${s}][gender]"]`)?.value || 'male',
                    id_number: row.querySelector(`[name="passengers[${s}][id_number]"]`)?.value || '',
                    nationality: row.querySelector(`[name="passengers[${s}][nationality]"]`)?.value || '',
                    pickup_location: row.querySelector(`[name="passengers[${s}][pickup_location]"]`)?.value || '',
                    dropoff_location: row.querySelector(`[name="passengers[${s}][dropoff_location]"]`)?.value || ''
                };
            });

            // Rebuild rows
            list.innerHTML = '';
            selectedSeats.forEach((sNum, idx) => {
                const saved = currentVals[sNum] || {};
                const isLead = (idx === 0);
                const defaultGender = saved.gender || (isLead ? (document.querySelector('[name="passenger_gender"]')?.value || 'male') : 'male');
                const defaultNat = saved.nationality || (isLead ? (document.querySelector('[name="passenger_nationality"]')?.value || 'PAK') : 'PAK');
                const currentPickupVal = document.querySelector('[name="pickup_location"]')?.value || 'Office';
                const defaultLoc = saved.pickup_location || currentPickupVal;
                const defaultDrop = saved.dropoff_location || (isLead ? (document.querySelector('[name="dropoff_location"]')?.value || '') : '');
                
                const row = document.createElement('div');
                row.className = 'p-2 rounded-3 border bg-light mb-2';
                row.setAttribute('data-seat-row', sNum);
                row.innerHTML = `
                    <div class="row g-2 align-items-center">
                        <div class="col-md-2 d-flex align-items-center gap-1">
                            <span class="badge bg-primary fs-6 px-2 py-1">#${sNum}</span>
                            <span class="badge ${isLead ? 'bg-success' : 'bg-secondary'} extra-small">${isLead ? '{{ __("Lead") }}' : '#' + (idx + 1)}</span>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="passengers[${sNum}][name]" class="form-control form-control-sm bg-white" 
                                placeholder="${isLead ? '{{ __("Lead Passenger Name") }}' : '{{ __("Passenger Full Name") }}'}" 
                                value="${saved.name || (isLead ? (document.querySelector('[name="passenger_name"]')?.value || '') : '')}">
                        </div>
                        <div class="col-md-2">
                            <input type="text" name="passengers[${sNum}][id_number]" class="form-control form-control-sm bg-white" 
                                placeholder="{{ __('ID / Iqama') }}" value="${saved.id_number || (isLead ? (document.querySelector('[name="passenger_id_number"]')?.value || '') : '')}">
                        </div>
                        <div class="col-md-2">
                            <input type="text" name="passengers[${sNum}][dropoff_location]" class="form-control form-control-sm bg-white" 
                                placeholder="{{ __('Drop Point (النزول)') }}" value="${saved.dropoff_location || defaultDrop}">
                        </div>
                        <div class="col-md-2">
                            <input type="text" name="passengers[${sNum}][pickup_location]" class="form-control form-control-sm bg-white" 
                                placeholder="{{ __('Pickup Location') }}" value="${saved.pickup_location || defaultLoc}"
                                @if(!$isAdmin) readonly style="background-color: #f8fafc; cursor: not-allowed;" title="{{ __('Only Admin can change boarding location') }}" @endif>
                        </div>
                        <div class="col-md-1">
                            <select name="passengers[${sNum}][gender]" class="form-select form-select-sm bg-white">
                                <option value="male" ${defaultGender === 'male' ? 'selected' : ''}>{{ __('M') }}</option>
                                <option value="female" ${defaultGender === 'female' ? 'selected' : ''}>{{ __('F') }}</option>
                            </select>
                        </div>
                    </div>
                `;
                list.appendChild(row);
            });
        } else {
            sec.style.display = 'none';
            list.innerHTML = '';
        }
    }

    function setDropoffVal(val) {
        const input = document.getElementById('dropoffLocationInput');
        if (input) input.value = val;
    }

    function setReturnDropoffVal(val) {
        const input = document.getElementById('returnDropoffLocationInput');
        if (input) input.value = val;
    }

    function clearReturnDropoffVal() {
        const input = document.getElementById('returnDropoffLocationInput');
        if (input) {
            input.value = '';
            input.focus();
        }
    }

    function onJourneyTypeChanged(type) {
        const dirSec = document.getElementById('oneWayDirectionSection');
        const retSec = document.getElementById('returnDropoffGroup');
        const lblRound = document.getElementById('labelRoundTrip');
        const lblOne = document.getElementById('labelOneWay');

        if (type === 'one_way') {
            if (dirSec) dirSec.style.display = 'block';
            if (retSec) retSec.style.display = 'none';
            if (lblOne) lblOne.classList.add('border-info', 'bg-info-subtle');
            if (lblRound) lblRound.classList.remove('border-primary', 'bg-primary-subtle');

            updateDirectionOptions();

            // Default to without_hotel for one-way transport unless user explicitly re-selects hotel
            const withoutRadio = document.getElementById('pkg_without_hotel');
            if (withoutRadio && !withoutRadio.checked) {
                withoutRadio.checked = true;
                onPackageTypeChanged('without_hotel');
                return;
            }
        } else {
            if (dirSec) dirSec.style.display = 'none';
            if (retSec) retSec.style.display = 'block';
            if (lblRound) lblRound.classList.add('border-primary', 'bg-primary-subtle');
            if (lblOne) lblOne.classList.remove('border-info', 'bg-info-subtle');
        }

        const paidInput = document.getElementById('paidAmountInput');
        if (paidInput) delete paidInput.dataset.userEdited;

        recalcAutoFares();
    }

    function onOneWayDirectionChanged(dir) {
        // Updated direction
    }

    function updateDirectionOptions() {
        if (!currentTripData) return;
        const depCity = currentTripData.departure_city || 'Origin';
        const destCity = currentTripData.destination_city || 'Destination';

        const outOpt = document.getElementById('dirOutboundOption');
        const retOpt = document.getElementById('dirReturnOption');

        if (outOpt) outOpt.innerText = `{{ __('Outbound (ذهاب):') }} ${depCity} → ${destCity}`;
        if (retOpt) retOpt.innerText = `{{ __('Inbound (عودة):') }} ${destCity} → ${depCity}`;
    }

    function onPackageTypeChanged(pkgType) {
        const hotelSec = document.getElementById('hotelConfigSection');
        const hotelCol = document.getElementById('hotelExpenseCol');
        const lblWith = document.getElementById('labelWithHotel');
        const lblWithout = document.getElementById('labelWithoutHotel');

        if (pkgType === 'with_hotel') {
            hotelSec.style.display = 'block';
            hotelCol.style.display = 'block';
            lblWith.classList.add('border-success', 'bg-success-subtle');
            lblWithout.classList.remove('border-primary', 'bg-primary-subtle');
        } else {
            hotelSec.style.display = 'none';
            hotelCol.style.display = 'none';
            document.getElementById('hotelExpenseInput').value = '0.00';
            lblWithout.classList.add('border-primary', 'bg-primary-subtle');
            lblWith.classList.remove('border-success', 'bg-success-subtle');
        }

        const hotelInput = document.getElementById('hotelExpenseInput');
        if (hotelInput) delete hotelInput.dataset.userEdited;

        const paidInput = document.getElementById('paidAmountInput');
        if (paidInput) delete paidInput.dataset.userEdited;

        recalcAutoFares();
    }

    function updateFaresFromTrip(trip, pkgType) {
        updateDirectionOptions();
        recalcAutoFares();
    }

    function recalcAutoFares() {
        const count = selectedSeats.length;
        const fareInput = document.getElementById('travelFareInput');
        const hotelInput = document.getElementById('hotelExpenseInput');
        const noteEl = document.getElementById('fareMultiplierNote');
        const hotelNoteEl = document.getElementById('hotelMultiplierNote');
        const journeyType = document.querySelector('input[name="journey_type"]:checked')?.value || 'round_trip';
        const isOneWay = (journeyType === 'one_way');
        const pkgType = document.querySelector('input[name="package_type"]:checked')?.value || 'with_hotel';

        if (currentTripData) {
            const roundTripBase = parseFloat(currentTripData.price_without_hotel) || 1;
            const oneWayBase = parseFloat(currentTripData.one_way_price) || 0;
            // Ratio: One-Way is half (50%) by default, or exact ratio if one_way_price is set and less than round_trip
            let ratio = 1.0;
            if (isOneWay) {
                if (oneWayBase > 0 && oneWayBase < roundTripBase) {
                    ratio = oneWayBase / roundTripBase;
                } else {
                    ratio = 0.5;
                }
            }

            if (count === 0) {
                fareInput.value = '0.00';
                hotelInput.value = '0.00';
                if (noteEl) noteEl.innerText = '{{ __("Please click one or more seats on the bus map above to calculate fare.") }}';
                if (hotelNoteEl) hotelNoteEl.innerText = '';
            } else {
                let totalTravelFare = 0;
                const classCounts = { vip: 0, business: 0, economy: 0 };

                selectedSeats.forEach(s => {
                    const btn = document.getElementById('seatBtn_' + s);
                    const baseFare = parseFloat(btn?.dataset.seatFare) || roundTripBase;
                    const fare = isOneWay ? Math.round(baseFare * ratio) : baseFare;
                    const sClass = btn?.dataset.seatClass || 'economy';
                    totalTravelFare += fare;
                    classCounts[sClass] = (classCounts[sClass] || 0) + 1;
                });

                fareInput.value = totalTravelFare.toFixed(2);

                const unitFare = parseFloat(currentTripData.price_without_hotel) || 0;
                const unitWithHotel = parseFloat(currentTripData.price_with_hotel) || 0;

                if (pkgType === 'with_hotel') {
                    const unitHotelDiff = Math.max(0, unitWithHotel - unitFare);
                    const roomTypeSelect = document.getElementById('roomTypeSelect');
                    const opt = roomTypeSelect ? roomTypeSelect.options[roomTypeSelect.selectedIndex] : null;
                    const badgeText = opt ? opt.text : '{{ __("Share room") }}';

                    const pill = document.getElementById('roomTypePillBadge');
                    if (pill) pill.innerText = badgeText;

                    if (!hotelInput.dataset.userEdited) {
                        const calculatedHotel = unitHotelDiff * count;
                        hotelInput.value = calculatedHotel.toFixed(2);
                    }

                    const topRoomPriceInput = document.getElementById('roomCategoryPriceInput');
                    if (topRoomPriceInput && !topRoomPriceInput.matches(':focus')) {
                        topRoomPriceInput.value = hotelInput.value;
                    }

                    if (hotelNoteEl) {
                        hotelNoteEl.innerText = `${badgeText}: SAR ${hotelInput.value} (${count} {{ __('pax') }})`;
                    }
                } else {
                    hotelInput.value = '0.00';
                    if (hotelNoteEl) hotelNoteEl.innerText = '';
                }

                if (noteEl) {
                    let parts = [];
                    if (classCounts.vip > 0) parts.push(`${classCounts.vip}x VIP`);
                    if (classCounts.business > 0) parts.push(`${classCounts.business}x Business`);
                    if (classCounts.economy > 0) parts.push(`${classCounts.economy}x Economy`);
                    const breakdown = parts.length > 0 ? ` (${parts.join(', ')})` : '';
                    const typeText = isOneWay ? ' [{{ __("ONE-WAY / ذهاب فقط") }}]' : ' [{{ __("ROUND TRIP / ذهاب وعودة") }}]';
                    noteEl.innerText = count + ' {{ __("Seat(s)") }}' + typeText + breakdown + ' = {{ currency_symbol() }} ' + totalTravelFare.toFixed(2);
                }
            }
        }

        recalcBooking();
    }

    function recalcBooking() {
        const travelFare = parseFloat(document.getElementById('travelFareInput').value) || 0;
        const hotelExpense = parseFloat(document.getElementById('hotelExpenseInput').value) || 0;
        const extraCharges = parseFloat(document.getElementById('extraChargesInput').value) || 0;
        const discount = parseFloat(document.getElementById('discountInput').value) || 0;
        const taxAmount = parseFloat(document.getElementById('taxInput').value) || 0;

        const total = Math.max(0, (travelFare + hotelExpense + extraCharges - discount + taxAmount));
        document.getElementById('totalAmountDisplay').innerText = '{{ currency_symbol() }} ' + total.toFixed(2);

        const paidInput = document.getElementById('paidAmountInput');
        let paid = parseFloat(paidInput.value);
        if (isNaN(paid) || paid < 0) {
            paid = 0;
        }

        const balance = Math.max(0, total - paid);
        document.getElementById('balanceDueDisplay').innerText = '{{ currency_symbol() }} ' + balance.toFixed(2);

        // Dynamic Payment Status
        let status = 'unpaid';
        let statusLabel = '{{ __("UNPAID / غير مدفوع") }}';
        let statusClass = 'bg-danger-subtle text-danger border border-danger-subtle';

        if (total > 0 && paid >= total) {
            status = 'paid';
            statusLabel = '{{ __("PAID / مدفوع بالكامل") }}';
            statusClass = 'bg-success-subtle text-success border border-success-subtle';
        } else if (paid > 0 && paid < total) {
            status = 'partial';
            statusLabel = '{{ __("PARTIAL / مدفوع جزئياً") }}';
            statusClass = 'bg-warning-subtle text-warning-emphasis border border-warning-subtle';
        }

        const badge = document.getElementById('paymentStatusBadge');
        if (badge) {
            badge.className = 'badge px-3 py-1 fw-bold fs-7 ' + statusClass;
            badge.innerText = statusLabel;
        }
        const statusInput = document.getElementById('paymentStatusInput');
        if (statusInput) {
            statusInput.value = status;
        }
    }

    function onRoomCategoryPriceInput(val) {
        const hotelInput = document.getElementById('hotelExpenseInput');
        const helperText = document.getElementById('roomPriceHelperText');
        const hotelNoteEl = document.getElementById('hotelMultiplierNote');
        
        const price = parseFloat(val);
        const validPrice = (isNaN(price) || price < 0) ? 0 : price;
        if (hotelInput) {
            hotelInput.value = validPrice.toFixed(2);
            hotelInput.dataset.userEdited = 'true';
        }
        if (helperText) {
            helperText.innerHTML = '<span class="text-success fw-semibold"><i class="fas fa-check-circle me-1"></i>{{ __("Manual Custom Rate Active") }}</span>';
        }
        if (hotelNoteEl) {
            const count = selectedSeats.length || 1;
            hotelNoteEl.innerHTML = `<span class="text-success"><i class="fas fa-edit me-1"></i>{{ __("Custom Hotel Rate") }}: {{ currency_symbol() }}${validPrice.toFixed(2)} (${count} pax)</span>`;
        }
        recalcBooking();
    }

    function resetHotelPriceToAuto() {
        const hotelInput = document.getElementById('hotelExpenseInput');
        const helperText = document.getElementById('roomPriceHelperText');
        if (hotelInput) {
            delete hotelInput.dataset.userEdited;
        }
        if (helperText) {
            helperText.innerText = '{{ __("Enter custom rate or auto-calc") }}';
        }
        recalcAutoFares();
    }

    function syncCreateHotelNights(source) {
        const mkInput = document.getElementById('makkahNightsInput');
        const mdInput = document.getElementById('madinahNightsInput');
        const totInput = document.getElementById('nightsCountInput');
        const cityInput = document.getElementById('hotelCityInput');

        let mk = parseInt(mkInput ? mkInput.value : 0) || 0;
        let md = parseInt(mdInput ? mdInput.value : 0) || 0;
        let tot = parseInt(totInput ? totInput.value : 0) || 0;

        if (source === 'makkah' || source === 'madinah') {
            tot = mk + md;
            if (totInput) totInput.value = tot;
        } else if (source === 'total') {
            if (mk === 0 && md === 0 && tot > 0) {
                mk = tot;
                if (mkInput) mkInput.value = mk;
            } else if (mk + md !== tot) {
                mk = Math.max(0, tot - md);
                if (mkInput) mkInput.value = mk;
            }
        }

        if (cityInput) {
            if (mk > 0 && md > 0) {
                cityInput.value = 'Makkah & Madinah';
            } else if (md > 0) {
                cityInput.value = 'Madinah';
            } else {
                cityInput.value = 'Makkah';
            }
        }
    }

    function onRoomTypeChanged() {
        const sel = document.getElementById('roomTypeSelect');
        const opt = sel ? sel.options[sel.selectedIndex] : null;
        const badgeText = opt ? opt.text : '';
        const pill = document.getElementById('roomTypePillBadge');
        if (pill) pill.innerText = badgeText;
        const hotelNoteEl = document.getElementById('hotelMultiplierNote');
        const hotelInput = document.getElementById('hotelExpenseInput');
        if (hotelNoteEl && hotelInput) {
            const count = selectedSeats.length || 1;
            hotelNoteEl.innerText = `${badgeText}: SAR ${hotelInput.value} (${count} {{ __('pax') }})`;
        }
    }

    function onBookingEmployeeChanged(sel) {
        const opt = sel.options[sel.selectedIndex];
        if (opt) {
            const name = opt.getAttribute('data-name');
            const phone = opt.getAttribute('data-phone');
            const nameInput = document.getElementById('salesmanNameInput');
            const phoneInput = document.getElementById('salesmanPhoneInput');
            if (name && nameInput) nameInput.value = name;
            if (phone !== null && phone !== undefined && phoneInput) phoneInput.value = phone;

            const pickupInput = document.getElementById('pickupLocationInput');
            if (pickupInput && (!pickupInput.dataset.userEdited || pickupInput.value === '')) {
                if (name) pickupInput.value = name;
            }
        }
    }

    document.getElementById('pickupLocationInput')?.addEventListener('input', function() {
        this.dataset.userEdited = 'true';
    });

    document.getElementById('hotelExpenseInput')?.addEventListener('input', function() {
        this.dataset.userEdited = 'true';
        const topRoomPriceInput = document.getElementById('roomCategoryPriceInput');
        if (topRoomPriceInput && !topRoomPriceInput.matches(':focus')) {
            topRoomPriceInput.value = this.value;
        }
        const helperText = document.getElementById('roomPriceHelperText');
        if (helperText) {
            helperText.innerHTML = '<span class="text-success fw-semibold"><i class="fas fa-check-circle me-1"></i>{{ __("Manual Custom Rate Active") }}</span>';
        }
    });

    document.getElementById('paidAmountInput')?.addEventListener('input', function() {
        this.dataset.userEdited = 'true';
    });

    // Initialize on page load
    document.addEventListener("DOMContentLoaded", function () {
        const hotelInput = document.getElementById('hotelExpenseInput');
        const topRoomPriceInput = document.getElementById('roomCategoryPriceInput');
        if (hotelInput && topRoomPriceInput) {
            topRoomPriceInput.value = hotelInput.value;
        }

        // Initialize Filterable Search Dropdowns
        ['tripSelect', 'roomTypeSelect', 'bookingEmployeeSelect', 'oneWayDirectionSelect'].forEach(function(id) {
            const el = document.getElementById(id);
            if (el) initSearchableFilterSelect(el);
        });
        document.querySelectorAll('#seatBookingForm select:not([name*="passengers["])').forEach(function(el) {
            initSearchableFilterSelect(el);
        });

        // Initialize Departure Date Calendar Filter
        const dateFilterInput = document.getElementById('tripDepartureDateFilter');
        if (dateFilterInput) {
            dateFilterInput.addEventListener('change', function() {
                filterTripsByDate(this.value);
            });
        }
        const clearDateFilterBtn = document.getElementById('clearTripDateFilterBtn');
        if (clearDateFilterBtn) {
            clearDateFilterBtn.addEventListener('click', function() {
                if (dateFilterInput) dateFilterInput.value = '';
                filterTripsByDate(null);
            });
        }

        const initialTripId = document.getElementById('tripSelect')?.value;
        if (initialTripId) {
            updateTripScheduleBanner(initialTripId);
            fetchTripSeats(initialTripId);
        }
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
                searchStr: (opt.text + ' ' + opt.value + ' ' + (opt.dataset.name || '') + ' ' + (opt.dataset.phone || '') + ' ' + (opt.dataset.departureDate || '') + ' ' + (opt.dataset.departureDatetime || '') + ' ' + (opt.dataset.tripCode || '')).toLowerCase()
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

        select._filterControl = {
            filterOptions: filterOptions,
            filterOptionsByDate: function(d) {
                let visibleCount = 0;
                items.forEach(function(item) {
                    const optDate = item.opt.dataset.departureDate || '';
                    const isMatch = !d || optDate === d;
                    item.el.style.display = isMatch ? 'flex' : 'none';
                    if (isMatch) visibleCount++;
                });
                emptyEl.style.display = visibleCount === 0 ? 'block' : 'none';
            },
            syncFromSelect: syncFromSelect,
            buildOptions: buildOptions
        };

        buildOptions();
        return wrapper;
    }

    window.addEventListener('beforeunload', function() {
        if (liveSyncInterval) clearInterval(liveSyncInterval);
    });
</script>
@endpush
@endsection
