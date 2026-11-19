@extends('admin.layouts.app')

@section('title', __('Edit Trip') . ' - ' . $trip->trip_code)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        <a href="{{ route('travel.trips.index') }}" class="btn btn-light btn-sm rounded-circle me-3 shadow-sm">
            <i class="fas fa-arrow-left text-muted"></i>
        </a>
        <div class="d-flex align-items-center">
            <h2 class="fw-bold text-dark mb-0">{{ __('Edit Trip') }}</h2>
            <span class="badge-soft badge-soft-primary ms-3">{{ $trip->trip_code }}</span>
            <span class="badge {{ $trip->status === 'upcoming' ? 'bg-info' : ($trip->status === 'completed' ? 'bg-success' : 'bg-warning text-dark') }} ms-2">
                {{ __(ucfirst($trip->status)) }}
            </span>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('travel.trips.show', $trip->id) }}" class="btn btn-outline-primary d-flex align-items-center shadow-sm">
            <i class="fas fa-th me-2"></i> {{ __('View Seat Layout') }}
        </a>
    </div>
</div>

<form action="{{ route('travel.trips.update', $trip->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <!-- Left: Trip Route & Bus Specification -->
        <div class="col-lg-8">
            <x-card :title="__('1. Trip Details & Bus Configuration')">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">{{ __('Trip Code') }} *</label>
                        <input type="text" class="form-control" value="{{ $trip->trip_code }}" readonly style="background-color: #f8fafc;">
                    </div>

                    <div class="col-md-8">
                        <label class="form-label fw-bold text-dark">{{ __('Trip Title / Route Headline') }} *</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $trip->title) }}" required>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">{{ __('Bus Fleet Name / Model') }} *</label>
                        <input type="text" name="bus_name" class="form-control @error('bus_name') is-invalid @enderror" value="{{ old('bus_name', $trip->bus_name) }}" required>
                        @error('bus_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark">{{ __('Bus Plate / Reg #') }}</label>
                        <input type="text" name="bus_number" class="form-control @error('bus_number') is-invalid @enderror" value="{{ old('bus_number', $trip->bus_number) }}">
                        @error('bus_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark">{{ __('Total Seats') }} *</label>
                        <input type="number" name="total_seats" class="form-control @error('total_seats') is-invalid @enderror" value="{{ old('total_seats', $trip->total_seats) }}" min="{{ max(10, $trip->bookedSeatsCount()) }}" max="100" required>
                        @error('total_seats') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text extra-small">{{ __('Already booked:') }} {{ $trip->bookedSeatsCount() }} {{ __('seats') }}</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">{{ __('Departure City') }} *</label>
                        <input type="text" name="departure_city" class="form-control @error('departure_city') is-invalid @enderror" value="{{ old('departure_city', $trip->departure_city) }}" required>
                        @error('departure_city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">{{ __('Destination City') }} *</label>
                        <input type="text" name="destination_city" class="form-control @error('destination_city') is-invalid @enderror" value="{{ old('destination_city', $trip->destination_city) }}" required>
                        @error('destination_city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">{{ __('Departure Date & Time') }} *</label>
                        <input type="datetime-local" name="departure_date" class="form-control @error('departure_date') is-invalid @enderror" value="{{ old('departure_date', $trip->departure_date ? $trip->departure_date->format('Y-m-d\TH:i') : '') }}" required>
                        @error('departure_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">{{ __('Return Date') }}</label>
                        <input type="datetime-local" name="return_date" class="form-control @error('return_date') is-invalid @enderror" value="{{ old('return_date', $trip->return_date ? $trip->return_date->format('Y-m-d\TH:i') : '') }}">
                        @error('return_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">{{ __('Duration (Days)') }} *</label>
                        <input type="number" name="duration_days" class="form-control @error('duration_days') is-invalid @enderror" value="{{ old('duration_days', $trip->duration_days) }}" min="1" max="90" required>
                        @error('duration_days') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold text-dark">{{ __('Trip Overview / Description') }}</label>
                        <textarea name="description" rows="2" class="form-control">{{ old('description', $trip->description) }}</textarea>
                    </div>
                </div>
            </x-card>

            <!-- Hotel & Accommodation Details -->
            <x-card :title="__('2. Hotel & Accommodation Partner (Optional)')" class="mt-4">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-bold text-dark">{{ __('Hotel Name in Makkah / Madinah') }}</label>
                        <input type="text" name="hotel_name" class="form-control @error('hotel_name') is-invalid @enderror" value="{{ old('hotel_name', $trip->hotel_name) }}">
                        @error('hotel_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">{{ __('Hotel Star Rating') }}</label>
                        <select name="hotel_rating" class="form-select">
                            <option value="">{{ __('Select Star Rating') }}</option>
                            <option value="5-Star" {{ old('hotel_rating', $trip->hotel_rating) === '5-Star' ? 'selected' : '' }}>⭐⭐⭐⭐⭐ 5-Star</option>
                            <option value="4-Star" {{ old('hotel_rating', $trip->hotel_rating) === '4-Star' ? 'selected' : '' }}>⭐⭐⭐⭐ 4-Star</option>
                            <option value="3-Star" {{ old('hotel_rating', $trip->hotel_rating) === '3-Star' ? 'selected' : '' }}>⭐⭐⭐ 3-Star</option>
                            <option value="Economy" {{ old('hotel_rating', $trip->hotel_rating) === 'Economy' ? 'selected' : '' }}>Economy Standard</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold text-dark">{{ __('Hotel Address / Location') }}</label>
                        <input type="text" name="hotel_address" class="form-control @error('hotel_address') is-invalid @enderror" placeholder="{{ __('e.g. Ibrahim Al Khalil Road, Makkah / Near Haram') }}" value="{{ old('hotel_address', $trip->hotel_address) }}">
                        @error('hotel_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Right: Pricing Packages & Status -->
        <div class="col-lg-4">
            <x-card :title="__('3. Package Pricing & Fares')">
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark">{{ __('Seat Fare Only (Without Hotel) *') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light fw-bold">{{ currency_symbol() }}</span>
                        <input type="number" step="0.01" name="price_without_hotel" class="form-control @error('price_without_hotel') is-invalid @enderror" value="{{ old('price_without_hotel', $trip->price_without_hotel) }}" required>
                    </div>
                    @error('price_without_hotel') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-success">{{ __('Complete Package (With Hotel) *') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-success-subtle text-success fw-bold">{{ currency_symbol() }}</span>
                        <input type="number" step="0.01" name="price_with_hotel" class="form-control @error('price_with_hotel') is-invalid @enderror" value="{{ old('price_with_hotel', $trip->price_with_hotel) }}" required>
                    </div>
                    @error('price_with_hotel') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark">{{ __('Trip Status') }} *</label>
                    <select name="status" class="form-select" required>
                        <option value="upcoming" {{ old('status', $trip->status) === 'upcoming' ? 'selected' : '' }}>{{ __('Upcoming (Open for Booking)') }}</option>
                        <option value="ongoing" {{ old('status', $trip->status) === 'ongoing' ? 'selected' : '' }}>{{ __('Ongoing (On The Road)') }}</option>
                        <option value="completed" {{ old('status', $trip->status) === 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                        <option value="cancelled" {{ old('status', $trip->status) === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark">{{ __('Internal Notes') }}</label>
                    <textarea name="notes" rows="3" class="form-control">{{ old('notes', $trip->notes) }}</textarea>
                </div>

                <div class="mt-4 pt-2 border-top d-flex justify-content-end gap-2">
                    <a href="{{ route('travel.trips.show', $trip->id) }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary-modern flex-grow-1">
                        <i class="fas fa-save me-1"></i> {{ __('Update Trip') }}
                    </button>
                </div>
            </x-card>
        </div>
    </div>
</form>
@endsection
