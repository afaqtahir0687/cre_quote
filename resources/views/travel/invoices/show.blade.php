@extends('admin.layouts.app')

@section('title', __('Official Tax Invoice') . ' - ' . $invoice->invoice_number)

@section('content')
<!-- Page Header (Matching Travel Booking Voucher & Invoices) -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div class="d-flex align-items-center">
        <a href="{{ route('invoices.index') }}" class="btn btn-light btn-sm rounded-circle me-3 shadow-sm" title="{{ __('Back to Invoices') }}">
            <i class="fas fa-arrow-left text-muted"></i>
        </a>
        <div>
            <div class="d-flex align-items-center flex-wrap gap-2">
                <h2 class="fw-bold text-dark mb-0">{{ __('Official Tax Invoice') }}</h2>
                <span class="badge-soft badge-soft-primary fs-6">{{ $invoice->invoice_number }}</span>
                <span class="badge bg-light text-primary border font-monospace">{{ $booking->booking_reference }}</span>
                @php
                    $groupSeats = isset($groupBookings) && $groupBookings->isNotEmpty() ? $groupBookings : collect([$booking]);
                    $seatsList = $groupSeats->pluck('seat_number')->all();
                @endphp
                @if(count($seatsList) > 1)
                    <span class="badge bg-primary text-white">{{ count($seatsList) }} {{ __('Seats') }} (#{{ implode(', #', $seatsList) }})</span>
                @else
                    <span class="badge bg-primary text-white">{{ __('Seat #:seat', ['seat' => $booking->seat_number]) }}</span>
                @endif

                @if(strtolower($invoice->status) === 'cancelled' || $booking->booking_status === 'cancelled')
                    <span class="badge bg-danger"><i class="fas fa-ban me-1"></i> {{ __('Cancelled') }}</span>
                @elseif(strtolower($invoice->status) === 'paid' || (float)$invoice->balance_due <= 0)
                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> {{ __('Paid') }}</span>
                @elseif(strtolower($invoice->status) === 'partially paid' || (float)$invoice->amount_paid > 0)
                    <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> {{ __('Partial Paid') }}</span>
                @else
                    <span class="badge bg-secondary"><i class="fas fa-hourglass-start me-1"></i> {{ __('Unpaid') }}</span>
                @endif

                @php
                    $bShop = $booking->shop ?: ($invoice->shop ?? null);
                    $bCr = $bShop?->cr_number ?: ($bShop?->setting?->company_cr_no ?? null);
                    $bLic = $bShop?->license_number ?: ($bShop?->setting?->company_license_no ?? null);
                @endphp
                @if($bCr)
                    <span class="badge bg-light text-secondary border font-monospace" title="{{ __('Commercial Registration No') }}">
                        <i class="fas fa-file-contract text-primary me-1"></i>CR: {{ $bCr }}
                    </span>
                @endif
                @if($bLic)
                    <span class="badge bg-success-subtle text-success border font-monospace" title="{{ __('Ministry License No') }}">
                        <i class="fas fa-id-card-alt me-1"></i>LIC: {{ $bLic }}
                    </span>
                @endif
            </div>
            <p class="text-muted small mb-0 mt-1">
                {{ tdb($booking->passenger_name) }} &bull; {{ $booking->trip ? tdb($booking->trip->title) : '' }} ({{ $booking->trip ? $booking->trip->trip_code : '' }}) &bull; {{ __('Date') }}: {{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') : $invoice->created_at->format('d M Y') }}
            </p>
        </div>
    </div>

    <div class="d-flex gap-2 flex-wrap align-items-center">
        @if(strtolower($invoice->status) !== 'cancelled' && (float)$invoice->balance_due > 0)
            <button type="button" class="btn btn-primary-modern d-flex align-items-center shadow-sm" data-bs-toggle="modal" data-bs-target="#paymentModal">
                <i class="fas fa-plus me-2"></i> {{ __('Record Payment') }}
            </button>
        @endif
        <a href="{{ route('travel.bookings.voucher', $booking->id) }}" target="_blank" class="btn btn-light border d-flex align-items-center shadow-sm">
            <i class="fas fa-print me-2 text-primary"></i> {{ __('Print Ticket (A5)') }}
        </a>
        <a href="{{ route('invoices.pdf', $invoice->id) }}" target="_blank" class="btn btn-outline-danger d-flex align-items-center shadow-sm">
            <i class="fas fa-file-pdf me-2"></i> {{ __('Download A5 PDF') }}
        </a>
        <a href="{{ route('travel.bookings.show', $booking->id) }}" class="btn btn-outline-primary d-flex align-items-center shadow-sm">
            <i class="fas fa-ticket-alt me-2"></i> {{ __('Booking Voucher') }}
        </a>
        <a href="{{ route('travel.bookings.edit', $booking->id) }}" class="btn btn-outline-info d-flex align-items-center shadow-sm">
            <i class="fas fa-edit me-2"></i> {{ __('Edit') }}
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Passenger & Bus Seat Details -->
    <div class="col-lg-7">
        <!-- Passenger & Coach Profile -->
        <x-card :title="__('Passenger & Travel Details')" class="border-0 shadow-sm rounded-4 mb-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 border">
                        <span class="text-muted extra-small d-block text-uppercase fw-bold">{{ __('Passenger Name') }}</span>
                        <span class="fs-5 fw-bold text-dark d-block">{{ tdb($booking->passenger_name) }}</span>
                        <span class="badge bg-secondary-subtle text-secondary extra-small mt-1">
                            <i class="fas fa-{{ $booking->passenger_gender === 'female' ? 'venus' : 'mars' }} me-1"></i>
                            {{ ucfirst(__($booking->passenger_gender)) }}
                        </span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 border">
                        <span class="text-muted extra-small d-block text-uppercase fw-bold">{{ __('Contact Phone') }}</span>
                        <a href="tel:{{ $booking->passenger_phone }}" class="fs-5 fw-bold text-primary text-decoration-none d-block">
                            {{ $booking->passenger_phone }}
                        </a>
                        @php
                            $waClean = preg_replace('/[^0-9]/', '', $booking->passenger_phone);
                        @endphp
                        <a href="https://wa.me/{{ $waClean }}" target="_blank" class="text-success extra-small fw-semibold text-decoration-none mt-1 d-inline-flex align-items-center gap-1">
                            <i class="fab fa-whatsapp"></i> {{ __('Chat on WhatsApp') }}
                        </a>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 border">
                        <span class="text-muted extra-small d-block text-uppercase fw-bold">{{ __('ID / Iqama / Passport Number') }}</span>
                        <span class="fw-bold text-dark font-monospace fs-6">{{ $booking->passenger_id_number ?: '—' }}</span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 border">
                        <span class="text-muted extra-small d-block text-uppercase fw-bold">{{ __('Linked CRM Customer') }}</span>
                        @if($booking->customer)
                            <a href="{{ route('customers.show', $booking->customer_id) }}" class="fw-bold text-primary text-decoration-none fs-6 d-block text-truncate">
                                <i class="fas fa-user-check me-1"></i> {{ $booking->customer->name }}
                            </a>
                        @else
                            <span class="text-muted small">{{ __('Direct Passenger (Auto Registered)') }}</span>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 border">
                        <span class="text-muted extra-small d-block text-uppercase fw-bold">{{ __('Journey Type / Direction') }}</span>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            {!! $booking->journeyTypeBadge() !!}
                            @if($booking->isOneWay() && $booking->one_way_direction)
                                <span class="badge bg-light text-dark border extra-small">
                                    {{ $booking->one_way_direction === 'return' ? __('Return / Inbound') : __('Outbound / Going') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 border">
                        <span class="text-muted extra-small d-block text-uppercase fw-bold">{{ __('Boarding & Drop-off Points') }}</span>
                        <div class="small fw-bold text-dark mt-1">
                            <span class="text-success"><i class="fas fa-sign-in-alt me-1"></i>{{ $booking->effective_pickup_location }}</span>
                            <span class="text-muted mx-1">&rarr;</span>
                            <span class="text-danger"><i class="fas fa-map-marker-alt me-1"></i>{{ $booking->effective_dropoff_location }}</span>
                        </div>
                        @if($booking->isRoundTrip() && $booking->return_dropoff_location)
                            <div class="extra-small text-muted mt-2 pt-1 border-top">
                                <i class="fas fa-undo-alt text-warning me-1"></i>
                                <strong>{{ __('Return Midway Stop / موقع النزول في العودة:') }}</strong>
                                <span class="text-dark fw-bold">{{ $booking->return_dropoff_location }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-2 px-3 bg-light rounded-3 border d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-store text-primary fs-5"></i>
                            <div>
                                <span class="text-muted extra-small d-block text-uppercase fw-bold">{{ __('Booking Branch / Counter') }}</span>
                                <strong class="text-dark small">{{ $booking->shop?->name ?? __('Main Office') }}</strong>
                            </div>
                        </div>
                        <span class="badge bg-primary text-white extra-small">{{ __('Branch') }}</span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-2 px-3 bg-light rounded-3 border d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-user-tag text-primary fs-5"></i>
                            <div>
                                <span class="text-muted extra-small d-block text-uppercase fw-bold">{{ __('Booking Agent / Salesman') }}</span>
                                <strong class="text-dark small">{{ tdb($booking->effective_salesman) }}</strong>
                                @if($booking->effective_salesman_phone)
                                    <div class="text-muted extra-small mt-1"><i class="fas fa-phone-alt me-1 text-primary"></i><span dir="ltr">{{ $booking->effective_salesman_phone }}</span></div>
                                @endif
                            </div>
                        </div>
                        <span class="badge bg-primary text-white extra-small">{{ __('Staff') }}</span>
                    </div>
                </div>
            </div>

            <!-- Seat & Route Highlight Banner -->
            <div class="mt-3 p-3 rounded-3 text-white d-flex flex-wrap justify-content-between align-items-center gap-3" 
                 style="background: linear-gradient(135deg, #0F172A 0%, #1E293B 60%, #1E3A8A 100%); border-left: 4px solid var(--erp-primary);">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex flex-column align-items-center justify-content-center text-white fw-bold shadow-sm p-2 text-center" 
                         style="min-width: 65px; height: 58px; background: var(--erp-primary, #4361EE); font-size: {{ count($seatsList) > 2 ? '0.95rem' : '1.3rem' }};">
                        @if(count($seatsList) > 1)
                            <div class="extra-small text-white-50" style="font-size: 0.6rem;">{{ count($seatsList) }} {{ __('Seats') }}</div>
                            <div class="text-truncate" style="max-width: 80px;">#{{ implode(',#', $seatsList) }}</div>
                        @else
                            #{{ $booking->seat_number }}
                        @endif
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-warning text-dark extra-small mb-1 fw-bold text-uppercase">{{ __('Allocated Coach Seat') }}</span>
                            @if(count($seatsList) > 1)
                                <span class="badge bg-success text-white extra-small mb-1 fw-bold">
                                    {{ __('Group / Family Booking') }} ({{ count($seatsList) }} {{ __('Seats') }})
                                </span>
                            @endif
                        </div>
                        <h5 class="fw-bold mb-0 text-white">{{ $booking->trip ? tdb($booking->trip->bus_name) : 'Umrah Coach' }} (49 Seats)</h5>
                        <p class="extra-small mb-0 opacity-75 mt-1">
                            <i class="fas fa-route me-1 text-info"></i>{{ $booking->trip ? tdb($booking->trip->departure_city) : '' }} &rarr; {{ $booking->trip ? tdb($booking->trip->destination_city) : '' }}
                            @if($booking->trip && $booking->trip->bus_number) &bull; Plate: {{ $booking->trip->bus_number }} @endif
                            @if(count($seatsList) > 1)
                                &bull; <strong class="text-warning">{{ __('Seats') }}: #{{ implode(', #', $seatsList) }}</strong>
                            @endif
                        </p>
                    </div>
                </div>

                @if($booking->trip)
                    <div class="text-lg-end">
                        <span class="extra-small opacity-75 d-block text-uppercase">{{ __('Departure Schedule') }}</span>
                        <span class="fw-bold fs-6 d-block">{{ $booking->trip->departure_date ? $booking->trip->departure_date->format('d M Y, h:i A') : '—' }}</span>
                        <span class="badge bg-light text-dark extra-small mt-1">
                            <i class="fas fa-calendar-day text-primary me-1"></i> {{ $booking->trip->duration_days }} {{ __('Days Trip') }}
                        </span>
                    </div>
                @endif
            </div>

            @if(count($groupSeats) > 1)
                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold text-dark mb-0">
                            <i class="fas fa-users text-primary me-1"></i> {{ __('Allocated Seats & Passengers') }}
                        </h6>
                        <span class="badge bg-primary text-white">{{ count($groupSeats) }} {{ __('Passengers') }}</span>
                    </div>
                    <div class="table-responsive rounded-3 border bg-white">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 80px;">{{ __('Seat #') }}</th>
                                    <th>{{ __('Passenger Name') }}</th>
                                    <th class="text-center" style="width: 100px;">{{ __('Gender') }}</th>
                                    <th>{{ __('ID / Iqama / Passport') }}</th>
                                    <th class="text-end" style="width: 120px;">{{ __('Travel Fare') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groupSeats as $gb)
                                    <tr>
                                        <td class="text-center"><span class="badge bg-primary fs-6 px-2">#{{ $gb->seat_number }}</span></td>
                                        <td class="fw-bold text-dark">{{ tdb($gb->passenger_name) }}</td>
                                        <td class="text-center"><span class="badge bg-light text-dark border">{{ ucfirst(__($gb->passenger_gender)) }}</span></td>
                                        <td class="font-monospace text-muted">{{ $gb->passenger_id_number ?: '—' }}</td>
                                        <td class="text-end fw-bold font-monospace">{{ format_currency($gb->travel_fare) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </x-card>

        <!-- Accommodation Details Card -->
        <x-card :title="__('Package & Accommodation')" class="border-0 shadow-sm rounded-4">
            <div class="p-3 rounded-3 mb-3 border {{ $booking->package_type === 'with_hotel' ? 'bg-success-subtle border-success-subtle' : 'bg-light' }}">
                <div class="d-flex align-items-center gap-2 mb-1">
                    @if($booking->package_type === 'with_hotel')
                        <span class="badge bg-success text-white fs-6">
                            <i class="fas fa-hotel me-1"></i> {{ __('Full Package With Hotel Included') }}
                        </span>
                    @else
                        <span class="badge bg-secondary text-white fs-6">
                            <i class="fas fa-bus me-1"></i> {{ __('Transport Only (Without Hotel)') }}
                        </span>
                    @endif
                </div>
                <p class="small text-muted mb-0">
                    @if($booking->package_type === 'with_hotel')
                        {{ __('Includes round-trip 49-seat coach transport plus confirmed hotel accommodation in Makkah / Madinah.') }}
                    @else
                        {{ __('Includes coach seat transport only. Passenger arranges own accommodation.') }}
                    @endif
                </p>
            </div>

            @if($booking->package_type === 'with_hotel')
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label text-muted extra-small uppercase fw-bold mb-1">{{ __('Hotel Name') }}</label>
                        <div class="fw-bold text-dark fs-6">{{ tdb($booking->effective_hotel_name ?: __('Designated Hotel')) }}</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted extra-small uppercase fw-bold mb-1">{{ __('Room Category / Type') }}</label>
                        <div>
                            {!! $booking->roomTypeBadge() !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted extra-small uppercase fw-bold mb-1">{{ __('Extra Hotel Nights') }}</label>
                        <div class="fw-bold text-dark fs-6">
                            {{ $booking->nights_count ?? 0 }} {{ __('Nights') }}
                            @if(($booking->makkah_nights ?? 0) > 0 || ($booking->madinah_nights ?? 0) > 0)
                                <div class="small text-muted mt-1">
                                    @if(($booking->makkah_nights ?? 0) > 0)
                                        <span class="badge bg-success-subtle text-success me-1"><i class="fas fa-kaaba me-1"></i>{{ $booking->makkah_nights }} {{ __('Makkah') }}</span>
                                    @endif
                                    @if(($booking->madinah_nights ?? 0) > 0)
                                        <span class="badge bg-info-subtle text-info"><i class="fas fa-mosque me-1"></i>{{ $booking->madinah_nights }} {{ __('Madinah') }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted extra-small uppercase fw-bold mb-1">{{ __('Hotel Location / City') }}</label>
                        <div class="fw-bold text-dark fs-6"><i class="fas fa-map-marker-alt text-danger me-1"></i>{{ $booking->hotelCityLabel() }}</div>
                    </div>
                    @if($booking->effective_hotel_address)
                        <div class="col-12 mt-2">
                            <label class="form-label text-muted extra-small uppercase fw-bold mb-1"><i class="fas fa-location-arrow text-primary me-1"></i> {{ __('Hotel Address / Details') }}</label>
                            <div class="p-2 rounded bg-light border text-dark small">{{ tdb($booking->effective_hotel_address) }}</div>
                        </div>
                    @endif
                </div>
            @endif

            @if($booking->notes)
                <div class="mt-3 pt-3 border-top">
                    <label class="form-label text-muted extra-small uppercase fw-bold mb-1">{{ __('Passenger / Special Notes') }}</label>
                    <p class="small text-dark mb-0 bg-light p-2 rounded border">{{ $booking->notes }}</p>
                </div>
            @endif
        </x-card>
    </div>

    <!-- Right Column: Financial Breakdown & Payment Summary -->
    <div class="col-lg-5">
        <x-card :title="__('Financial & Payment Breakdown')" class="border-0 shadow-sm rounded-4 mb-4">
            @php
                $totFare = $groupSeats->sum('travel_fare');
                $totHotel = $groupSeats->sum('hotel_expense');
                $totExtra = $groupSeats->sum('extra_charges');
                $totDisc = $groupSeats->sum('discount');
                $totTax = $groupSeats->sum('tax_amount');
                $totGrand = (float) $invoice->grand_total;
                $totPaid = (float) $invoice->amount_paid;
                $totBal = (float) $invoice->balance_due;
            @endphp
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted">
                                {{ __('Travel Bus Fare') }}
                                @if(count($seatsList) > 1)
                                    <span class="badge bg-light text-dark border extra-small ms-1">({{ count($seatsList) }} {{ __('Seats') }}: #{{ implode(', #', $seatsList) }})</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold font-monospace">{{ format_currency($totFare) }}</td>
                        </tr>
                        @if($totHotel > 0)
                            <tr>
                                <td class="text-muted">{{ __('Hotel & Accommodation Expense') }}</td>
                                <td class="text-end fw-bold font-monospace">{{ format_currency($totHotel) }}</td>
                            </tr>
                        @endif
                        @if($totExtra > 0)
                            <tr>
                                <td class="text-muted">{{ __('Extra Services / Charges') }}</td>
                                <td class="text-end fw-bold font-monospace">+ {{ format_currency($totExtra) }}</td>
                            </tr>
                        @endif
                        @if($totDisc > 0)
                            <tr>
                                <td class="text-muted">{{ __('Discount Applied') }}</td>
                                <td class="text-end fw-bold text-danger font-monospace">- {{ format_currency($totDisc) }}</td>
                            </tr>
                        @endif
                        @if($totTax > 0)
                            <tr>
                                <td class="text-muted">{{ __('Tax / VAT') }}</td>
                                <td class="text-end fw-bold font-monospace">+ {{ format_currency($totTax) }}</td>
                            </tr>
                        @endif
                        <tr class="border-top table-light">
                            <td class="fw-bold fs-6 text-dark py-3">{{ __('Total Package Amount') }}</td>
                            <td class="text-end fw-bold fs-5 text-primary font-monospace py-3">{{ format_currency($totGrand) }}</td>
                        </tr>
                        <tr>
                            <td class="text-success fw-bold">{{ __('Amount Paid') }}</td>
                            <td class="text-end fw-bold text-success font-monospace">{{ format_currency($totPaid) }}</td>
                        </tr>
                        <tr class="{{ $totBal > 0 ? 'table-warning' : 'table-success' }}">
                            <td class="fw-bold">{{ __('Balance Due') }}</td>
                            <td class="text-end fw-bold fs-6 font-monospace {{ $totBal > 0 ? 'text-danger' : 'text-success' }}">
                                {{ format_currency($totBal) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-3 p-3 bg-light rounded-3 border">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted extra-small uppercase fw-bold">{{ __('Payment Method') }}</span>
                    <span class="badge bg-white text-dark border">{{ ucfirst(str_replace('_', ' ', $booking->payment_method ?: $invoice->payment_method ?? 'Cash')) }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted extra-small uppercase fw-bold">{{ __('Invoice Date') }}</span>
                    <span class="small text-dark font-monospace">{{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') : $invoice->created_at->format('d M Y') }}</span>
                </div>
            </div>

            @if(strtolower($invoice->status) !== 'cancelled' && $totBal > 0)
                <div class="mt-3">
                    <button type="button" class="btn btn-primary-modern w-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#paymentModal">
                        <i class="fas fa-plus-circle me-1"></i> {{ __('Record Payment') }} ({{ format_currency($totBal) }})
                    </button>
                </div>
            @endif
        </x-card>

        <!-- Payment History Card -->
        <x-card :title="__('Payment History')" class="border-0 shadow-sm rounded-4">
            @if($invoice->payments && $invoice->payments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Method') }}</th>
                                <th class="text-end">{{ __('Amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->payments as $payment)
                                <tr>
                                    <td class="small">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $payment->payment_method }}</span>
                                        @if($payment->note)
                                            <div class="extra-small text-muted text-truncate" style="max-width: 150px;">{{ $payment->note }}</div>
                                        @endif
                                    </td>
                                    <td class="text-end fw-bold text-success font-monospace">{{ format_currency($payment->amount) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-3 text-muted">
                    <i class="fas fa-receipt fa-2x mb-2 text-black-50"></i>
                    <p class="small mb-0">{{ __('No payment records found yet.') }}</p>
                </div>
            @endif
        </x-card>
    </div>
</div>

<!-- Record Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('payments.store', $invoice) }}" method="POST">
            @csrf
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="fas fa-money-bill-wave text-success me-2"></i> {{ __('Record Payment') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small">{{ __('Invoice Number') }}:</span>
                            <strong class="font-monospace text-primary">{{ $invoice->invoice_number }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">{{ __('Balance Due') }}:</span>
                            <strong class="font-monospace text-danger fs-6">{{ format_currency($invoice->balance_due) }}</strong>
                        </div>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="date" name="payment_date" id="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        <label for="payment_date">{{ __('Payment Date') }}</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="number" step="0.01" max="{{ $invoice->balance_due }}" name="amount" id="payment_amount" class="form-control" value="{{ $invoice->balance_due }}" required>
                        <label for="payment_amount">{{ __('Payment Amount') }}</label>
                    </div>

                    <div class="form-floating mb-3">
                        <select name="payment_method" id="payment_method" class="form-select" required>
                            <option value="Cash">{{ __('Cash at Counter (نقدي)') }}</option>
                            <option value="Bank Transfer">{{ __('Bank Transfer (تحويل بنكي)') }}</option>
                            <option value="Credit Card">{{ __('Credit / Debit Card (شبكة)') }}</option>
                            <option value="Online Payment">{{ __('Online Payment (إلكتروني)') }}</option>
                            <option value="Check">{{ __('Check (شيك)') }}</option>
                        </select>
                        <label for="payment_method">{{ __('Payment Method') }}</label>
                    </div>

                    <div class="form-floating">
                        <textarea name="note" id="payment_note" class="form-control" style="height: 80px" placeholder="Optional note"></textarea>
                        <label for="payment_note">{{ __('Note (Optional)') }}</label>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-link text-muted text-decoration-none" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary-modern px-4">{{ __('Confirm Payment') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
