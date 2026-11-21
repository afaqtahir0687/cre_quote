@extends('admin.layouts.app')

@section('title', __('Create New Umrah / Bus Trip'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        <a href="{{ route('travel.trips.index') }}" class="btn btn-light btn-sm rounded-circle me-3 shadow-sm">
            <i class="fas fa-arrow-left text-muted"></i>
        </a>
        <div>
            <h2 class="fw-bold text-dark mb-0">{{ __('Create Umrah & Bus Trip') }}</h2>
            <p class="text-muted small mb-0">{{ __('Configure a new travel route, assign luxury coach bus with 49 seats, and set pricing packages.') }}</p>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <div class="d-flex align-items-center bg-white px-3 py-2 rounded-3 shadow-sm border">
            <span class="text-muted me-2 small fw-medium">{{ __('Trip Code:') }}</span>
            <span class="fw-bold text-primary font-monospace">{{ $tripCode }}</span>
        </div>
    </div>
</div>

<form action="{{ route('travel.trips.store') }}" method="POST">
    @csrf

    <div class="row g-4">
        <!-- Left: Trip Route & Bus Specification -->
        <div class="col-lg-8">
            <x-card :title="__('1. Trip Details & Bus Configuration')">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">{{ __('Trip Code') }} *</label>
                        <input type="text" name="trip_code" class="form-control @error('trip_code') is-invalid @enderror" value="{{ old('trip_code', $tripCode) }}" required readonly style="background-color: #f8fafc;">
                        @error('trip_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-8">
                        <label class="form-label fw-bold text-dark">{{ __('Trip Title / Route Headline') }} *</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" placeholder="{{ __('e.g. Makkah & Madinah 10-Days Executive Umrah') }}" value="{{ old('title') }}" required>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">{{ __('Bus Fleet Name / Model') }} *</label>
                        <input type="text" name="bus_name" class="form-control @error('bus_name') is-invalid @enderror" placeholder="{{ __('e.g. VIP Luxury Coach Bus') }}" value="{{ old('bus_name', 'VIP Luxury Coach Bus') }}" required>
                        @error('bus_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark">{{ __('Bus Plate / Reg #') }}</label>
                        <input type="text" name="bus_number" class="form-control @error('bus_number') is-invalid @enderror" placeholder="{{ __('e.g. B-4910') }}" value="{{ old('bus_number') }}">
                        @error('bus_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark">{{ __('Total Seats') }} *</label>
                        <input type="number" name="total_seats" class="form-control @error('total_seats') is-invalid @enderror" value="{{ old('total_seats', 49) }}" min="10" max="100" required>
                        @error('total_seats') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text extra-small">{{ __('Standard passenger coach has 49 seats.') }}</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">{{ __('Departure City') }} *</label>
                        <input type="text" name="departure_city" class="form-control @error('departure_city') is-invalid @enderror" placeholder="{{ __('e.g. Riyadh, Jeddah, Dammam, Lahore') }}" value="{{ old('departure_city') }}" required>
                        @error('departure_city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">{{ __('Destination City') }} *</label>
                        <input type="text" name="destination_city" class="form-control @error('destination_city') is-invalid @enderror" placeholder="{{ __('e.g. Makkah Al-Mukarramah, Madinah') }}" value="{{ old('destination_city', 'Makkah Al-Mukarramah') }}" required>
                        @error('destination_city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">{{ __('Departure Date & Time') }} *</label>
                        <input type="datetime-local" name="departure_date" class="form-control @error('departure_date') is-invalid @enderror" value="{{ old('departure_date') }}" required id="depDateInput">
                        @error('departure_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">{{ __('Return Date') }}</label>
                        <input type="datetime-local" name="return_date" class="form-control @error('return_date') is-invalid @enderror" value="{{ old('return_date') }}" id="retDateInput">
                        @error('return_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">{{ __('Duration (Days)') }} *</label>
                        <input type="number" name="duration_days" class="form-control @error('duration_days') is-invalid @enderror" value="{{ old('duration_days', 10) }}" min="1" max="90" required id="durationInput">
                        @error('duration_days') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold text-dark">{{ __('Trip Overview / Description') }}</label>
                        <textarea name="description" rows="2" class="form-control" placeholder="{{ __('Brief itinerary points, ziyarat stops, gathering points...') }}">{{ old('description') }}</textarea>
                    </div>
                </div>
            </x-card>

            <!-- Driver & Crew Details (For Manifest Statement) -->
            <x-card :title="__('2. Driver & Crew Details (For Manifest Statement / بيان السائقين)')" class="mt-4">
                <div class="row g-3">
                    <div class="col-12">
                        <span class="badge bg-primary text-white mb-2">{{ __('Primary Driver 1 (السائق الأول)') }}</span>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-bold text-dark small">{{ __('Driver 1 Full Name') }}</label>
                        <input type="text" name="driver_1_name" class="form-control" placeholder="{{ __('e.g. Adnan Khan') }}" value="{{ old('driver_1_name') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark small">{{ __('Nationality / Country') }}</label>
                        <input type="text" name="driver_1_nationality" class="form-control" placeholder="{{ __('e.g. PAK, SAU, IND, EGY') }}" value="{{ old('driver_1_nationality', 'PAK') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small">{{ __('Phone / Iqama Number') }}</label>
                        <input type="text" name="driver_1_phone" class="form-control" placeholder="{{ __('e.g. 2580796130 / 0501234567') }}" value="{{ old('driver_1_phone') }}">
                    </div>

                    <div class="col-12 mt-3 pt-2 border-top">
                        <span class="badge bg-secondary text-white mb-2">{{ __('Co-Driver 2 (السائق الثاني - اختياري)') }}</span>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-bold text-dark small">{{ __('Driver 2 Full Name') }}</label>
                        <input type="text" name="driver_2_name" class="form-control" placeholder="{{ __('e.g. Showkat') }}" value="{{ old('driver_2_name') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark small">{{ __('Nationality / Country') }}</label>
                        <input type="text" name="driver_2_nationality" class="form-control" placeholder="{{ __('e.g. PAK, SAU, IND, EGY') }}" value="{{ old('driver_2_nationality', 'PAK') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small">{{ __('Phone / Iqama Number') }}</label>
                        <input type="text" name="driver_2_phone" class="form-control" placeholder="{{ __('e.g. 2620061750 / 0509876543') }}" value="{{ old('driver_2_phone') }}">
                    </div>
                </div>
            </x-card>

            <!-- Hotel & Accommodation Details -->
            <x-card :title="__('3. Hotel & Accommodation Partner (Optional)')" class="mt-4">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-bold text-dark">{{ __('Hotel Name in Makkah / Madinah') }}</label>
                        <input type="text" name="hotel_name" class="form-control @error('hotel_name') is-invalid @enderror" placeholder="{{ __('e.g. Pullman Zamzam / Swissôtel / Dar Al Tawhid') }}" value="{{ old('hotel_name') }}">
                        @error('hotel_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">{{ __('Hotel Star Rating') }}</label>
                        <select name="hotel_rating" class="form-select">
                            <option value="">{{ __('Select Star Rating') }}</option>
                            <option value="5-Star" {{ old('hotel_rating') === '5-Star' ? 'selected' : '' }}>⭐⭐⭐⭐⭐ 5-Star</option>
                            <option value="4-Star" {{ old('hotel_rating') === '4-Star' ? 'selected' : '' }}>⭐⭐⭐⭐ 4-Star</option>
                            <option value="3-Star" {{ old('hotel_rating') === '3-Star' ? 'selected' : '' }}>⭐⭐⭐ 3-Star</option>
                            <option value="Economy" {{ old('hotel_rating') === 'Economy' ? 'selected' : '' }}>Economy Standard</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold text-dark">{{ __('Hotel Address / Location') }}</label>
                        <input type="text" name="hotel_address" class="form-control @error('hotel_address') is-invalid @enderror" placeholder="{{ __('e.g. Ibrahim Al Khalil Road, Makkah / Near Haram') }}" value="{{ old('hotel_address') }}">
                        @error('hotel_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Right: Pricing Packages & Status -->
        <div class="col-lg-4">
            <x-card :title="__('3. Package Pricing & Fares')">
                <p class="text-muted small">
                    {{ __('Set rates for travel seat fare alone vs complete package with hotel accommodations.') }}
                </p>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark">{{ __('Seat Fare Only (Without Hotel) *') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light fw-bold">{{ currency_symbol() }}</span>
                        <input type="number" step="0.01" name="price_without_hotel" class="form-control @error('price_without_hotel') is-invalid @enderror" placeholder="0.00" value="{{ old('price_without_hotel', '250.00') }}" required>
                    </div>
                    @error('price_without_hotel') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    <div class="form-text extra-small">{{ __('Bus transportation ticket price per passenger.') }}</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-success">{{ __('Complete Package (With Hotel) *') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-success-subtle text-success fw-bold">{{ currency_symbol() }}</span>
                        <input type="number" step="0.01" name="price_with_hotel" class="form-control @error('price_with_hotel') is-invalid @enderror" placeholder="0.00" value="{{ old('price_with_hotel', '1250.00') }}" required>
                    </div>
                    @error('price_with_hotel') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    <div class="form-text extra-small">{{ __('Includes seat ticket + hotel stay.') }}</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark">{{ __('Trip Status') }} *</label>
                    <select name="status" class="form-select" required>
                        <option value="upcoming" {{ old('status', 'upcoming') === 'upcoming' ? 'selected' : '' }}>{{ __('Upcoming (Open for Booking)') }}</option>
                        <option value="ongoing" {{ old('status') === 'ongoing' ? 'selected' : '' }}>{{ __('Ongoing (On The Road)') }}</option>
                        <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                        <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark">{{ __('Internal Notes') }}</label>
                    <textarea name="notes" rows="3" class="form-control" placeholder="{{ __('Private remarks, driver contact, permits...') }}">{{ old('notes') }}</textarea>
                </div>

                <div class="mt-4 pt-2 border-top d-flex justify-content-end gap-2">
                    <a href="{{ route('travel.trips.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary-modern flex-grow-1">
                        <i class="fas fa-check-circle me-1"></i> {{ __('Save & Configure Seats') }}
                    </button>
                </div>
            </x-card>
        </div>
    </div>
</form>
@endsection
