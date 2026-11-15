@extends('admin.layouts.app')

@section('title', __('Booking Voucher') . ' - ' . $booking->booking_reference)

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-primary text-white fs-6 px-3 py-1">{{ $booking->booking_reference }}</span>
            @if($booking->booking_status === 'confirmed')
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1">
                    <i class="fas fa-check-circle me-1"></i> {{ __('Confirmed') }}
                </span>
            @else
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1">
                    <i class="fas fa-times-circle me-1"></i> {{ __('Cancelled') }}
                </span>
            @endif

            @if($booking->payment_status === 'paid')
                <span class="badge bg-success text-white px-3 py-1">
                    <i class="fas fa-check-double me-1"></i> {{ __('Paid in Full') }}
                </span>
            @elseif($booking->payment_status === 'partial')
                <span class="badge bg-warning text-dark px-3 py-1">
                    <i class="fas fa-hourglass-half me-1"></i> {{ __('Partially Paid') }}
                </span>
            @else
                <span class="badge bg-danger text-white px-3 py-1">
                    <i class="fas fa-exclamation-circle me-1"></i> {{ __('Unpaid') }}
                </span>
            @endif
        </div>
        <h2 class="fw-bold text-dark mb-0">
            {{ $booking->passenger_name }} — <span class="text-primary">{{ __('Seat #:seat', ['seat' => $booking->seat_number]) }}</span>
        </h2>
        <p class="text-muted small mb-0">{{ $booking->trip ? $booking->trip->title : '' }} ({{ $booking->trip ? $booking->trip->trip_code : '' }})</p>
    </div>

    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="{{ route('travel.bookings.voucher', $booking->id) }}" target="_blank" class="btn btn-outline-dark">
            <i class="fas fa-print me-1"></i> {{ __('Print Boarding Pass') }}
        </a>
        <a href="{{ route('travel.bookings.edit', $booking->id) }}" class="btn btn-outline-primary">
            <i class="fas fa-edit me-1"></i> {{ __('Edit Booking') }}
        </a>
        @if($booking->invoice)
            <a href="{{ route('invoices.show', $booking->invoice_id) }}" class="btn btn-outline-info">
                <i class="fas fa-file-invoice-dollar me-1"></i> {{ __('View Invoice #:inv', ['inv' => $booking->invoice->invoice_number]) }}
            </a>
        @endif
        <a href="{{ route('travel.bookings.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Bookings') }}
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Passenger & Bus Seat Details -->
    <div class="col-lg-7">
        <!-- Passenger & Coach Profile -->
        <x-card :title="__('Passenger & Travel Details')" class="mb-4">
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
                        <span class="fw-bold text-dark font-monospace">{{ $booking->passenger_id_number ?: '—' }}</span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 border">
                        <span class="text-muted extra-small d-block text-uppercase fw-bold">{{ __('Linked CRM Customer') }}</span>
                        @if($booking->customer)
                            <a href="{{ route('customers.show', $booking->customer_id) }}" class="fw-bold text-primary text-decoration-none">
                                <i class="fas fa-user-check me-1"></i> {{ $booking->customer->name }}
                            </a>
                        @else
                            <span class="text-muted">{{ __('Direct Passenger (No CRM link)') }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Seat & Route Highlight Card -->
            <div class="mt-3 p-3 rounded-3 text-white d-flex flex-wrap justify-content-between align-items-center gap-3" style="background: linear-gradient(135deg, #0A6FB7 0%, #034475 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 60px; height: 60px; background: #FA7436; font-size: 1.4rem;">
                        #{{ $booking->seat_number }}
                    </div>
                    <div>
                        <span class="badge bg-white text-dark extra-small mb-1">{{ __('Allocated Coach Seat') }}</span>
                        <h5 class="fw-bold mb-0 text-white">{{ $booking->trip ? $booking->trip->bus_name : 'Umrah Coach' }} ({{ $booking->trip ? $booking->trip->bus_model : '49 Seats' }})</h5>
                        <p class="extra-small mb-0 opacity-75">
                            {{ $booking->trip ? $booking->trip->departure_city : '' }} &rarr; {{ $booking->trip ? $booking->trip->destination_city : '' }}
                        </p>
                    </div>
                </div>

                @if($booking->trip)
                    <div class="text-lg-end">
                        <span class="extra-small opacity-75 d-block">{{ __('Departure Schedule') }}</span>
                        <span class="fw-bold fs-6 d-block">{{ $booking->trip->departure_date ? $booking->trip->departure_date->format('d M Y, h:i A') : '—' }}</span>
                        <span class="badge bg-light text-dark extra-small mt-1">
                            <i class="fas fa-clock me-1"></i> {{ $booking->trip->duration_days }} {{ __('Days Trip') }}
                        </span>
                    </div>
                @endif
            </div>
        </x-card>

        <!-- Accommodation Details Card -->
        <x-card :title="__('Package & Accommodation')">
            <div class="p-3 rounded-3 mb-3 border {{ $booking->package_type === 'with_hotel' ? 'bg-primary-subtle border-primary-subtle' : 'bg-light' }}">
                <div class="d-flex align-items-center gap-2 mb-2">
                    @if($booking->package_type === 'with_hotel')
                        <span class="badge bg-primary text-white fs-6">
                            <i class="fas fa-hotel me-1"></i> {{ __('Full Package With Hotel') }}
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
                    <div class="col-md-6">
                        <label class="form-label text-muted extra-small uppercase fw-bold mb-1">{{ __('Hotel Name') }}</label>
                        <div class="fw-bold text-dark fs-6">{{ $booking->hotel_name ?: ($booking->trip->hotel_name ?? __('Designated Hotel')) }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted extra-small uppercase fw-bold mb-1">{{ __('Room Sharing Category') }}</label>
                        <div class="fw-bold text-dark fs-6">
                            <span class="badge bg-light border text-dark">
                                {{ ucfirst(str_replace('_', ' ', $booking->room_type ?: 'quad_sharing')) }}
                            </span>
                        </div>
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
        <x-card :title="__('Financial & Payment Breakdown')">
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted">{{ __('Travel Bus Fare') }}</td>
                            <td class="text-end fw-bold font-monospace">{{ number_format($booking->travel_fare, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('Hotel & Accommodation Expense') }}</td>
                            <td class="text-end fw-bold font-monospace">{{ number_format($booking->hotel_expense, 2) }}</td>
                        </tr>
                        @if($booking->extra_charges > 0)
                            <tr>
                                <td class="text-muted">{{ __('Extra Services / Charges') }}</td>
                                <td class="text-end fw-bold font-monospace">+ {{ number_format($booking->extra_charges, 2) }}</td>
                            </tr>
                        @endif
                        @if($booking->discount > 0)
                            <tr>
                                <td class="text-muted">{{ __('Discount Applied') }}</td>
                                <td class="text-end fw-bold text-danger font-monospace">- {{ number_format($booking->discount, 2) }}</td>
                            </tr>
                        @endif
                        @if($booking->tax_amount > 0)
                            <tr>
                                <td class="text-muted">{{ __('Tax / VAT') }}</td>
                                <td class="text-end fw-bold font-monospace">+ {{ number_format($booking->tax_amount, 2) }}</td>
                            </tr>
                        @endif
                        <tr class="border-top table-light">
                            <td class="fw-bold fs-6 text-dark py-3">{{ __('Total Package Amount') }}</td>
                            <td class="text-end fw-bold fs-5 text-primary font-monospace py-3">{{ number_format($booking->total_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-success fw-bold">{{ __('Amount Paid') }}</td>
                            <td class="text-end fw-bold text-success font-monospace">{{ number_format($booking->paid_amount, 2) }}</td>
                        </tr>
                        <tr class="{{ $booking->balance_due > 0 ? 'table-warning' : 'table-success' }}">
                            <td class="fw-bold">{{ __('Balance Due') }}</td>
                            <td class="text-end fw-bold fs-6 font-monospace {{ $booking->balance_due > 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format($booking->balance_due, 2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-3 p-3 bg-light rounded-3 border">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted extra-small uppercase fw-bold">{{ __('Payment Method') }}</span>
                    <span class="badge bg-light text-dark border">{{ ucfirst(str_replace('_', ' ', $booking->payment_method)) }}</span>
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
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                            <i class="fas fa-ban me-1"></i> {{ __('Cancel Booking & Release Seat #:seat', ['seat' => $booking->seat_number]) }}
                        </button>
                    </form>
                </div>
            @endif
        </x-card>
    </div>
</div>
@endsection
