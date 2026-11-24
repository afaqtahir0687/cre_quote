@extends('admin.layouts.app')

@section('title', __('Book Umrah Bus Seat'))

@section('content')
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
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark">{{ __('Scheduled Trip / Route *') }}</label>
                    <select name="travel_trip_id" id="tripSelect" class="form-select @error('travel_trip_id') is-invalid @enderror" required onchange="onTripSelectChanged(this.value)">
                        @foreach($trips as $t)
                            <option value="{{ $t->id }}" 
                                    data-seats="{{ $t->total_seats }}" 
                                    data-fare-no-hotel="{{ $t->price_without_hotel }}"
                                    data-fare-with-hotel="{{ $t->price_with_hotel }}"
                                    data-hotel="{{ $t->hotel_name }}"
                                    data-hotel-address="{{ $t->hotel_address }}"
                                    data-duration="{{ $t->duration_days }}"
                                    {{ $selectedTrip && $selectedTrip->id == $t->id ? 'selected' : '' }}>
                                {{ $t->trip_code }} — {{ tdb($t->title) }} ({{ $t->availableSeatsCount() }} {{ __('Seats Free') }})
                            </option>
                        @endforeach
                    </select>
                    @error('travel_trip_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                        <label class="form-label fw-bold text-dark">{{ __('Pickup / Boarding Office Location (موقع الركوب)') }}</label>
                        @php
                            $defaultPickupLocation = $currentEmployee?->name 
                                ?: (auth()->user()?->employee?->name 
                                ?: (auth()->user()?->name 
                                ?: (current_shop()?->name ?? 'Office')));
                        @endphp
                        <input type="text" name="pickup_location" id="pickupLocationInput" class="form-control @error('pickup_location') is-invalid @enderror" placeholder="{{ __('Main Office, Batha, Villa 12, Nesto') }}" value="{{ old('pickup_location', $defaultPickupLocation) }}">
                        @error('pickup_location') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                            <input type="text" name="return_dropoff_location" id="returnDropoffLocationInput" class="form-control form-control-sm @error('return_dropoff_location') is-invalid @enderror" placeholder="{{ __('Taif Highway Stop, Al-Kharj, Muzahimiyah, SASCO Station (Leave empty if returning to main office)') }}" value="{{ old('return_dropoff_location') }}">
                            @error('return_dropoff_location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="d-flex gap-1 flex-wrap mt-2 align-items-center">
                                <span class="extra-small text-muted me-1">{{ __('Quick Presets:') }}</span>
                                <span class="badge bg-white text-secondary border extra-small" style="cursor: pointer;" onclick="setReturnDropoffVal('Same as Boarding Office')">{{ __('Same as Boarding Office') }}</span>
                                <span class="badge bg-white text-secondary border extra-small" style="cursor: pointer;" onclick="setReturnDropoffVal('Taif Highway Stop (طائف)')">{{ __('Taif Highway Stop') }}</span>
                                <span class="badge bg-white text-secondary border extra-small" style="cursor: pointer;" onclick="setReturnDropoffVal('Al-Kharj (الخرج)')">{{ __('Al-Kharj') }}</span>
                                <span class="badge bg-white text-secondary border extra-small" style="cursor: pointer;" onclick="setReturnDropoffVal('Muzahimiyah (المزاحمية)')">{{ __('Muzahimiyah') }}</span>
                                <span class="badge bg-white text-secondary border extra-small" style="cursor: pointer;" onclick="setReturnDropoffVal('Highway SASCO Station')">{{ __('SASCO Station') }}</span>
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
                        <div class="col-md-5">
                            <label class="form-label fw-bold text-dark small">{{ __('Hotel Accommodation Partner') }}</label>
                            <input type="text" name="hotel_name" id="hotelNameInput" class="form-control" placeholder="{{ __('Swissôtel Makkah') }}" value="{{ old('hotel_name', $selectedTrip?->hotel_name) }}">
                        </div>

                        <div class="col-md-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-bold text-dark small mb-0">{{ __('Room Category / Type') }}</label>
                                <span class="badge bg-primary-subtle text-primary extra-small" id="roomTypePillBadge">Quad Sharing</span>
                            </div>
                            <select name="room_type" id="roomTypeSelect" class="form-select form-select-sm" onchange="onRoomTypeChanged()">
                                <optgroup label="{{ __('Standard Shared Rooms (مشترك)') }}">
                                    <option value="quad_sharing" data-surcharge="0" data-badge="Quad (4 Beds)" {{ old('room_type', 'quad_sharing') === 'quad_sharing' ? 'selected' : '' }}>{{ __('Quad Sharing (رباعي - 4 Beds)') }}</option>
                                    <option value="triple_sharing" data-surcharge="100" data-badge="Triple (3 Beds)" {{ old('room_type') === 'triple_sharing' ? 'selected' : '' }}>{{ __('Triple Sharing (ثلاثي - 3 Beds)') }} (+100 SAR)</option>
                                    <option value="double_sharing" data-surcharge="200" data-badge="Double (2 Beds)" {{ old('room_type') === 'double_sharing' ? 'selected' : '' }}>{{ __('Double Sharing (ثنائي - 2 Beds)') }} (+200 SAR)</option>
                                </optgroup>
                                <optgroup label="{{ __('Personal & Private Rooms (خاص / شخصي)') }}">
                                    <option value="single_private" data-surcharge="500" data-badge="Personal Single" {{ old('room_type') === 'single_private' ? 'selected' : '' }}>{{ __('Personal / Single Private Room (شخصي / مفرد)') }} (+500 SAR)</option>
                                    <option value="private_room" data-surcharge="600" data-badge="Private Family" {{ old('room_type') === 'private_room' ? 'selected' : '' }}>{{ __('Private Family Room (غرفة عائلية خاصة)') }} (+600 SAR)</option>
                                </optgroup>
                                <optgroup label="{{ __('Luxury & VIP Suites (أجنحة وغرف فاخرة)') }}">
                                    <option value="luxury_haram_view" data-surcharge="800" data-badge="👑 Luxury Haram View" {{ old('room_type') === 'luxury_haram_view' ? 'selected' : '' }}>👑 {{ __('Luxury Haram View (إطلالة الحرم فاخرة)') }} (+800 SAR)</option>
                                    <option value="executive_suite" data-surcharge="1200" data-badge="⭐ Executive Suite" {{ old('room_type') === 'executive_suite' ? 'selected' : '' }}>⭐ {{ __('Executive Luxury Suite (جناح تنفيذي فاخر)') }} (+1,200 SAR)</option>
                                </optgroup>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold text-dark small">{{ __('Hotel Nights') }}</label>
                            <input type="number" name="nights_count" id="nightsCountInput" class="form-control" min="1" max="90" placeholder="10" value="{{ old('nights_count', $selectedTrip?->duration_days ?: 10) }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold text-dark small"><i class="fas fa-map-marker-alt text-danger me-1"></i> {{ __('Hotel Address / Location in Makkah / Madinah') }}</label>
                            <input type="text" name="hotel_address" id="hotelAddressInput" class="form-control" placeholder="{{ __('Ibrahim Al Khalil Road, Makkah / Near Clock Tower') }}" value="{{ old('hotel_address', $selectedTrip?->hotel_address) }}">
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
                        <label class="form-label fw-bold text-dark small">{{ __('Extra / Visa Charges') }}</label>
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
                            <select name="payment_method" class="form-select" required>
                                <option value="cash">{{ __('Cash at Counter (نقدي)') }}</option>
                                <option value="bank_transfer">{{ __('Bank Transfer (تحويل بنكي)') }}</option>
                                <option value="card">{{ __('Credit / Debit Card (شبكة)') }}</option>
                                <option value="online">{{ __('Online Payment (إلكتروني)') }}</option>
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
        fetchTripSeats(tripId);
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
                                placeholder="{{ __('Pickup Location') }}" value="${saved.pickup_location || defaultLoc}">
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
                    const surcharge = opt ? (parseFloat(opt.dataset.surcharge) || 0) : 0;
                    const badgeText = opt ? (opt.dataset.badge || 'Quad (4 Beds)') : 'Quad (4 Beds)';

                    const pill = document.getElementById('roomTypePillBadge');
                    if (pill) pill.innerText = badgeText;

                    if (!hotelInput.dataset.userEdited) {
                        const singleSeatHotel = Math.max(0, unitHotelDiff + surcharge);
                        const calculatedHotel = singleSeatHotel * count;
                        hotelInput.value = calculatedHotel.toFixed(2);
                    }

                    if (hotelNoteEl) {
                        const surchargeNote = surcharge > 0 ? ` (+SAR ${surcharge} tier fee)` : '';
                        hotelNoteEl.innerText = `${badgeText}: SAR ${hotelInput.value} (${count} {{ __('pax') }})${surchargeNote}`;
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

    function onRoomTypeChanged() {
        const hotelInput = document.getElementById('hotelExpenseInput');
        if (hotelInput) delete hotelInput.dataset.userEdited;
        recalcAutoFares();
    }

    function onBookingEmployeeChanged(sel) {
        const opt = sel.options[sel.selectedIndex];
        if (opt) {
            const name = opt.getAttribute('data-name');
            const phone = opt.getAttribute('data-phone');
            if (name) document.getElementById('salesmanNameInput').value = name;
            if (phone !== null && phone !== undefined) document.getElementById('salesmanPhoneInput').value = phone;

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
    });

    document.getElementById('paidAmountInput')?.addEventListener('input', function() {
        this.dataset.userEdited = 'true';
    });

    // Initialize on page load
    document.addEventListener("DOMContentLoaded", function () {
        const initialTripId = document.getElementById('tripSelect')?.value;
        if (initialTripId) {
            fetchTripSeats(initialTripId);
        }
    });

    window.addEventListener('beforeunload', function() {
        if (liveSyncInterval) clearInterval(liveSyncInterval);
    });
</script>
@endpush
@endsection
