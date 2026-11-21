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
                        <div class="d-flex gap-2">
                            <span class="badge bg-success-subtle text-success border extra-small">{{ __('Free (Click)') }}</span>
                            <span class="badge bg-danger-subtle text-danger border extra-small">{{ __('Booked (Locked)') }}</span>
                        </div>
                    </div>

                    <!-- Bus Cabin Header -->
                    <div class="p-2 mb-3 rounded-3 d-flex justify-content-between align-items-center text-white" style="background: linear-gradient(135deg, #1E293B, #0F172A);">
                        <div class="d-flex align-items-center gap-2 small fw-bold">
                            <i class="fas fa-circle-notch fa-spin text-info"></i>
                            <span>{{ __('Driver Cabin') }}</span>
                        </div>
                        <span class="extra-small text-warning"><i class="fas fa-door-open me-1"></i>{{ __('Door') }}</span>
                    </div>

                    <!-- Interactive Seat Buttons Container (populated dynamically) -->
                    <div id="interactiveSeatsContainer" class="d-flex flex-column gap-2">
                        <!-- Rendered via JavaScript -->
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
                        <input type="text" name="passenger_name" class="form-control @error('passenger_name') is-invalid @enderror" placeholder="{{ __('e.g. Muhammad Abdullah') }}" value="{{ old('passenger_name') }}" required>
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
                        <input type="text" name="passenger_id_number" id="passengerIdInput" class="form-control @error('passenger_id_number') is-invalid @enderror" placeholder="{{ __('e.g. 2489012345 or Passport #') }}" value="{{ old('passenger_id_number') }}">
                        @error('passenger_id_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">{{ __('Nationality (الجنسية)') }}</label>
                        <input type="text" name="passenger_nationality" id="passengerNatInput" class="form-control @error('passenger_nationality') is-invalid @enderror" placeholder="{{ __('e.g. PAK, IND, BAN, SAU, EGY, SRI') }}" value="{{ old('passenger_nationality', 'PAK') }}">
                        @error('passenger_nationality') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">{{ __('Pickup / Boarding Office Location (موقع الركوب)') }}</label>
                        <input type="text" name="pickup_location" id="pickupLocationInput" class="form-control @error('pickup_location') is-invalid @enderror" placeholder="{{ __('e.g. Main Office, Batha, Villa 12, Nesto') }}" value="{{ old('pickup_location', current_shop()?->name ?? 'Office') }}">
                        @error('pickup_location') <div class="invalid-feedback">{{ $message }}</div> @enderror
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

            <!-- 3. Package Type & Hotel Accommodations -->
            <x-card :title="__('3. Package Accommodation Selection')" class="mt-4">
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
                            <input type="text" name="hotel_name" id="hotelNameInput" class="form-control" placeholder="{{ __('e.g. Swissôtel Makkah') }}" value="{{ old('hotel_name', $selectedTrip?->hotel_name) }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark small">{{ __('Room Sharing Type') }}</label>
                            <select name="room_type" class="form-select">
                                <option value="quad_sharing" {{ old('room_type') === 'quad_sharing' ? 'selected' : '' }}>{{ __('Quad Sharing (رباعي)') }}</option>
                                <option value="triple_sharing" {{ old('room_type') === 'triple_sharing' ? 'selected' : '' }}>{{ __('Triple Sharing (ثلاثي)') }}</option>
                                <option value="double_sharing" {{ old('room_type') === 'double_sharing' ? 'selected' : '' }}>{{ __('Double Sharing (ثنائي)') }}</option>
                                <option value="private_room" {{ old('room_type') === 'private_room' ? 'selected' : '' }}>{{ __('Private Room (غرفة خاصة)') }}</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold text-dark small">{{ __('Hotel Nights') }}</label>
                            <input type="number" name="nights_count" id="nightsCountInput" class="form-control" min="1" max="90" placeholder="10" value="{{ old('nights_count', $selectedTrip?->duration_days ?: 10) }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold text-dark small"><i class="fas fa-map-marker-alt text-danger me-1"></i> {{ __('Hotel Address / Location in Makkah / Madinah') }}</label>
                            <input type="text" name="hotel_address" id="hotelAddressInput" class="form-control" placeholder="{{ __('e.g. Ibrahim Al Khalil Road, Makkah / Near Clock Tower') }}" value="{{ old('hotel_address', $selectedTrip?->hotel_address) }}">
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
                            <input type="number" step="0.01" name="travel_fare" id="travelFareInput" class="form-control" value="{{ old('travel_fare', $selectedTrip?->price_without_hotel ?? 250) }}" required oninput="recalcBooking()">
                        </div>
                        <small class="text-primary extra-small fw-semibold d-block mt-1" id="fareMultiplierNote"></small>
                    </div>

                    <div class="col-md-6" id="hotelExpenseCol">
                        <label class="form-label fw-bold text-dark small">{{ __('Hotel Accommodation Expense') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold">{{ currency_symbol() }}</span>
                            @php
                                $defHotelExp = max(0, ($selectedTrip?->price_with_hotel ?? 1250) - ($selectedTrip?->price_without_hotel ?? 250));
                            @endphp
                            <input type="number" step="0.01" name="hotel_expense" id="hotelExpenseInput" class="form-control" value="{{ old('hotel_expense', $defHotelExp) }}" oninput="recalcBooking()">
                        </div>
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
                            <label class="form-label fw-bold text-dark small">{{ __('Paid Amount (Cash In Hand / Card) *') }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-success-subtle text-success fw-bold">{{ currency_symbol() }}</span>
                                <input type="number" step="0.01" name="paid_amount" id="paidAmountInput" class="form-control fw-bold text-success" value="{{ old('paid_amount', '0.00') }}" required oninput="recalcBooking()">
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
                        <span class="text-muted">{{ __('Remaining Balance Due:') }}</span>
                        <strong class="text-danger fs-6" id="balanceDueDisplay">{{ currency_symbol() }}0.00</strong>
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
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark small"><i class="fas fa-user-tag text-primary me-1"></i> {{ __('Salesman / Agent / البائع أو مسؤول الحجز') }}</label>
                        <input type="text" name="salesman_name" class="form-control" placeholder="{{ __('e.g. Salesman Name') }}" value="{{ old('salesman_name', auth()->user()?->name) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark small"><i class="fas fa-phone-alt text-primary me-1"></i> {{ __('Salesman Phone / هاتف البائع') }}</label>
                        <input type="text" name="salesman_phone" class="form-control" placeholder="{{ __('e.g. +966 50 123 4567') }}" value="{{ old('salesman_phone', auth()->user()?->employee?->phone ?: (auth()->user()?->phone ?: '')) }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark small">{{ __('Special Requests / Notes') }}</label>
                    <textarea name="notes" rows="2" class="form-control" placeholder="{{ __('e.g. Needs front seat, travelling with family, wheelchair request...') }}">{{ old('notes') }}</textarea>
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
</style>

@push('scripts')
<script>
    let currentTripData = null;
    let selectedSeats = [];
    @if($selectedSeat)
        selectedSeats = [{{ (int) $selectedSeat }}];
    @endif

    function onTripSelectChanged(tripId) {
        if (!tripId) return;
        fetchTripSeats(tripId);
    }

    function fetchTripSeats(tripId) {
        fetch(`{{ url('travel/trips') }}/${tripId}/seats-json`)
            .then(res => res.json())
            .then(data => {
                currentTripData = data;
                renderBusSeats(data);

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
            .catch(err => console.error('Error loading seats:', err));
    }

    function renderBusSeats(data) {
        const container = document.getElementById('interactiveSeatsContainer');
        container.innerHTML = '';

        const totalSeats = data.total_seats || 49;
        const seats = data.seats || [];
        const seatsMap = {};
        seats.forEach(s => seatsMap[s.seat_number] = s);

        const standardRows = 11;
        let counter = 1;

        // Render rows 1 to 11
        for (let r = 1; r <= standardRows && counter <= totalSeats - 5; r++) {
            const rowDiv = document.createElement('div');
            rowDiv.className = 'd-flex align-items-center justify-content-between mb-1';

            // Left pair
            const leftDiv = document.createElement('div');
            leftDiv.className = 'd-flex gap-2';
            for (let i = 0; i < 2; i++) {
                const sNum = counter++;
                leftDiv.appendChild(createSeatElement(sNum, seatsMap[sNum]));
            }

            // Aisle
            const aisleDiv = document.createElement('div');
            aisleDiv.className = 'text-muted extra-small opacity-50 px-2';
            aisleDiv.style.fontSize = '0.65rem';
            aisleDiv.innerText = r;

            // Right pair
            const rightDiv = document.createElement('div');
            rightDiv.className = 'd-flex gap-2';
            for (let i = 0; i < 2; i++) {
                const sNum = counter++;
                rightDiv.appendChild(createSeatElement(sNum, seatsMap[sNum]));
            }

            rowDiv.appendChild(leftDiv);
            rowDiv.appendChild(aisleDiv);
            rowDiv.appendChild(rightDiv);
            container.appendChild(rowDiv);
        }

        // Back Bench Row (5 seats)
        if (counter <= totalSeats) {
            const backRowContainer = document.createElement('div');
            backRowContainer.className = 'mt-2 pt-2 border-top';

            const backLabel = document.createElement('div');
            backLabel.className = 'text-center extra-small text-muted mb-1';
            backLabel.innerText = '{{ __("Back Row (5 Seats)") }}';
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

        if (isBooked) {
            btn.classList.add('seat-state-booked');
            btn.disabled = true;
            btn.title = `{{ __("Seat") }} #${seatNum} - {{ __("Booked by") }} ${seatInfo.passenger_name}`;
            btn.innerHTML = `<span>${seatNum}</span><i class="fas fa-lock" style="font-size: 0.6rem;"></i>`;
        } else {
            btn.classList.add('seat-state-free');
            if (selectedSeats.includes(seatNum)) {
                btn.classList.add('seat-state-selected');
            }
            btn.title = `{{ __("Seat") }} #${seatNum} - {{ __("Available") }}`;
            btn.innerHTML = `<span>${seatNum}</span><i class="fas fa-chair" style="font-size: 0.6rem;"></i>`;
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
            const badges = selectedSeats.map(s => `<span class="badge bg-primary fs-6 me-1">#${s}</span>`).join(' ');
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
                    pickup_location: row.querySelector(`[name="passengers[${s}][pickup_location]"]`)?.value || ''
                };
            });

            // Rebuild rows
            list.innerHTML = '';
            selectedSeats.forEach((sNum, idx) => {
                const saved = currentVals[sNum] || {};
                const isLead = (idx === 0);
                const defaultGender = saved.gender || (isLead ? (document.querySelector('[name="passenger_gender"]')?.value || 'male') : 'male');
                const defaultNat = saved.nationality || (isLead ? (document.querySelector('[name="passenger_nationality"]')?.value || 'PAK') : 'PAK');
                const defaultLoc = saved.pickup_location || (isLead ? (document.querySelector('[name="pickup_location"]')?.value || 'Office') : 'Office');
                
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
                        <div class="col-md-1">
                            <input type="text" name="passengers[${sNum}][nationality]" class="form-control form-control-sm bg-white text-center" 
                                placeholder="PAK" value="${saved.nationality || defaultNat}">
                        </div>
                        <div class="col-md-2">
                            <input type="text" name="passengers[${sNum}][pickup_location]" class="form-control form-control-sm bg-white" 
                                placeholder="{{ __('Office/Location') }}" value="${saved.pickup_location || defaultLoc}">
                        </div>
                        <div class="col-md-2">
                            <select name="passengers[${sNum}][gender]" class="form-select form-select-sm bg-white">
                                <option value="male" ${defaultGender === 'male' ? 'selected' : ''}>{{ __('Male (ذكر)') }}</option>
                                <option value="female" ${defaultGender === 'female' ? 'selected' : ''}>{{ __('Female (أنثى)') }}</option>
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

        recalcAutoFares();
    }

    function updateFaresFromTrip(trip, pkgType) {
        recalcAutoFares();
    }

    function recalcAutoFares() {
        const count = Math.max(1, selectedSeats.length);
        const fareInput = document.getElementById('travelFareInput');
        const hotelInput = document.getElementById('hotelExpenseInput');
        const noteEl = document.getElementById('fareMultiplierNote');

        if (currentTripData) {
            const unitFare = parseFloat(currentTripData.price_without_hotel) || 0;
            const unitWithHotel = parseFloat(currentTripData.price_with_hotel) || 0;
            const pkgType = document.querySelector('input[name="package_type"]:checked')?.value || 'with_hotel';

            fareInput.value = (unitFare * count).toFixed(2);

            if (pkgType === 'with_hotel') {
                const unitHotelDiff = Math.max(0, unitWithHotel - unitFare);
                hotelInput.value = (unitHotelDiff * count).toFixed(2);
            } else {
                hotelInput.value = '0.00';
            }

            if (noteEl) {
                noteEl.innerText = count + ' {{ __("Seats") }} × {{ currency_symbol() }} ' + unitFare.toFixed(2) + ' = {{ currency_symbol() }} ' + (unitFare * count).toFixed(2);
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
        let paid = parseFloat(paidInput.value) || 0;

        // Auto-fill paid if user hasn't explicitly customized paid amount
        if (!paidInput.dataset.userEdited || paid === 0) {
            paidInput.value = total.toFixed(2);
            paid = total;
        }

        const balance = Math.max(0, total - paid);
        document.getElementById('balanceDueDisplay').innerText = '{{ currency_symbol() }} ' + balance.toFixed(2);
    }

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
</script>
@endpush
@endsection
