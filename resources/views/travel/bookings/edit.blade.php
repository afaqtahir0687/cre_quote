@extends('admin.layouts.app')

@section('title', __('Edit Passenger Booking') . ' - ' . $booking->booking_reference)

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
                        <label class="form-label fw-bold text-dark">{{ __('Nationality (الجنسية)') }}</label>
                        <input type="text" name="passenger_nationality" class="form-control @error('passenger_nationality') is-invalid @enderror" value="{{ old('passenger_nationality', $booking->passenger_nationality ?: 'PAK') }}" placeholder="PAK, IND, BAN, SAU, EGY">
                        @error('passenger_nationality') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        @php
                            $isAdmin = auth()->user()->isSuperAdmin() || auth()->user()->isAdmin();
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
                               class="form-control @error('pickup_location') is-invalid @enderror" 
                               value="{{ old('pickup_location', $booking->pickup_location ?: $booking->shop?->name) }}" 
                               placeholder="Office, Batha, Villa 12, Nesto"
                               @if(!$isAdmin) readonly style="background-color: #f8fafc; cursor: not-allowed;" title="{{ __('Only Admin can change boarding location') }}" @endif>
                        @error('pickup_location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        @if(!$isAdmin)
                            <div class="form-text extra-small text-muted">
                                <i class="fas fa-lock me-1 text-secondary"></i>{{ __('Boarding location is locked. Only Admin can edit.') }}
                            </div>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">{{ __('Outbound Drop-off Point / Station (موقع النزول في الذهاب)') }}</label>
                        <input type="text" name="dropoff_location" class="form-control @error('dropoff_location') is-invalid @enderror" value="{{ old('dropoff_location', $booking->dropoff_location) }}" placeholder="Makkah Haram, Kudai, Taif, Hotel">
                        @error('dropoff_location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">{{ __('Return Journey Drop-off / Midway Stop (موقع النزول في العودة)') }}</label>
                        <input type="text" name="return_dropoff_location" id="returnDropoffLocationInput" class="form-control @error('return_dropoff_location') is-invalid @enderror" value="{{ old('return_dropoff_location', $booking->return_dropoff_location) }}" placeholder="{{ __('Select employee point below or type custom return stop') }}">
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
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">{{ __('Journey Type (اتجاه الرحلة) *') }}</label>
                        <select name="journey_type" class="form-select @error('journey_type') is-invalid @enderror" required onchange="document.getElementById('editOneWayDirGroup').style.display = this.value === 'one_way' ? 'block' : 'none'">
                            <option value="round_trip" {{ old('journey_type', $booking->journey_type) === 'round_trip' ? 'selected' : '' }}>{{ __('Two-Way / Round Trip (ذهاب وعودة)') }}</option>
                            <option value="one_way" {{ old('journey_type', $booking->journey_type) === 'one_way' ? 'selected' : '' }}>{{ __('One-Way (ذهاب فقط / اتجاه واحد)') }}</option>
                        </select>
                        @error('journey_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6" id="editOneWayDirGroup" style="{{ old('journey_type', $booking->journey_type) === 'one_way' ? '' : 'display:none;' }}">
                        <label class="form-label fw-bold text-dark">{{ __('One-Way Travel Direction') }}</label>
                        <select name="one_way_direction" class="form-select">
                            <option value="outbound" {{ old('one_way_direction', $booking->one_way_direction) === 'outbound' ? 'selected' : '' }}>{{ __('Outbound (ذهاب): Departure → Destination') }}</option>
                            <option value="return" {{ old('one_way_direction', $booking->one_way_direction) === 'return' ? 'selected' : '' }}>{{ __('Inbound (عودة): Destination → Departure') }}</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">{{ __('Booking Status *') }}</label>
                        <select name="booking_status" class="form-select @error('booking_status') is-invalid @enderror" required>
                            <option value="confirmed" {{ old('booking_status', $booking->booking_status) === 'confirmed' ? 'selected' : '' }}>{{ __('Confirmed (Active Seat Lock)') }}</option>
                            <option value="cancelled" {{ old('booking_status', $booking->booking_status) === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled (Frees Seat)') }}</option>
                        </select>
                        @error('booking_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin())
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark"><i class="fas fa-user-tag text-primary me-1"></i> {{ __('Assign Employee / Agent (مسؤول الحجز)') }}</label>
                            <select name="employee_id" id="editEmployeeSelect" class="form-select @error('employee_id') is-invalid @enderror" onchange="onEditEmployeeChanged(this)">
                                <option value="" data-name="{{ auth()->user()?->name }}" data-phone="{{ auth()->user()?->phone }}">-- {{ __('Admin / Central Office') }} --</option>
                                @if(isset($employees))
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}" 
                                                data-name="{{ $emp->name }}" 
                                                data-phone="{{ $emp->phone }}"
                                                {{ (string)old('employee_id', $booking->employee_id ?? '') === (string)$emp->id ? 'selected' : '' }}>
                                            {{ $emp->name }} ({{ $emp->designation ?? 'Staff' }})
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark"><i class="fas fa-user-edit text-primary me-1"></i> {{ __('Salesman / Agent Name') }}</label>
                            <input type="text" name="salesman_name" id="editSalesmanNameInput" class="form-control" value="{{ old('salesman_name', $booking->effective_salesman) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark"><i class="fas fa-phone-alt text-primary me-1"></i> {{ __('Salesman Phone / هاتف البائع') }}</label>
                            <input type="text" name="salesman_phone" id="editSalesmanPhoneInput" class="form-control" placeholder="{{ __('+966 50 123 4567') }}" value="{{ old('salesman_phone', $booking->effective_salesman_phone) }}">
                        </div>
                    @else
                        <input type="hidden" name="employee_id" value="{{ $booking->employee_id }}">
                        <input type="hidden" name="salesman_name" value="{{ $booking->salesman_name }}">
                        <input type="hidden" name="salesman_phone" value="{{ $booking->salesman_phone }}">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted"><i class="fas fa-user-tag text-secondary me-1"></i> {{ __('Salesman / Agent (مسؤول الحجز)') }}</label>
                            <input type="text" class="form-control bg-light text-muted" value="{{ $booking->effective_salesman }}" readonly disabled>
                        </div>
                    @endif
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

                    <div class="col-md-6" id="hotelNameGroup">
                        <label class="form-label fw-bold text-dark small mb-1"><i class="fas fa-hotel text-primary me-1"></i>{{ __('Hotel Name') }}</label>
                        <input type="text" name="hotel_name" class="form-control" value="{{ old('hotel_name', $booking->hotel_name) }}">
                    </div>

                    <div class="col-md-6" id="hotelAddressGroup">
                        <label class="form-label fw-bold text-dark small mb-1"><i class="fas fa-map-marker-alt text-danger me-1"></i> {{ __('Hotel Address / Location in Makkah / Madinah') }}</label>
                        <input type="text" name="hotel_address" class="form-control" placeholder="{{ __('Ibrahim Al Khalil Road, Makkah / Near Haram') }}" value="{{ old('hotel_address', $booking->hotel_address ?: $booking->trip?->hotel_address) }}">
                    </div>

                    <div class="col-md-5" id="roomTypeGroup">
                        <label class="form-label fw-bold text-dark small mb-1"><i class="fas fa-bed text-primary me-1"></i>{{ __('Room Category / Type') }}</label>
                        <select name="room_type" id="editRoomTypeSelect" class="form-select" onchange="onEditRoomTypeChanged()">
                            <optgroup label="{{ __('Standard Shared Rooms (مشترك)') }}">
                                <option value="quad_sharing" data-surcharge="0" {{ old('room_type', $booking->room_type) === 'quad_sharing' ? 'selected' : '' }}>{{ __('Quad Sharing (رباعي - 4 Beds)') }}</option>
                                <option value="triple_sharing" data-surcharge="100" {{ old('room_type', $booking->room_type) === 'triple_sharing' ? 'selected' : '' }}>{{ __('Triple Sharing (ثلاثي - 3 Beds)') }} (+100 SAR)</option>
                                <option value="double_sharing" data-surcharge="200" {{ old('room_type', $booking->room_type) === 'double_sharing' ? 'selected' : '' }}>{{ __('Double Sharing (ثنائي - 2 Beds)') }} (+200 SAR)</option>
                            </optgroup>
                            <optgroup label="{{ __('Personal & Private Rooms (خاص / شخصي)') }}">
                                <option value="single_private" data-surcharge="500" {{ old('room_type', $booking->room_type) === 'single_private' ? 'selected' : '' }}>{{ __('Personal / Single Private Room (شخصي / مفرد)') }} (+500 SAR)</option>
                                <option value="private_room" data-surcharge="600" {{ old('room_type', $booking->room_type) === 'private_room' ? 'selected' : '' }}>{{ __('Private Family Room (غرفة عائلية خاصة)') }} (+600 SAR)</option>
                            </optgroup>
                            <optgroup label="{{ __('Luxury & VIP Suites (أجنحة وغرف فاخرة)') }}">
                                <option value="luxury_haram_view" data-surcharge="800" {{ old('room_type', $booking->room_type) === 'luxury_haram_view' ? 'selected' : '' }}>👑 {{ __('Luxury Haram View (إطلالة الحرم فاخرة)') }} (+800 SAR)</option>
                                <option value="executive_suite" data-surcharge="1200" {{ old('room_type', $booking->room_type) === 'executive_suite' ? 'selected' : '' }}>⭐ {{ __('Executive Luxury Suite (جناح تنفيذي فاخر)') }} (+1,200 SAR)</option>
                            </optgroup>
                            <optgroup label="{{ __('Custom / Manual (مخصص / يدوي)') }}">
                                <option value="custom" data-surcharge="0" {{ old('room_type', $booking->room_type) === 'custom' ? 'selected' : '' }}>✏️ {{ __('Custom Room / Custom Price (غرفة مخصصة / سعر يدوي)') }}</option>
                            </optgroup>
                        </select>
                    </div>

                    <div class="col-md-4" id="roomPriceGroup">
                        <label class="form-label fw-bold text-dark text-success small mb-1"><i class="fas fa-tag me-1"></i>{{ __('Room / Hotel Price') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-success-subtle text-success fw-bold">{{ currency_symbol() }}</span>
                            <input type="number" step="0.01" min="0" id="editRoomPriceInput" class="form-control font-monospace fw-bold text-success" value="{{ old('hotel_expense', $booking->hotel_expense) }}" oninput="syncEditHotelExpense(this.value)">
                        </div>
                    </div>

                    <div class="col-md-3" id="nightsCountGroup">
                        <label class="form-label fw-bold text-dark small mb-1"><i class="fas fa-moon text-warning me-1"></i>{{ __('Hotel Nights') }}</label>
                        <div class="input-group">
                            <input type="number" name="nights_count" class="form-control font-monospace" min="1" max="90" value="{{ old('nights_count', $booking->nights_count ?: ($booking->trip?->duration_days ?: 10)) }}">
                            <span class="input-group-text bg-white text-muted small">{{ __('Nights') }}</span>
                        </div>
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
                        <input type="number" step="0.01" name="hotel_expense" id="hotelExpense" class="form-control font-monospace" value="{{ old('hotel_expense', $booking->hotel_expense) }}" oninput="recalcTotals(); const topP = document.getElementById('editRoomPriceInput'); if (topP && !topP.matches(':focus')) topP.value = this.value;">
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

                    <div class="row g-2 pt-2 border-top mb-2">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-bold text-dark extra-small mb-0">{{ __('Paid Amount') }} *</label>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2" style="font-size: 0.70rem;" onclick="setPaidZero()">{{ __('0 / Unpaid') }}</button>
                                    <button type="button" class="btn btn-xs btn-outline-success py-0 px-2" style="font-size: 0.70rem;" onclick="setPaidFull()">{{ __('Full Paid') }}</button>
                                </div>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-success-subtle text-success fw-bold">{{ currency_symbol() }}</span>
                                <input type="number" step="0.01" min="0" name="paid_amount" id="paidAmount" class="form-control font-monospace fw-bold text-success" value="{{ old('paid_amount', $booking->paid_amount) }}" oninput="recalcTotals()">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark extra-small mb-1">{{ __('Payment Method') }} *</label>
                            @php
                                $curPaymentMethod = old('payment_method', $booking->payment_method ?? 'cash');
                            @endphp
                            <select name="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                                <option value="cash" {{ in_array($curPaymentMethod, ['cash', 'Cash', 'نقدي']) ? 'selected' : '' }}>{{ __('Cash at Counter (نقدي)') }}</option>
                                <option value="bank_transfer" {{ in_array($curPaymentMethod, ['bank_transfer', 'Bank Transfer', 'تحويل بنكي']) ? 'selected' : '' }}>{{ __('Bank Transfer (تحويل بنكي)') }}</option>
                                <option value="card" {{ in_array($curPaymentMethod, ['card', 'credit_card', 'Credit Card', 'Credit / Debit Card', 'شبكة']) ? 'selected' : '' }}>{{ __('Credit / Debit Card (شبكة)') }}</option>
                                <option value="online" {{ in_array($curPaymentMethod, ['online', 'online_pos', 'mada', 'Online Payment', 'إلكتروني']) ? 'selected' : '' }}>{{ __('Online Payment (إلكتروني)') }}</option>
                            </select>
                            @error('payment_method') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <span class="fw-bold text-dark">{{ __('Balance Due') }}:</span>
                        <span class="fs-6 fw-bold font-monospace text-danger" id="displayBalance">
                            {{ currency_symbol() }} {{ number_format($booking->balance_due, 2) }}
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <span class="fw-bold text-dark">{{ __('Payment Status (الحالة)') }}:</span>
                        <span id="displayStatusBadge" class="badge px-3 py-1 fw-bold fs-7 {{ $booking->payment_status === 'paid' ? 'bg-success-subtle text-success border border-success-subtle' : ($booking->payment_status === 'partial' ? 'bg-warning-subtle text-warning-emphasis border border-warning-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle') }}">
                            {{ $booking->payment_status === 'paid' ? __('PAID / مدفوع بالكامل') : ($booking->payment_status === 'partial' ? __('PARTIAL / مدفوع جزئياً') : __('UNPAID / غير مدفوع')) }}
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

function setPaidZero() {
    const paid = document.getElementById('paidAmount');
    if (paid) {
        paid.value = '0.00';
        recalcTotals();
    }
}

function setPaidFull() {
    const paid = document.getElementById('paidAmount');
    if (paid) {
        const fare = parseFloat(document.getElementById('travelFare').value) || 0;
        const hotel = parseFloat(document.getElementById('hotelExpense').value) || 0;
        const extra = parseFloat(document.getElementById('extraCharges').value) || 0;
        const disc = parseFloat(document.getElementById('discountInput').value) || 0;
        const tax = parseFloat(document.getElementById('taxAmount').value) || 0;
        const total = Math.max(0, fare + hotel + extra - disc + tax);
        paid.value = total.toFixed(2);
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

    let statusLabel = '{{ __("UNPAID / غير مدفوع") }}';
    let statusClass = 'bg-danger-subtle text-danger border border-danger-subtle';

    if (total > 0 && paid >= total) {
        statusLabel = '{{ __("PAID / مدفوع بالكامل") }}';
        statusClass = 'bg-success-subtle text-success border border-success-subtle';
    } else if (paid > 0 && paid < total) {
        statusLabel = '{{ __("PARTIAL / مدفوع جزئياً") }}';
        statusClass = 'bg-warning-subtle text-warning-emphasis border border-warning-subtle';
    }

    const badge = document.getElementById('displayStatusBadge');
    if (badge) {
        badge.className = 'badge px-3 py-1 fw-bold fs-7 ' + statusClass;
        badge.innerText = statusLabel;
    }
}

function syncEditHotelExpense(val) {
    const mainHotel = document.getElementById('hotelExpense');
    if (mainHotel) {
        mainHotel.value = val;
        recalcTotals();
    }
}

function onEditRoomTypeChanged() {
    const sel = document.getElementById('editRoomTypeSelect');
    if (!sel) return;
    const opt = sel.options[sel.selectedIndex];
    const pInput = document.getElementById('editRoomPriceInput');
    const mainHotel = document.getElementById('hotelExpense');

    if (sel.value === 'custom') {
        if (pInput) {
            pInput.focus();
            pInput.select();
        }
    } else {
        const surcharge = opt ? (parseFloat(opt.dataset.surcharge) || 0) : 0;
        const tripHotelDiff = parseFloat(@json(max(0, ($booking->trip?->price_with_hotel ?? 0) - ($booking->trip?->price_without_hotel ?? 0)))) || 0;
        const calculated = tripHotelDiff + surcharge;
        if (pInput) pInput.value = calculated.toFixed(2);
        if (mainHotel) mainHotel.value = calculated.toFixed(2);
        recalcTotals();
    }
}

function onEditEmployeeChanged(sel) {
    const opt = sel.options[sel.selectedIndex];
    if (opt) {
        const name = opt.getAttribute('data-name');
        const phone = opt.getAttribute('data-phone');
        const nameInput = document.getElementById('editSalesmanNameInput');
        const phoneInput = document.getElementById('editSalesmanPhoneInput');
        if (name && nameInput) nameInput.value = name;
        if (phone !== null && phone !== undefined && phoneInput) phoneInput.value = phone;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    toggleHotelFields();

    // Initialize Searchable Filter Dropdowns
    ['editEmployeeSelect', 'editRoomTypeSelect'].forEach(function(id) {
        const el = document.getElementById(id);
        if (el) initSearchableFilterSelect(el);
    });
    document.querySelectorAll('#editBookingForm select').forEach(function(el) {
        initSearchableFilterSelect(el);
    });
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
            searchStr: (opt.text + ' ' + opt.value + ' ' + (opt.dataset.name || '') + ' ' + (opt.dataset.phone || '')).toLowerCase()
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

    buildOptions();
    return wrapper;
}
</script>
@endpush
@endsection
