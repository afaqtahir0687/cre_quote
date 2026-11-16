@extends('admin.layouts.app')

@section('title', __('Booking Voucher') . ' - ' . $booking->booking_reference)

@section('content')
<!-- Page Header (Matching Job Orders & Invoices) -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        <a href="{{ route('travel.bookings.index') }}" class="btn btn-light btn-sm rounded-circle me-3 shadow-sm">
            <i class="fas fa-arrow-left text-muted"></i>
        </a>
        <div>
            <div class="d-flex align-items-center">
                <h2 class="fw-bold text-dark mb-0">{{ __('Booking Detail') }}</h2>
                <span class="badge-soft badge-soft-primary ms-3">{{ $booking->booking_reference }}</span>
                <span class="badge bg-primary text-white ms-2">{{ __('Seat #:seat', ['seat' => $booking->seat_number]) }}</span>
                @if($booking->booking_status === 'cancelled')
                    <span class="badge bg-danger ms-2"><i class="fas fa-ban me-1"></i> {{ __('Cancelled') }}</span>
                @elseif($booking->payment_status === 'paid')
                    <span class="badge bg-success ms-2"><i class="fas fa-check-circle me-1"></i> {{ __('Paid') }}</span>
                @elseif($booking->payment_status === 'partial')
                    <span class="badge bg-warning text-dark ms-2"><i class="fas fa-clock me-1"></i> {{ __('Partial Paid') }}</span>
                @else
                    <span class="badge bg-secondary ms-2"><i class="fas fa-hourglass-start me-1"></i> {{ __('Unpaid') }}</span>
                @endif
            </div>
            <p class="text-muted small mb-0 mt-1">
                {{ $booking->passenger_name }} &bull; {{ $booking->trip ? $booking->trip->title : '' }} ({{ $booking->trip ? $booking->trip->trip_code : '' }})
            </p>
        </div>
    </div>

    <div class="d-flex gap-2">
        <a href="{{ route('travel.bookings.voucher', $booking->id) }}" target="_blank" class="btn btn-primary-modern d-flex align-items-center shadow-sm">
            <i class="fas fa-print me-2"></i> {{ __('Print Boarding Pass') }}
        </a>
        <a href="{{ route('travel.bookings.edit', $booking->id) }}" class="btn btn-outline-info d-flex align-items-center shadow-sm">
            <i class="fas fa-edit me-2"></i> {{ __('Edit') }}
        </a>
        @if($booking->invoice)
            <a href="{{ route('invoices.show', $booking->invoice_id) }}" class="btn btn-outline-success d-flex align-items-center shadow-sm">
                <i class="fas fa-receipt me-2"></i> {{ __('Invoice') }}
            </a>
        @endif
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
                        <span class="fs-5 fw-bold text-dark d-block">{{ $booking->passenger_name }}</span>
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

                <div class="col-md-12">
                    <div class="p-2 px-3 bg-light rounded-3 border d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-store text-primary fs-5"></i>
                            <div>
                                <span class="text-muted extra-small d-block text-uppercase fw-bold">{{ __('Booking Branch / Counter') }}</span>
                                <strong class="text-dark small">{{ $booking->shop?->name ?? __('Main Office') }}</strong>
                            </div>
                        </div>
                        <span class="badge bg-primary text-white extra-small">{{ __('Branch Ledger Account') }}</span>
                    </div>
                </div>
            </div>

            <!-- Seat & Route Highlight Banner -->
            <div class="mt-3 p-3 rounded-3 text-white d-flex flex-wrap justify-content-between align-items-center gap-3" 
                 style="background: linear-gradient(135deg, #0F172A 0%, #1E293B 60%, #1E3A8A 100%); border-left: 4px solid var(--erp-primary);">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" 
                         style="width: 58px; height: 58px; background: var(--erp-primary, #4361EE); font-size: 1.4rem;">
                        #{{ $booking->seat_number }}
                    </div>
                    <div>
                        <span class="badge bg-warning text-dark extra-small mb-1 fw-bold text-uppercase">{{ __('Allocated Coach Seat') }}</span>
                        <h5 class="fw-bold mb-0 text-white">{{ $booking->trip ? $booking->trip->bus_name : 'Umrah Coach' }} (49 Seats)</h5>
                        <p class="extra-small mb-0 opacity-75 mt-1">
                            <i class="fas fa-route me-1 text-info"></i>{{ $booking->trip ? $booking->trip->departure_city : '' }} &rarr; {{ $booking->trip ? $booking->trip->destination_city : '' }}
                            @if($booking->trip && $booking->trip->bus_number) &bull; Plate: {{ $booking->trip->bus_number }} @endif
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
                    <div class="col-md-5">
                        <label class="form-label text-muted extra-small uppercase fw-bold mb-1">{{ __('Hotel Name') }}</label>
                        <div class="fw-bold text-dark fs-6">{{ $booking->hotel_name ?: ($booking->trip->hotel_name ?? __('Designated Hotel')) }}</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted extra-small uppercase fw-bold mb-1">{{ __('Room Sharing Category') }}</label>
                        <div>
                            <span class="badge bg-light border text-dark fs-6">
                                {{ ucfirst(str_replace('_', ' ', $booking->room_type ?: 'quad_sharing')) }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted extra-small uppercase fw-bold mb-1">{{ __('Stay Duration') }}</label>
                        <div class="fw-bold text-dark fs-6">{{ $booking->nights_count ?: ($booking->trip?->duration_days ?: 10) }} {{ __('Nights') }}</div>
                    </div>
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
        <x-card :title="__('Financial & Payment Breakdown')" class="border-0 shadow-sm rounded-4">
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted">{{ __('Travel Bus Fare') }}</td>
                            <td class="text-end fw-bold font-monospace">{{ format_currency($booking->travel_fare) }}</td>
                        </tr>
                        @if($booking->hotel_expense > 0)
                            <tr>
                                <td class="text-muted">{{ __('Hotel & Accommodation Expense') }}</td>
                                <td class="text-end fw-bold font-monospace">{{ format_currency($booking->hotel_expense) }}</td>
                            </tr>
                        @endif
                        @if($booking->extra_charges > 0)
                            <tr>
                                <td class="text-muted">{{ __('Extra Services / Charges') }}</td>
                                <td class="text-end fw-bold font-monospace">+ {{ format_currency($booking->extra_charges) }}</td>
                            </tr>
                        @endif
                        @if($booking->discount > 0)
                            <tr>
                                <td class="text-muted">{{ __('Discount Applied') }}</td>
                                <td class="text-end fw-bold text-danger font-monospace">- {{ format_currency($booking->discount) }}</td>
                            </tr>
                        @endif
                        @if($booking->tax_amount > 0)
                            <tr>
                                <td class="text-muted">{{ __('Tax / VAT') }}</td>
                                <td class="text-end fw-bold font-monospace">+ {{ format_currency($booking->tax_amount) }}</td>
                            </tr>
                        @endif
                        <tr class="border-top table-light">
                            <td class="fw-bold fs-6 text-dark py-3">{{ __('Total Package Amount') }}</td>
                            <td class="text-end fw-bold fs-5 text-primary font-monospace py-3">{{ format_currency($booking->total_amount) }}</td>
                        </tr>
                        <tr>
                            <td class="text-success fw-bold">{{ __('Amount Paid') }}</td>
                            <td class="text-end fw-bold text-success font-monospace">{{ format_currency($booking->paid_amount) }}</td>
                        </tr>
                        <tr class="{{ $booking->balance_due > 0 ? 'table-warning' : 'table-success' }}">
                            <td class="fw-bold">{{ __('Balance Due') }}</td>
                            <td class="text-end fw-bold fs-6 font-monospace {{ $booking->balance_due > 0 ? 'text-danger' : 'text-success' }}">
                                {{ format_currency($booking->balance_due) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-3 p-3 bg-light rounded-3 border">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted extra-small uppercase fw-bold">{{ __('Payment Method') }}</span>
                    <span class="badge bg-white text-dark border">{{ ucfirst(str_replace('_', ' ', $booking->payment_method)) }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted extra-small uppercase fw-bold">{{ __('Booking Date') }}</span>
                    <span class="small text-dark font-monospace">{{ $booking->created_at->format('d M Y, h:i A') }}</span>
                </div>
            </div>

            <!-- Cancellation Warning or Action -->
            @if($booking->booking_status === 'confirmed')
                <div class="mt-4 pt-3 border-top text-center">
                    <form action="{{ route('travel.bookings.cancel', $booking->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to cancel this booking and free Seat #:seat for other passengers?', ['seat' => $booking->seat_number]) }}');">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100 shadow-sm">
                            <i class="fas fa-ban me-1"></i> {{ __('Cancel Booking & Release Seat #:seat', ['seat' => $booking->seat_number]) }}
                        </button>
                    </form>
                </div>
            @endif
        </x-card>
    </div>
</div>
@endsection
