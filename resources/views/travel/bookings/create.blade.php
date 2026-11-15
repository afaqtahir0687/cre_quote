@extends('admin.layouts.app')

@section('title', __('Book Umrah Bus Seat'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="fas fa-ticket-alt text-primary me-2"></i>{{ __('Book Umrah Bus Seat') }}
        </h2>
        <p class="text-muted small mb-0">{{ __('Select trip, pick an available seat on the interactive bus layout, and register passenger details with package pricing.') }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('travel.bookings.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Bookings') }}
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
                                    data-duration="{{ $t->duration_days }}"
                                    {{ $selectedTrip && $selectedTrip->id == $t->id ? 'selected' : '' }}>
                                {{ $t->trip_code }} — {{ $t->title }} ({{ $t->availableSeatsCount() }} {{ __('Seats Free') }})
                            </option>
                        @endforeach
                    </select>
                    @error('travel_trip_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Selected Seat Banner -->
                <div class="p-3 rounded-3 mb-3 d-flex justify-content-between align-items-center border" style="background: #F0FDF4; border-color: #86EFAC !important;">
                    <div>
                        <span class="text-muted extra-small d-block uppercase fw-bold">{{ __('Allocated Seat Number') }}</span>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fs-4 fw-bold text-success" id="selectedSeatDisplay">
                                {{ $selectedSeat ? '#' . $selectedSeat : __('Click a Seat Below') }}
                            </span>
                            <span class="badge bg-success text-white extra-small" id="seatConfirmedBadge" style="{{ $selectedSeat ? '' : 'display:none;' }}">
                                <i class="fas fa-check-circle me-1"></i> {{ __('Selected') }}
                            </span>
                        </div>
                    </div>
                    <input type="hidden" name="seat_number" id="seatNumberInput" value="{{ old('seat_number', $selectedSeat) }}" required>
                    <div class="text-end">
                        <span class="badge bg-danger-subtle text-danger border extra-small d-block mb-1">
                            <i class="fas fa-lock me-1"></i> {{ __('Anti-Collision Protected') }}
                        </span>
                        <small class="text-muted extra-small">{{ __('Double-booking strictly blocked') }}</small>
                    </div>
                </div>
                @error('seat_number') <div class="alert alert-danger py-2 small mb-3">{{ $message }}</div> @enderror

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
                        <input type="text" name="passenger_id_number" class="form-control @error('passenger_id_number') is-invalid @enderror" placeholder="{{ __('e.g. 2489012345 or Passport #') }}" value="{{ old('passenger_id_number') }}">
                        @error('passenger_id_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
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
                        <div class="col-md-7">
                            <label class="form-label fw-bold text-dark small">{{ __('Hotel Accommodation Partner') }}</label>
                            <input type="text" name="hotel_name" id="hotelNameInput" class="form-control" placeholder="{{ __('e.g. Swissôtel Makkah') }}" value="{{ old('hotel_name', $selectedTrip?->hotel_name) }}">
                        </div>

                        <div class="col-md-5">
                            <label class="form-label fw-bold text-dark small">{{ __('Room Sharing Type') }}</label>
                            <select name="room_type" class="form-select">
                                <option value="quad_sharing" {{ old('room_type') === 'quad_sharing' ? 'selected' : '' }}>{{ __('Quad Sharing (رباعي)') }}</option>
                                <option value="triple_sharing" {{ old('room_type') === 'triple_sharing' ? 'selected' : '' }}>{{ __('Triple Sharing (ثلاثي)') }}</option>
                                <option value="double_sharing" {{ old('room_type') === 'double_sharing' ? 'selected' : '' }}>{{ __('Double Sharing (ثنائي)') }}</option>
                                <option value="private_room" {{ old('room_type') === 'private_room' ? 'selected' : '' }}>{{ __('Private Room (غرفة خاصة)') }}</option>
                            </select>
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
    let selectedSeatNumber = {{ $selectedSeat ? (int)$selectedSeat : 'null' }};

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
            if (selectedSeatNumber === seatNum) {
                btn.classList.add('seat-state-selected');
            }
            btn.title = `{{ __("Seat") }} #${seatNum} - {{ __("Available") }}`;
            btn.innerHTML = `<span>${seatNum}</span><i class="fas fa-chair" style="font-size: 0.6rem;"></i>`;
            btn.onclick = () => selectSeat(seatNum);
        }

        return btn;
    }

    function selectSeat(seatNum) {
        selectedSeatNumber = seatNum;
        document.getElementById('seatNumberInput').value = seatNum;
        document.getElementById('selectedSeatDisplay').innerText = '#' + seatNum;
        document.getElementById('seatConfirmedBadge').style.display = 'inline-block';

        // Clear previous selected styling
        document.querySelectorAll('.seat-state-selected').forEach(el => el.classList.remove('seat-state-selected'));
        const newBtn = document.getElementById('seatBtn_' + seatNum);
        if (newBtn) {
            newBtn.classList.add('seat-state-selected');
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

        if (currentTripData) {
            updateFaresFromTrip(currentTripData, pkgType);
        } else {
            recalcBooking();
        }
    }

    function updateFaresFromTrip(trip, pkgType) {
        const fareInput = document.getElementById('travelFareInput');
        const hotelInput = document.getElementById('hotelExpenseInput');

        const noHotelPrice = parseFloat(trip.price_without_hotel) || 0;
        const withHotelPrice = parseFloat(trip.price_with_hotel) || 0;

        fareInput.value = noHotelPrice.toFixed(2);

        if (pkgType === 'with_hotel') {
            const hotelDiff = Math.max(0, withHotelPrice - noHotelPrice);
            hotelInput.value = hotelDiff.toFixed(2);
        } else {
            hotelInput.value = '0.00';
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
        document.getElementById('totalAmountDisplay').innerText = '{{ currency_symbol() }}' + total.toFixed(2);

        const paidInput = document.getElementById('paidAmountInput');
        let paid = parseFloat(paidInput.value) || 0;

        // Auto-fill paid if pristine 0 on first calculate
        if (paid === 0 && !paidInput.dataset.userEdited) {
            paidInput.value = total.toFixed(2);
            paid = total;
        }

        const balance = Math.max(0, total - paid);
        document.getElementById('balanceDueDisplay').innerText = '{{ currency_symbol() }}' + balance.toFixed(2);
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
