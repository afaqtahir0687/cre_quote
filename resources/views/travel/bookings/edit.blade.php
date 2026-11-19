@extends('admin.layouts.app')

@section('title', __('Edit Passenger Booking') . ' - ' . $booking->booking_reference)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        <a href="{{ route('travel.bookings.show', $booking->id) }}" class="btn btn-light btn-sm rounded-circle me-3 shadow-sm">
            <i class="fas fa-arrow-left text-muted"></i>
        </a>
        <div class="d-flex align-items-center">
            <h2 class="fw-bold text-dark mb-0">{{ __('Edit Booking') }}</h2>
            <span class="badge-soft badge-soft-primary ms-3">{{ $booking->booking_reference }}</span>
            <span class="badge bg-primary text-white ms-2">{{ __('Seat #:seat', ['seat' => $booking->seat_number]) }}</span>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('travel.bookings.show', $booking->id) }}" class="btn btn-outline-info d-flex align-items-center shadow-sm">
            <i class="fas fa-eye me-2"></i> {{ __('View Details') }}
        </a>
    </div>
</div>

<form action="{{ route('travel.bookings.update', $booking->id) }}" method="POST" id="editBookingForm">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <!-- Left Column: Passenger & Seat Assignment -->
        <div class="col-lg-7">
            <x-card :title="__('1. Passenger Information & Seat Assignment')" class="mb-4">
                <!-- Trip Summary Header -->
                <div class="p-3 bg-light rounded-3 border mb-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted extra-small uppercase fw-bold d-block">{{ __('Scheduled Trip') }}</span>
                        <span class="fw-bold text-dark fs-6">{{ $booking->trip ? tdb($booking->trip->title) : __('Trip') }} ({{ $booking->trip ? $booking->trip->trip_code : '' }})</span>
                        <div class="extra-small text-muted">
                            {{ $booking->trip ? tdb($booking->trip->departure_city) : '' }} &rarr; {{ $booking->trip ? tdb($booking->trip->destination_city) : '' }}
                            &bull; {{ $booking->trip->departure_date ? $booking->trip->departure_date->format('d M Y') : '' }}
                        </div>
                    </div>
                    <span class="badge bg-primary text-white fs-6 px-3 py-2">
                        {{ __('Current Seat #:seat', ['seat' => $booking->seat_number]) }}
                    </span>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">{{ __('Seat Number (1-49) *') }}</label>
                        <input type="number" name="seat_number" id="seatNumberInput" class="form-control @error('seat_number') is-invalid @enderror" value="{{ old('seat_number', $booking->seat_number) }}" min="1" max="{{ $booking->trip->total_seats ?? 49 }}" required>
                        <span class="extra-small text-muted">{{ __('Change seat number if passenger requested seat switch.') }}</span>
                        @error('seat_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-8">
                        <label class="form-label fw-bold text-dark">{{ __('Passenger Full Name *') }}</label>
                        <input type="text" name="passenger_name" class="form-control @error('passenger_name') is-invalid @enderror" value="{{ old('passenger_name', $booking->passenger_name) }}" required>
                        @error('passenger_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">{{ __('Phone Number *') }}</label>
                        <input type="text" name="passenger_phone" class="form-control @error('passenger_phone') is-invalid @enderror" value="{{ old('passenger_phone', $booking->passenger_phone) }}" required>
                        @error('passenger_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">{{ __('ID / Passport / Iqama') }}</label>
                        <input type="text" name="passenger_id_number" class="form-control @error('passenger_id_number') is-invalid @enderror" value="{{ old('passenger_id_number', $booking->passenger_id_number) }}">
                        @error('passenger_id_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">{{ __('Gender *') }}</label>
                        <select name="passenger_gender" class="form-select @error('passenger_gender') is-invalid @enderror" required>
                            <option value="male" {{ old('passenger_gender', $booking->passenger_gender) === 'male' ? 'selected' : '' }}>{{ __('Male') }}</option>
                            <option value="female" {{ old('passenger_gender', $booking->passenger_gender) === 'female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                        </select>
                        @error('passenger_gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">{{ __('Booking Status *') }}</label>
                        <select name="booking_status" class="form-select @error('booking_status') is-invalid @enderror" required>
                            <option value="confirmed" {{ old('booking_status', $booking->booking_status) === 'confirmed' ? 'selected' : '' }}>{{ __('Confirmed (Active Seat Lock)') }}</option>
                            <option value="cancelled" {{ old('booking_status', $booking->booking_status) === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled (Frees Seat)') }}</option>
                        </select>
                        @error('booking_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">{{ __('Payment Method *') }}</label>
                        <select name="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                            <option value="cash" {{ old('payment_method', $booking->payment_method) === 'cash' ? 'selected' : '' }}>{{ __('Cash') }}</option>
                            <option value="bank_transfer" {{ old('payment_method', $booking->payment_method) === 'bank_transfer' ? 'selected' : '' }}>{{ __('Bank Transfer') }}</option>
                            <option value="credit_card" {{ old('payment_method', $booking->payment_method) === 'credit_card' ? 'selected' : '' }}>{{ __('Credit Card') }}</option>
                            <option value="online_pos" {{ old('payment_method', $booking->payment_method) === 'online_pos' ? 'selected' : '' }}>{{ __('Mada / POS Machine') }}</option>
                        </select>
                        @error('payment_method') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark"><i class="fas fa-user-tag text-primary me-1"></i> {{ __('Salesman / Agent / البائع أو مسؤول الحجز') }}</label>
                        <input type="text" name="salesman_name" class="form-control" value="{{ old('salesman_name', $booking->effective_salesman) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark"><i class="fas fa-phone-alt text-primary me-1"></i> {{ __('Salesman Phone / هاتف البائع') }}</label>
                        <input type="text" name="salesman_phone" class="form-control" placeholder="{{ __('e.g. +966 50 123 4567') }}" value="{{ old('salesman_phone', $booking->effective_salesman_phone) }}">
                    </div>
                </div>
            </x-card>

            <!-- Package & Hotel Details -->
            <x-card :title="__('2. Package Option & Hotel Accommodation')">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold text-dark">{{ __('Select Package Tier *') }}</label>
                        <div class="d-flex gap-3">
                            <div class="form-check form-check-inline p-2 border rounded-3 w-50">
                                <input class="form-check-input ms-1" type="radio" name="package_type" id="pkgNoHotel" value="without_hotel" {{ old('package_type', $booking->package_type) === 'without_hotel' ? 'checked' : '' }} onchange="toggleHotelFields()">
                                <label class="form-check-label fw-bold text-dark" for="pkgNoHotel">
                                    <i class="fas fa-bus text-primary me-1"></i> {{ __('Fare Only (Without Hotel)') }}
                                </label>
                            </div>
                            <div class="form-check form-check-inline p-2 border rounded-3 w-50">
                                <input class="form-check-input ms-1" type="radio" name="package_type" id="pkgWithHotel" value="with_hotel" {{ old('package_type', $booking->package_type) === 'with_hotel' ? 'checked' : '' }} onchange="toggleHotelFields()">
                                <label class="form-check-label fw-bold text-dark" for="pkgWithHotel">
                                    <i class="fas fa-hotel text-success me-1"></i> {{ __('With Hotel (Full Package)') }}
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5" id="hotelNameGroup">
                        <label class="form-label fw-bold text-dark">{{ __('Hotel Name') }}</label>
                        <input type="text" name="hotel_name" class="form-control" value="{{ old('hotel_name', $booking->hotel_name) }}">
                    </div>

                    <div class="col-md-4" id="roomTypeGroup">
                        <label class="form-label fw-bold text-dark">{{ __('Room Sharing Type') }}</label>
                        <select name="room_type" class="form-select">
                            <option value="quad_sharing" {{ old('room_type', $booking->room_type) === 'quad_sharing' ? 'selected' : '' }}>{{ __('Quad Sharing (4 Beds)') }}</option>
                            <option value="triple_sharing" {{ old('room_type', $booking->room_type) === 'triple_sharing' ? 'selected' : '' }}>{{ __('Triple Sharing (3 Beds)') }}</option>
                            <option value="double_sharing" {{ old('room_type', $booking->room_type) === 'double_sharing' ? 'selected' : '' }}>{{ __('Double Sharing (2 Beds)') }}</option>
                            <option value="single_private" {{ old('room_type', $booking->room_type) === 'single_private' ? 'selected' : '' }}>{{ __('Single Private Room') }}</option>
                        </select>
                    </div>

                    <div class="col-md-3" id="nightsCountGroup">
                        <label class="form-label fw-bold text-dark">{{ __('Hotel Nights') }}</label>
                        <input type="number" name="nights_count" class="form-control" min="1" max="90" value="{{ old('nights_count', $booking->nights_count ?: ($booking->trip?->duration_days ?: 10)) }}">
                    </div>

                    <div class="col-12" id="hotelAddressGroup">
                        <label class="form-label fw-bold text-dark"><i class="fas fa-map-marker-alt text-danger me-1"></i> {{ __('Hotel Address / Location in Makkah / Madinah') }}</label>
                        <input type="text" name="hotel_address" class="form-control" placeholder="{{ __('e.g. Ibrahim Al Khalil Road, Makkah / Near Haram') }}" value="{{ old('hotel_address', $booking->hotel_address ?: $booking->trip?->hotel_address) }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold text-dark">{{ __('Notes / Remarks') }}</label>
                        <textarea name="notes" rows="2" class="form-control">{{ old('notes', $booking->notes) }}</textarea>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Right Column: Financial Calculation -->
        <div class="col-lg-5">
            <x-card :title="__('3. Pricing & Financial Settlement')">
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark">{{ __('Travel Bus Fare *') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light fw-bold">{{ currency_symbol() }}</span>
                        <input type="number" step="0.01" name="travel_fare" id="travelFare" class="form-control font-monospace" value="{{ old('travel_fare', $booking->travel_fare) }}" required oninput="recalcTotals()">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark">{{ __('Hotel / Accommodation Expense') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light fw-bold">{{ currency_symbol() }}</span>
                        <input type="number" step="0.01" name="hotel_expense" id="hotelExpense" class="form-control font-monospace" value="{{ old('hotel_expense', $booking->hotel_expense) }}" oninput="recalcTotals()">
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-bold text-dark extra-small">{{ __('Extra Charges') }}</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light fw-bold">{{ currency_symbol() }}</span>
                            <input type="number" step="0.01" name="extra_charges" id="extraCharges" class="form-control font-monospace form-control-sm" value="{{ old('extra_charges', $booking->extra_charges) }}" oninput="recalcTotals()">
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold text-dark extra-small">{{ __('Discount') }}</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light fw-bold">{{ currency_symbol() }}</span>
                            <input type="number" step="0.01" name="discount" id="discountInput" class="form-control font-monospace form-control-sm" value="{{ old('discount', $booking->discount) }}" oninput="recalcTotals()">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark extra-small">{{ __('Tax / VAT') }}</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light fw-bold">{{ currency_symbol() }}</span>
                        <input type="number" step="0.01" name="tax_amount" id="taxAmount" class="form-control font-monospace form-control-sm" value="{{ old('tax_amount', $booking->tax_amount) }}" oninput="recalcTotals()">
                    </div>
                </div>

                <!-- Live Computed Totals -->
                <div class="p-3 bg-light rounded-3 border mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold text-dark">{{ __('Total Amount') }}:</span>
                        <span class="fs-5 fw-bold text-primary font-monospace" id="displayTotal">
                            {{ currency_symbol() }} {{ number_format($booking->total_amount, 2) }}
                        </span>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold text-dark extra-small mb-1">{{ __('Paid Amount') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-success-subtle text-success fw-bold">{{ currency_symbol() }}</span>
                            <input type="number" step="0.01" name="paid_amount" id="paidAmount" class="form-control font-monospace" value="{{ old('paid_amount', $booking->paid_amount) }}" oninput="recalcTotals()">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <span class="fw-bold text-dark">{{ __('Balance Due') }}:</span>
                        <span class="fs-6 fw-bold font-monospace text-danger" id="displayBalance">
                            {{ currency_symbol() }} {{ number_format($booking->balance_due, 2) }}
                        </span>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary-modern btn-lg">
                        <i class="fas fa-save me-1"></i> {{ __('Save & Update Booking') }}
                    </button>
                    <a href="{{ route('travel.bookings.show', $booking->id) }}" class="btn btn-outline-secondary">
                        {{ __('Cancel & Keep Previous Data') }}
                    </a>
                </div>
            </x-card>
        </div>
    </div>
</form>

@push('scripts')
<script>
function toggleHotelFields() {
    const isWithHotel = document.getElementById('pkgWithHotel').checked;
    const hotelGroup = document.getElementById('hotelNameGroup');
    const roomGroup = document.getElementById('roomTypeGroup');
    const nightsGroup = document.getElementById('nightsCountGroup');
    if (isWithHotel) {
        hotelGroup.style.display = 'block';
        roomGroup.style.display = 'block';
        if (nightsGroup) nightsGroup.style.display = 'block';
    } else {
        hotelGroup.style.display = 'none';
        roomGroup.style.display = 'none';
        if (nightsGroup) nightsGroup.style.display = 'none';
        document.getElementById('hotelExpense').value = 0;
        recalcTotals();
    }
}

function recalcTotals() {
    const fare = parseFloat(document.getElementById('travelFare').value) || 0;
    const hotel = parseFloat(document.getElementById('hotelExpense').value) || 0;
    const extra = parseFloat(document.getElementById('extraCharges').value) || 0;
    const disc = parseFloat(document.getElementById('discountInput').value) || 0;
    const tax = parseFloat(document.getElementById('taxAmount').value) || 0;
    const paid = parseFloat(document.getElementById('paidAmount').value) || 0;

    const total = Math.max(0, fare + hotel + extra - disc + tax);
    const balance = Math.max(0, total - paid);

    document.getElementById('displayTotal').textContent = '{{ currency_symbol() }} ' + total.toFixed(2);
    document.getElementById('displayBalance').textContent = '{{ currency_symbol() }} ' + balance.toFixed(2);
}

document.addEventListener('DOMContentLoaded', function() {
    toggleHotelFields();
});
</script>
@endpush
@endsection
