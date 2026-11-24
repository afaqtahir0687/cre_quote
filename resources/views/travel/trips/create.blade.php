@extends('admin.layouts.app')

@section('title', __('Create New Umrah / Bus Trip'))

@section('styles')
<style>
    /* Filterable Search Dropdowns (Like Quotes / Job Orders) */
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
    .filter-trigger:focus, .filter-trigger.active {
        box-shadow: 0 0 0 0.25rem rgba(13,110,253,.2) !important;
        border-color: #86b7fe !important;
    }
    .filter-panel {
        display: none; position: absolute; top: calc(100% + 4px); left: 0; right: 0;
        z-index: 1055; background: #fff; border: 1px solid #dee2e6;
        border-radius: 0.5rem; box-shadow: 0 8px 24px rgba(0,0,0,.12); overflow: hidden;
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
    .filter-search:focus { border-color: #86b7fe; box-shadow: 0 0 0 3px rgba(13,110,253,.15); }
    .filter-list { max-height: 230px; overflow-y: auto; padding: 4px 0; }
    .filter-list::-webkit-scrollbar { width: 5px; }
    .filter-list::-webkit-scrollbar-thumb { background: #dee2e6; border-radius: 4px; }
    .filter-option {
        padding: 8px 14px; font-size: .88rem; cursor: pointer;
        border-left: 3px solid transparent; transition: background .12s, border-color .12s;
        display: flex; justify-content: space-between; align-items: center;
    }
    .filter-option:hover, .filter-option.focused { background: #f0f4ff; border-left-color: #0d6efd; }
    .filter-option.selected { background: #e7f0ff; border-left-color: #0d6efd; font-weight: 600; }
    .filter-option.selected .check-icon { display: inline-block; color: #0d6efd; }
    .filter-option .check-icon { display: none; }
    .filter-option.custom-opt {
        border-top: 1px dashed #dee2e6;
        background: #fafbfc;
        color: #0d6efd;
        font-weight: 600;
        margin-top: 4px;
    }
    .filter-option.custom-opt:hover { background: #f0f4ff; }
    .filter-empty { padding: 14px; text-align: center; color: #6c757d; font-size: .875rem; }
</style>
@endsection

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
                    {{-- 1. Trip Code --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark d-flex justify-content-between align-items-center">
                            <span>{{ __('Trip Code') }} <span class="text-danger">*</span></span>
                            <small class="text-muted fw-normal">{{ __('Auto Reference') }}</small>
                        </label>
                        <input type="text" name="trip_code" class="form-control font-monospace fw-bold @error('trip_code') is-invalid @enderror" value="{{ old('trip_code', $tripCode) }}" required readonly style="background-color: #f8fafc; color: #1e3a8a;">
                        @error('trip_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text extra-small">{{ __('Unique system trip tracking identifier.') }}</div>
                    </div>

                    {{-- 2. Duration (Days) Dropdown (Searchable Filter List) --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark d-flex justify-content-between align-items-center">
                            <span>{{ __('Duration (Days)') }} <span class="text-danger">*</span></span>
                            <span class="badge bg-light text-primary border" id="durationBadge">{{ old('duration_days', 3) }} {{ __('Days') }}</span>
                        </label>
                        @php
                            $selectedDuration = (int) old('duration_days', 3);
                            $isCustomDuration = !array_key_exists($selectedDuration, $durations);
                            $durationTriggerText = $isCustomDuration 
                                ? __('Custom (:days Days)', ['days' => $selectedDuration]) 
                                : ($durations[$selectedDuration] ?? ($selectedDuration . ' Days'));
                        @endphp
                        <div class="filter-dropdown" id="duration-filter-dropdown">
                            <input type="text" id="durationTrigger" class="form-control filter-trigger @error('duration_days') is-invalid @enderror" readonly value="{{ $durationTriggerText }}" placeholder="{{ __('Select Duration (Days) *') }}">
                            <div class="filter-panel" id="durationFilterPanel">
                                <div class="filter-search-wrap">
                                    <input type="text" class="filter-search" id="durationSearchInput" placeholder="{{ __('Search duration (10, 20, week, month)...') }}">
                                </div>
                                <div class="filter-list" id="durationFilterList">
                                    @foreach($durations as $dVal => $dLabel)
                                        @php $isSel = (!$isCustomDuration && $selectedDuration == $dVal); @endphp
                                        <div class="filter-option {{ $isSel ? 'selected' : '' }}" data-value="{{ $dVal }}" data-label="{{ $dLabel }}">
                                            <span class="opt-name">{{ $dLabel }}</span>
                                            <i class="fas fa-check check-icon"></i>
                                        </div>
                                    @endforeach
                                    <div class="filter-option custom-opt {{ $isCustomDuration ? 'selected' : '' }}" data-value="__custom__" data-label="{{ __('Custom Days...') }}">
                                        <span><i class="fas fa-plus-circle me-1"></i>{{ __('Custom / Other Days (أيام أخرى)...') }}</span>
                                        <i class="fas fa-edit text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="number" name="duration_days" id="durationInput" class="form-control mt-2 {{ $isCustomDuration ? '' : 'd-none' }} @error('duration_days') is-invalid @enderror" value="{{ $selectedDuration }}" min="1" max="90" placeholder="{{ __('Enter number of days (1-90)') }}" required oninput="syncDurationBadge(this.value); autoCalculateReturnDate(); autoUpdateTripTitle();">
                        @error('duration_days') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        <div class="form-text extra-small">{{ __('Select standard trip duration package or search/type custom.') }}</div>
                    </div>

                    {{-- 3. Departure City Dropdown (Searchable Filter List - Riyadh First) --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark d-flex justify-content-between align-items-center">
                            <span>{{ __('Departure City') }} <span class="text-danger">*</span></span>
                            <small class="text-muted fw-normal">{{ __('City of departure (Saudi Arabia)') }}</small>
                        </label>
                        @php
                            $selectedDeparture = old('departure_city', 'Riyadh');
                            $isCustomDeparture = !array_key_exists($selectedDeparture, $departureCities);
                            $departureTriggerText = $isCustomDeparture 
                                ? __('Custom (:city)', ['city' => $selectedDeparture]) 
                                : ($departureCities[$selectedDeparture] ?? $selectedDeparture);
                        @endphp
                        <div class="filter-dropdown" id="departure-filter-dropdown">
                            <input type="text" id="departureCityTrigger" class="form-control filter-trigger @error('departure_city') is-invalid @enderror" readonly value="{{ $departureTriggerText }}" placeholder="{{ __('Select Departure City *') }}">
                            <div class="filter-panel" id="departureFilterPanel">
                                <div class="filter-search-wrap">
                                    <input type="text" class="filter-search" id="departureSearchInput" placeholder="{{ __('Search city (Riyadh, Jeddah, Dammam, الرياض)...') }}">
                                </div>
                                <div class="filter-list" id="departureFilterList">
                                    @foreach($departureCities as $cVal => $cLabel)
                                        @php $isSel = (!$isCustomDeparture && $selectedDeparture == $cVal); @endphp
                                        <div class="filter-option {{ $isSel ? 'selected' : '' }}" data-value="{{ $cVal }}" data-label="{{ $cLabel }}">
                                            <span class="opt-name">
                                                @if($cVal === 'Riyadh')
                                                    <span class="badge bg-primary text-white me-1">{{ __('Primary') }}</span>
                                                @endif
                                                {{ $cLabel }}
                                            </span>
                                            <i class="fas fa-check check-icon"></i>
                                        </div>
                                    @endforeach
                                    <div class="filter-option custom-opt {{ $isCustomDeparture ? 'selected' : '' }}" data-value="__custom__" data-label="{{ __('Custom City...') }}">
                                        <span><i class="fas fa-plus-circle me-1"></i>{{ __('Custom / Other City (مدينة أخرى)...') }}</span>
                                        <i class="fas fa-edit text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="text" name="departure_city" id="departureCityInput" class="form-control mt-2 {{ $isCustomDeparture ? '' : 'd-none' }} @error('departure_city') is-invalid @enderror" value="{{ $selectedDeparture }}" placeholder="{{ __('Type departure city name') }}" required oninput="autoUpdateTripTitle()">
                        @error('departure_city') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        <div class="form-text extra-small">{{ __('Select departure point with real-time search. Riyadh is prioritized at top.') }}</div>
                    </div>

                    {{-- 4. Destination City Dropdown (Searchable Filter List) --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark d-flex justify-content-between align-items-center">
                            <span>{{ __('Destination City') }} <span class="text-danger">*</span></span>
                            <small class="text-muted fw-normal">{{ __('Trip destination') }}</small>
                        </label>
                        @php
                            $selectedDestination = old('destination_city', 'Makkah Al-Mukarramah');
                            $destKeyMatch = array_key_exists($selectedDestination, $destinationCities);
                            $destValMatch = in_array($selectedDestination, $destinationCities);
                            $isCustomDestination = !$destKeyMatch && !$destValMatch;
                            $destinationTriggerText = $isCustomDestination 
                                ? __('Custom (:city)', ['city' => $selectedDestination]) 
                                : ($destinationCities[$selectedDestination] ?? $selectedDestination);
                        @endphp
                        <div class="filter-dropdown" id="destination-filter-dropdown">
                            <input type="text" id="destinationCityTrigger" class="form-control filter-trigger @error('destination_city') is-invalid @enderror" readonly value="{{ $destinationTriggerText }}" placeholder="{{ __('Select Destination City *') }}">
                            <div class="filter-panel" id="destinationFilterPanel">
                                <div class="filter-search-wrap">
                                    <input type="text" class="filter-search" id="destinationSearchInput" placeholder="{{ __('Search Holy destination (Makkah, Madinah, مكة)...') }}">
                                </div>
                                <div class="filter-list" id="destinationFilterList">
                                    @foreach($destinationCities as $destVal => $destLabel)
                                        @php $isSel = (!$isCustomDestination && ($selectedDestination == $destVal || $selectedDestination == $destLabel)); @endphp
                                        <div class="filter-option {{ $isSel ? 'selected' : '' }}" data-value="{{ $destVal }}" data-label="{{ $destLabel }}">
                                            <span class="opt-name">{{ $destLabel }}</span>
                                            <i class="fas fa-check check-icon"></i>
                                        </div>
                                    @endforeach
                                    <div class="filter-option custom-opt {{ $isCustomDestination ? 'selected' : '' }}" data-value="__custom__" data-label="{{ __('Custom Destination...') }}">
                                        <span><i class="fas fa-plus-circle me-1"></i>{{ __('Custom / Other Destination (وجهة أخرى)...') }}</span>
                                        <i class="fas fa-edit text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="text" name="destination_city" id="destinationCityInput" class="form-control mt-2 {{ $isCustomDestination ? '' : 'd-none' }} @error('destination_city') is-invalid @enderror" value="{{ $selectedDestination }}" placeholder="{{ __('Type destination city name') }}" required oninput="autoUpdateTripTitle()">
                        @error('destination_city') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        <div class="form-text extra-small">{{ __('Select Holy destination or search custom city.') }}</div>
                    </div>

                    {{-- 5. Trip Title / Route Headline --}}
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold text-dark mb-0">
                                {{ __('Trip Title / Route Name') }} <span class="text-danger">*</span>
                            </label>
                            <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 rounded-pill small" onclick="generateAutoTitle(true)" title="{{ __('Automatically build headline from chosen route and duration') }}">
                                <i class="fas fa-magic me-1"></i> {{ __('Auto-Generate Title') }}
                            </button>
                        </div>
                        <input type="text" name="title" id="tripTitleInput" class="form-control @error('title') is-invalid @enderror" placeholder="{{ __('Makkah & Madinah 3-Days Executive Umrah') }}" value="{{ old('title') }}" required>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text extra-small">{{ __('Descriptive trip headline displayed on passenger tickets and manifests.') }}</div>
                    </div>

                    {{-- 6. Departure Date & Time and Return Date & Time --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">
                            {{ __('Departure Date & Time') }} <span class="text-danger">*</span>
                        </label>
                        <input type="datetime-local" name="departure_date" class="form-control @error('departure_date') is-invalid @enderror" value="{{ old('departure_date') }}" required id="depDateInput" onchange="autoCalculateReturnDate()">
                        @error('departure_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center">
                            <label class="form-label fw-bold text-dark mb-1">
                                {{ __('Return Date & Time') }}
                            </label>
                            <small class="text-muted fw-normal" id="calcReturnNote">{{ __('Auto-calculated from duration') }}</small>
                        </div>
                        <input type="datetime-local" name="return_date" class="form-control @error('return_date') is-invalid @enderror" value="{{ old('return_date') }}" id="retDateInput" onchange="onReturnDateChanged()">
                        @error('return_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- 7. Bus Fleet Details --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">
                            {{ __('Bus Fleet Name / Model') }} <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="bus_name" class="form-control @error('bus_name') is-invalid @enderror" placeholder="{{ __('VIP Luxury Coach Bus') }}" value="{{ old('bus_name', 'VIP Luxury Coach Bus') }}" required>
                        @error('bus_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark">
                            {{ __('Bus Plate / Reg #') }}
                        </label>
                        <input type="text" name="bus_number" class="form-control @error('bus_number') is-invalid @enderror" placeholder="{{ __('4819-KSA') }}" value="{{ old('bus_number') }}">
                        @error('bus_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark">
                            {{ __('Total Seats') }} <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="total_seats" id="totalSeatsInput" class="form-control @error('total_seats') is-invalid @enderror" value="{{ old('total_seats', 49) }}" min="10" max="100" required oninput="recalcSeatClasses(false)">
                        @error('total_seats') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text extra-small">{{ __('Standard passenger coach has 49 seats.') }}</div>
                    </div>

                    {{-- 8. Seat Classes Configuration (VIP, Business, Economy) --}}
                    <div class="col-12 mt-2 pt-3 border-top">
                        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                            <div>
                                <label class="form-label fw-bold text-dark mb-0">
                                    <i class="fas fa-layer-group text-primary me-1"></i>{{ __('Seat Classes & Tier Allocation (درجات المقاعد الثلاث)') }}
                                </label>
                                <div class="text-muted extra-small">{{ __('Allocate total coach seats across VIP, Business and Economy with custom counts & fares.') }}</div>
                            </div>
                            <span class="badge bg-primary text-white" id="seatClassSummaryBadge">
                                49 {{ __('Total Seats Allocated') }}
                            </span>
                        </div>

                        <div class="row g-3 p-3 rounded-4 bg-light border">
                            {{-- VIP Class --}}
                            <div class="col-md-4">
                                <div class="card border-0 shadow-xs rounded-3 p-3 h-100 bg-white" style="border-top: 3px solid #f59e0b !important;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-warning text-dark fw-bold px-2 py-1">
                                            <i class="fas fa-crown me-1"></i> {{ __('VIP Class') }}
                                        </span>
                                        <span class="text-muted extra-small font-monospace fw-semibold" id="vipSeatRangePreview">{{ __('Seats 1 - 10') }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label text-dark small fw-bold mb-1">{{ __('VIP Seats Count') }}</label>
                                        <input type="number" name="vip_seats_count" id="vipSeatsCountInput" class="form-control form-control-sm @error('vip_seats_count') is-invalid @enderror" value="{{ old('vip_seats_count', 10) }}" min="0" max="100" oninput="recalcSeatClasses()">
                                        @error('vip_seats_count') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div>
                                        <label class="form-label text-dark small fw-semibold mb-1">{{ __('VIP Fare (SAR)') }}</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light">{{ currency_symbol() }}</span>
                                            <input type="number" step="0.01" name="vip_price" id="vipPriceInput" class="form-control" placeholder="150.00" value="{{ old('vip_price', '150.00') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Business Class --}}
                            <div class="col-md-4">
                                <div class="card border-0 shadow-xs rounded-3 p-3 h-100 bg-white" style="border-top: 3px solid #3b82f6 !important;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-primary text-white fw-bold px-2 py-1">
                                            <i class="fas fa-briefcase me-1"></i> {{ __('Business Class') }}
                                        </span>
                                        <span class="text-muted extra-small font-monospace fw-semibold" id="bizSeatRangePreview">{{ __('Seats 11 - 25') }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label text-dark small fw-bold mb-1">{{ __('Business Seats Count') }}</label>
                                        <input type="number" name="business_seats_count" id="bizSeatsCountInput" class="form-control form-control-sm @error('business_seats_count') is-invalid @enderror" value="{{ old('business_seats_count', 15) }}" min="0" max="100" oninput="recalcSeatClasses()">
                                        @error('business_seats_count') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div>
                                        <label class="form-label text-dark small fw-semibold mb-1">{{ __('Business Fare (SAR)') }}</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light">{{ currency_symbol() }}</span>
                                            <input type="number" step="0.01" name="business_price" id="bizPriceInput" class="form-control" placeholder="120.00" value="{{ old('business_price', '120.00') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Economy Class --}}
                            <div class="col-md-4">
                                <div class="card border-0 shadow-xs rounded-3 p-3 h-100 bg-white" style="border-top: 3px solid #10b981 !important;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold px-2 py-1">
                                            <i class="fas fa-chair me-1"></i> {{ __('Economy Class') }}
                                        </span>
                                        <span class="text-muted extra-small font-monospace fw-semibold" id="ecoSeatRangePreview">{{ __('Seats 26 - 49') }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label text-dark small fw-bold mb-1">{{ __('Economy Seats Count') }}</label>
                                        <input type="number" name="economy_seats_count" id="ecoSeatsCountInput" class="form-control form-control-sm @error('economy_seats_count') is-invalid @enderror" value="{{ old('economy_seats_count', 24) }}" min="0" max="100" oninput="recalcSeatClasses(true)">
                                        @error('economy_seats_count') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div>
                                        <label class="form-label text-dark small fw-semibold mb-1">{{ __('Economy Fare (SAR)') }}</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light">{{ currency_symbol() }}</span>
                                            <input type="number" step="0.01" name="economy_price" id="ecoPriceInput" class="form-control" placeholder="100.00" value="{{ old('economy_price', '100.00') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mt-2">
                                <div class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-white border">
                                    <span class="extra-small text-muted">
                                        <i class="fas fa-info-circle text-info me-1"></i>
                                        {{ __('Total seats sum updates dynamically as you adjust VIP, Business, or Economy seat counts.') }}
                                    </span>
                                    <span class="extra-small fw-bold text-dark font-monospace" id="seatClassRatioPreview">
                                        VIP: 10 &bull; Business: 15 &bull; Economy: 24 = 49 Seats
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 8. Trip Overview & Terms / Conditions --}}
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold text-dark mb-0">
                                <i class="fas fa-file-contract text-primary me-1"></i>{{ __('Trip Overview & Passenger Conditions (الشروط وملاحظات الرحلة)') }}
                            </label>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 extra-small" onclick="document.getElementById('tripDescTextarea').value = @js(\App\Models\TravelTrip::defaultConditions())">
                                <i class="fas fa-undo me-1"></i>{{ __('Reset to Standard Conditions') }}
                            </button>
                        </div>
                        <textarea name="description" id="tripDescTextarea" rows="6" class="form-control font-monospace" style="font-size: 0.82rem; line-height: 1.45;" placeholder="{{ __('Terms & conditions, brief itinerary points, ziyarat stops...') }}">{{ old('description', \App\Models\TravelTrip::defaultConditions()) }}</textarea>
                        <div class="form-text extra-small">{{ __('These terms & conditions are auto-loaded by default and print on ticket vouchers & PDFs. You can edit or add notes anytime.') }}</div>
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
                        <input type="text" name="driver_1_name" class="form-control" placeholder="{{ __('Adnan Khan') }}" value="{{ old('driver_1_name') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark small">{{ __('Nationality / Country') }}</label>
                        <input type="text" name="driver_1_nationality" class="form-control" placeholder="{{ __('PAK, SAU, IND, EGY') }}" value="{{ old('driver_1_nationality', 'PAK') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small">{{ __('Phone / Iqama Number') }}</label>
                        <input type="text" name="driver_1_phone" class="form-control" placeholder="{{ __('0501234567 / 2580796130') }}" value="{{ old('driver_1_phone') }}">
                    </div>

                    <div class="col-12 mt-3 pt-2 border-top">
                        <span class="badge bg-secondary text-white mb-2">{{ __('Co-Driver 2 (السائق الثاني - اختياري)') }}</span>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-bold text-dark small">{{ __('Driver 2 Full Name') }}</label>
                        <input type="text" name="driver_2_name" class="form-control" placeholder="{{ __('Showkat') }}" value="{{ old('driver_2_name') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark small">{{ __('Nationality / Country') }}</label>
                        <input type="text" name="driver_2_nationality" class="form-control" placeholder="{{ __('PAK, SAU, IND, EGY') }}" value="{{ old('driver_2_nationality', 'PAK') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small">{{ __('Phone / Iqama Number') }}</label>
                        <input type="text" name="driver_2_phone" class="form-control" placeholder="{{ __('0509876543 / 2620061750') }}" value="{{ old('driver_2_phone') }}">
                    </div>
                </div>
            </x-card>

            <!-- Hotel & Accommodation Details -->
            <x-card :title="__('3. Hotel & Accommodation Partner (Optional)')" class="mt-4">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-bold text-dark">{{ __('Hotel Name in Makkah / Madinah') }}</label>
                        <input type="text" name="hotel_name" class="form-control @error('hotel_name') is-invalid @enderror" placeholder="{{ __('Pullman Zamzam / Swissôtel / Dar Al Tawhid') }}" value="{{ old('hotel_name') }}">
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
                        <input type="text" name="hotel_address" class="form-control @error('hotel_address') is-invalid @enderror" placeholder="{{ __('Ibrahim Al Khalil Road, Makkah / Near Haram') }}" value="{{ old('hotel_address') }}">
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
                    <label class="form-label fw-bold text-dark">{{ __('Round-Trip Fare (Without Hotel) *') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light fw-bold">{{ currency_symbol() }}</span>
                        <input type="number" step="0.01" min="0" name="price_without_hotel" id="priceWithoutHotelInput" class="form-control @error('price_without_hotel') is-invalid @enderror" placeholder="0.00" value="{{ old('price_without_hotel', '0.00') }}" required>
                    </div>
                    @error('price_without_hotel') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    <div class="form-text extra-small">{{ __('Standard two-way bus transportation ticket per passenger.') }}</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark">{{ __('One-Way Fare (ذهاب فقط / اتجاه واحد)') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light fw-bold">{{ currency_symbol() }}</span>
                        <input type="number" step="0.01" min="0" name="one_way_price" class="form-control @error('one_way_price') is-invalid @enderror" placeholder="0.00" value="{{ old('one_way_price', '0.00') }}">
                    </div>
                    @error('one_way_price') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    <div class="form-text extra-small">{{ __('Fare for passengers travelling one-way only (without return).') }}</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-success">{{ __('Complete Package (With Hotel)') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-success-subtle text-success fw-bold">{{ currency_symbol() }}</span>
                        <input type="number" step="0.01" min="0" name="price_with_hotel" id="priceWithHotelInput" class="form-control font-monospace @error('price_with_hotel') is-invalid @enderror" placeholder="0.00" value="{{ old('price_with_hotel', '0.00') }}">
                    </div>
                    @error('price_with_hotel') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    <div class="form-text extra-small">{{ __('Includes seat ticket + hotel stay. Defaults to 0.00 (enter custom package amount if offered).') }}</div>
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

@push('scripts')
<script>
function initFilterDropdown(options) {
    const dropdown = document.getElementById(options.dropdownId);
    if (!dropdown) return;

    const trigger = dropdown.querySelector('.filter-trigger');
    const panel = dropdown.querySelector('.filter-panel');
    const searchInput = dropdown.querySelector('.filter-search');
    const listEl = dropdown.querySelector('.filter-list');
    const hiddenInput = document.getElementById(options.inputId);
    const customOption = listEl.querySelector('.custom-opt');

    const items = [];
    listEl.querySelectorAll('.filter-option:not(.custom-opt)').forEach(function(el) {
        items.push({
            el: el,
            value: el.dataset.value,
            label: el.dataset.label,
            searchStr: (el.dataset.search || (el.dataset.label + ' ' + el.dataset.value)).toLowerCase()
        });
    });

    function esc(s) {
        return (s || '').replace(/[&<>"']/g, function(c){
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
        });
    }

    function hl(text, q) {
        if (!text) return '';
        if (!q) return esc(text);
        const rx = new RegExp('(' + q.replace(/[.*+?^${}()|[\]\\]/g,'\\$&') + ')', 'gi');
        return esc(text).replace(rx, '<mark style="background:#fff3cd;padding:0 2px;border-radius:2px">$1</mark>');
    }

    function renderFilter(q) {
        q = (q || '').toLowerCase().trim();
        let matchCount = 0;

        items.forEach(function(item) {
            const isMatch = !q || item.searchStr.indexOf(q) > -1;
            if (isMatch) {
                item.el.style.display = 'flex';
                const nameSpan = item.el.querySelector('.opt-name');
                if (nameSpan) {
                    nameSpan.innerHTML = hl(item.label, q);
                }
                matchCount++;
            } else {
                item.el.style.display = 'none';
            }
        });

        if (customOption) {
            customOption.style.display = 'flex';
        }
    }

    function openDropdown() {
        document.querySelectorAll('.filter-panel.open').forEach(function(p) {
            if (p !== panel) p.classList.remove('open');
        });
        document.querySelectorAll('.filter-trigger.active').forEach(function(t) {
            if (t !== trigger) t.classList.remove('active');
        });

        panel.classList.add('open');
        trigger.classList.add('active');
        searchInput.value = '';
        renderFilter('');
        setTimeout(function() { searchInput.focus(); }, 60);
    }

    function closeDropdown() {
        panel.classList.remove('open');
        trigger.classList.remove('active');
    }

    trigger.addEventListener('click', function(e) {
        e.stopPropagation();
        panel.classList.contains('open') ? closeDropdown() : openDropdown();
    });

    searchInput.addEventListener('input', function() {
        renderFilter(this.value);
    });

    searchInput.addEventListener('click', function(e) {
        e.stopPropagation();
    });

    items.forEach(function(item) {
        item.el.addEventListener('click', function(e) {
            e.stopPropagation();
            listEl.querySelectorAll('.filter-option').forEach(o => o.classList.remove('selected'));
            item.el.classList.add('selected');
            trigger.value = item.label;
            hiddenInput.value = item.value;
            if (customOption) {
                hiddenInput.classList.add('d-none');
            }
            closeDropdown();
            if (options.onSelect) {
                options.onSelect(item.value, item.label, false);
            }
        });
    });

    if (customOption) {
        customOption.addEventListener('click', function(e) {
            e.stopPropagation();
            listEl.querySelectorAll('.filter-option').forEach(o => o.classList.remove('selected'));
            customOption.classList.add('selected');
            trigger.value = customOption.dataset.label || 'Custom...';
            hiddenInput.classList.remove('d-none');
            hiddenInput.focus();
            closeDropdown();
            if (options.onSelect) {
                options.onSelect(hiddenInput.value, 'Custom', true);
            }
        });
    }

    document.addEventListener('click', function(e) {
        if (!dropdown.contains(e.target)) {
            closeDropdown();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDropdown();
        }
    });
}

function syncDurationBadge(val) {
    const badge = document.getElementById('durationBadge');
    if (badge && val) badge.textContent = val + ' Days';
}

function autoCalculateReturnDate() {
    const depInput = document.getElementById('depDateInput');
    const retInput = document.getElementById('retDateInput');
    const durationInput = document.getElementById('durationInput');
    if (!depInput || !retInput || !durationInput) return;

    const depVal = depInput.value;
    const days = parseInt(durationInput.value, 10);
    if (!depVal || isNaN(days) || days <= 0) return;

    try {
        const depDate = new Date(depVal);
        if (isNaN(depDate.getTime())) return;
        
        // Travel duration convention: Day 1 is departure day.
        // E.g. A 3-day trip starting on Nov 25th returns on Nov 27th (25, 26, 27 = 3 days).
        // Therefore, addDays = Math.max(0, days - 1).
        const addDays = Math.max(0, days - 1);
        const retDate = new Date(depDate.getTime() + (addDays * 24 * 60 * 60 * 1000));
        
        const year = retDate.getFullYear();
        const month = String(retDate.getMonth() + 1).padStart(2, '0');
        const day = String(retDate.getDate()).padStart(2, '0');
        const hours = String(retDate.getHours()).padStart(2, '0');
        const minutes = String(retDate.getMinutes()).padStart(2, '0');
        
        retInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
        const note = document.getElementById('calcReturnNote');
        if (note) {
            note.innerHTML = `<span class="text-success"><i class="fas fa-check-circle me-1"></i>${days} {{ __('Days Trip') }}</span>`;
        }
    } catch (e) {
        console.error(e);
    }
}

function onReturnDateChanged() {
    const depInput = document.getElementById('depDateInput');
    const retInput = document.getElementById('retDateInput');
    const durationInput = document.getElementById('durationInput');
    if (!depInput || !retInput || !durationInput || !depInput.value || !retInput.value) return;

    try {
        const depDate = new Date(depInput.value);
        const retDate = new Date(retInput.value);
        if (isNaN(depDate.getTime()) || isNaN(retDate.getTime())) return;

        const diffMs = retDate.getTime() - depDate.getTime();
        if (diffMs >= 0) {
            const diffDays = Math.round(diffMs / (24 * 60 * 60 * 1000)) + 1;
            if (diffDays >= 1 && diffDays <= 90) {
                durationInput.value = diffDays;
                syncDurationBadge(diffDays);
                const durTrigger = document.getElementById('durationTrigger');
                if (durTrigger) durTrigger.value = diffDays + ' Days';
                const note = document.getElementById('calcReturnNote');
                if (note) {
                    note.innerHTML = `<span class="text-success"><i class="fas fa-check-circle me-1"></i>${diffDays} {{ __('Days Trip') }}</span>`;
                }
                autoUpdateTripTitle();
            }
        }
    } catch(e) { console.error(e); }
}

function generateAutoTitle(force = false) {
    const titleInput = document.getElementById('tripTitleInput');
    if (!titleInput) return;
    if (!force && titleInput.value.trim() !== '') return;

    const depInput = document.getElementById('departureCityInput');
    const destInput = document.getElementById('destinationCityInput');
    const durInput = document.getElementById('durationInput');

    const dep = (depInput && depInput.value.trim()) ? depInput.value.trim() : 'Riyadh';
    const dest = (destInput && destInput.value.trim()) ? destInput.value.trim() : 'Makkah Al-Mukarramah';
    const dur = (durInput && durInput.value.trim()) ? durInput.value.trim() : '3';

    titleInput.value = `${dest} ${dur}-Days Umrah Trip (${dep})`;
}

function autoUpdateTripTitle() {
    const titleInput = document.getElementById('tripTitleInput');
    if (!titleInput) return;
    if (titleInput.value.trim() === '' || titleInput.dataset.autoGenerated === 'true') {
        titleInput.dataset.autoGenerated = 'true';
        generateAutoTitle(true);
    }
}

function setPrice(inputId, val) {
    const input = document.getElementById(inputId);
    if (input) {
        input.value = parseFloat(val).toFixed(2);
    }
}

function focusCustomPrice(inputId) {
    const input = document.getElementById(inputId);
    if (input) {
        input.value = '';
        input.focus();
    }
}

function recalcSeatClasses(ecoEditedByUser = false) {
    const totalInput = document.getElementById('totalSeatsInput');
    const vipInput = document.getElementById('vipSeatsCountInput');
    const bizInput = document.getElementById('bizSeatsCountInput');
    const ecoInput = document.getElementById('ecoSeatsCountInput');
    if (!totalInput || !vipInput || !bizInput || !ecoInput) return;

    let vip = Math.max(0, parseInt(vipInput.value, 10) || 0);
    let biz = Math.max(0, parseInt(bizInput.value, 10) || 0);
    let eco = Math.max(0, parseInt(ecoInput.value, 10) || 0);
    let total = Math.max(10, parseInt(totalInput.value, 10) || 49);

    if (ecoEditedByUser) {
        total = vip + biz + eco;
        totalInput.value = total;
    } else {
        if (total < (vip + biz)) {
            total = vip + biz;
            totalInput.value = total;
        }
        eco = Math.max(0, total - (vip + biz));
        ecoInput.value = eco;
    }

    const vipRange = document.getElementById('vipSeatRangePreview');
    const bizRange = document.getElementById('bizSeatRangePreview');
    const ecoRange = document.getElementById('ecoSeatRangePreview');
    const summaryBadge = document.getElementById('seatClassSummaryBadge');
    const ratioPreview = document.getElementById('seatClassRatioPreview');

    if (vipRange) vipRange.innerText = vip > 0 ? `Seats 1 - ${vip}` : `0 Seats`;
    if (bizRange) bizRange.innerText = biz > 0 ? `Seats ${vip + 1} - ${vip + biz}` : `0 Seats`;
    if (ecoRange) ecoRange.innerText = eco > 0 ? `Seats ${vip + biz + 1} - ${total}` : `0 Seats`;
    if (summaryBadge) summaryBadge.innerText = `${total} Total Seats Allocated`;
    if (ratioPreview) ratioPreview.innerText = `VIP: ${vip} • Business: ${biz} • Economy: ${eco} = ${total} Seats`;
}

document.addEventListener('DOMContentLoaded', function() {
    recalcSeatClasses(false);
    // 1. Init Duration Filter Dropdown
    initFilterDropdown({
        dropdownId: 'duration-filter-dropdown',
        inputId: 'durationInput',
        onSelect: function(val, label, isCustom) {
            syncDurationBadge(val);
            autoCalculateReturnDate();
            autoUpdateTripTitle();
        }
    });

    // 2. Init Departure City Filter Dropdown
    initFilterDropdown({
        dropdownId: 'departure-filter-dropdown',
        inputId: 'departureCityInput',
        onSelect: function(val, label, isCustom) {
            autoUpdateTripTitle();
        }
    });

    // 3. Init Destination City Filter Dropdown
    initFilterDropdown({
        dropdownId: 'destination-filter-dropdown',
        inputId: 'destinationCityInput',
        onSelect: function(val, label, isCustom) {
            autoUpdateTripTitle();
        }
    });

    // Auto calculate return date if initial departure date is present
    const depInput = document.getElementById('depDateInput');
    const retInput = document.getElementById('retDateInput');
    if (depInput && depInput.value && (!retInput || !retInput.value)) {
        autoCalculateReturnDate();
    }
});
</script>
@endpush
@endsection
