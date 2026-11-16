<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Boarding Pass') }} - {{ $booking->booking_reference }} - {{ $booking->passenger_name }}</title>
    
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    @if(app()->getLocale() === 'ar')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    @endif
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700;800&family=Libre+Barcode+39&display=swap" rel="stylesheet">

    <style>
        :root {
            --erp-primary: #4361EE;
            --erp-primary-dark: #2C46B6;
            --erp-navy: #0F172A;
            --erp-navy-light: #1E293B;
            --erp-gold: #D97706;
            --erp-gold-light: #FDE68A;
            --erp-success: #10B981;
            --erp-border: #E2E8F0;
            --erp-bg-soft: #F8FAFC;
        }

        body {
            font-family: {{ app()->getLocale() === 'ar' ? "'Cairo', sans-serif" : "'Inter', -apple-system, BlinkMacSystemFont, sans-serif" }};
            background: #F1F5F9;
            color: #1E293B;
            margin: 0;
            padding: 28px 12px;
            -webkit-font-smoothing: antialiased;
        }

        .print-toolbar {
            max-width: 960px;
            margin: 0 auto 16px auto;
        }

        .ticket-wrapper {
            max-width: 960px;
            margin: 0 auto;
            background: #FFFFFF;
            border-radius: 18px;
            box-shadow: 0 15px 40px rgba(15, 23, 42, 0.08);
            border: 1px solid var(--erp-border);
            overflow: hidden;
            position: relative;
        }

        /* Top Brand Strip */
        .ticket-brand-strip {
            background: linear-gradient(135deg, var(--erp-navy) 0%, var(--erp-navy-light) 60%, #1E3A8A 100%);
            color: #FFFFFF;
            padding: 20px 28px;
            border-bottom: 3px solid var(--erp-gold);
        }

        .brand-logo-img {
            max-height: 52px;
            max-width: 170px;
            object-fit: contain;
            background: #FFFFFF;
            padding: 4px 8px;
            border-radius: 8px;
        }

        .brand-logo-fallback {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.12);
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #FFFFFF;
            font-size: 1.4rem;
        }

        /* Main Ticket Container with Perforation */
        .ticket-body {
            display: flex;
            flex-direction: row;
            align-items: stretch;
            background: #FFFFFF;
        }

        .ticket-main {
            flex: 1 1 73%;
            padding: 24px 28px;
            border-right: 2px dashed #CBD5E1;
            position: relative;
        }

        [dir="rtl"] .ticket-main {
            border-right: none;
            border-left: 2px dashed #CBD5E1;
        }

        .ticket-stub {
            flex: 0 0 27%;
            width: 27%;
            padding: 24px 20px;
            background: #FAFAFC;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        /* Route Header Box */
        .route-hero {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 20px;
        }

        .route-city {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--erp-navy);
            line-height: 1.2;
        }

        .route-icon-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--erp-primary);
        }

        .seat-badge-main {
            background: linear-gradient(135deg, var(--erp-primary) 0%, var(--erp-primary-dark) 100%);
            color: #FFFFFF;
            padding: 8px 18px;
            border-radius: 10px;
            font-size: 1.25rem;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.25);
        }

        .seat-badge-stub {
            background: linear-gradient(135deg, var(--erp-primary) 0%, var(--erp-primary-dark) 100%);
            color: #FFFFFF;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 1.75rem;
            font-weight: 900;
            text-align: center;
            letter-spacing: 0.05em;
            box-shadow: 0 4px 14px rgba(67, 97, 238, 0.25);
        }

        /* Clean Info Grid */
        .info-cell-label {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748B;
            letter-spacing: 0.03em;
            margin-bottom: 2px;
        }

        .info-cell-value {
            font-size: 0.92rem;
            font-weight: 700;
            color: #0F172A;
            line-height: 1.3;
        }

        /* Package Pill */
        .pkg-pill-hotel {
            background: #ECFDF5;
            color: #065F46;
            border: 1px solid #A7F3D0;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .pkg-pill-bus {
            background: #EFF6FF;
            color: #1E40AF;
            border: 1px solid #BFDBFE;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        /* Financial Strip */
        .financial-strip {
            border: 1px solid #E2E8F0;
            border-radius: 10px;
            background: #FFFFFF;
            overflow: hidden;
            margin-top: 18px;
        }

        .financial-strip table {
            margin-bottom: 0;
            font-size: 0.84rem;
        }

        .financial-strip th {
            background: #F8FAFC;
            color: #475569;
            font-weight: 600;
            font-size: 0.78rem;
            padding: 6px 12px;
        }

        .financial-strip td {
            padding: 6px 12px;
        }

        /* Barcode Simulation */
        .barcode-font {
            font-family: 'Libre Barcode 39', cursive;
            font-size: 42px;
            letter-spacing: 2px;
            line-height: 1;
            color: #0F172A;
            text-align: center;
            user-select: none;
        }

        /* Perforation Notches */
        .notch-top, .notch-bottom {
            position: absolute;
            width: 22px;
            height: 22px;
            background: #F1F5F9;
            border-radius: 50%;
            z-index: 5;
        }

        .notch-top {
            top: -11px;
            right: calc(27% - 11px);
        }

        [dir="rtl"] .notch-top {
            right: auto;
            left: calc(27% - 11px);
        }

        .notch-bottom {
            bottom: -11px;
            right: calc(27% - 11px);
        }

        [dir="rtl"] .notch-bottom {
            right: auto;
            left: calc(27% - 11px);
        }

        /* Guidelines Footer */
        .guidelines-box {
            background: #F8FAFC;
            border-radius: 8px;
            padding: 10px 14px;
            border: 1px solid #E2E8F0;
            margin-top: 14px;
        }

        /* Print Media Styles */
        @media print {
            body {
                background: #FFFFFF !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .print-toolbar {
                display: none !important;
            }

            .ticket-wrapper {
                max-width: 100% !important;
                box-shadow: none !important;
                border: 1px solid #CBD5E1 !important;
                border-radius: 12px !important;
            }

            .notch-top, .notch-bottom {
                display: none !important;
            }

            .ticket-brand-strip,
            .seat-badge-main,
            .seat-badge-stub,
            .pkg-pill-hotel,
            .pkg-pill-bus,
            .route-hero {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            @page {
                size: A4 portrait;
                margin: 10mm 12mm;
            }
        }
    </style>
</head>
<body>

    @php
        $shop = $booking->shop;
        $trip = $booking->trip;
        $logoUrl = shop_logo_url($shop);
        $brandName = shop_brand_name($shop);
        $isAr = app()->getLocale() === 'ar';
    @endphp

    <!-- Print / Back Toolbar (Screen Only) -->
    <div class="print-toolbar d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('travel.bookings.show', $booking->id) }}" class="btn btn-outline-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Details') }}
            </a>
            <a href="{{ route('travel.bookings.index') }}" class="btn btn-light btn-sm border shadow-sm">
                <i class="fas fa-list me-1"></i> {{ __('Bookings Roster') }}
            </a>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button onclick="window.print()" class="btn btn-primary btn-sm px-4 shadow-sm" style="background: var(--erp-primary); border-color: var(--erp-primary);">
                <i class="fas fa-print me-1"></i> {{ __('Print Boarding Pass') }}
            </button>
            <button onclick="window.close()" class="btn btn-outline-dark btn-sm">
                <i class="fas fa-times me-1"></i> {{ __('Close') }}
            </button>
        </div>
    </div>

    <!-- Main Printable Boarding Pass Container -->
    <div class="ticket-wrapper">
        <!-- Top Perforation Notch Cutouts -->
        <div class="notch-top"></div>
        <div class="notch-bottom"></div>

        <!-- 1. Official Agency Header Strip -->
        <div class="ticket-brand-strip d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-3">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $brandName }}" class="brand-logo-img">
                @else
                    <div class="brand-logo-fallback">
                        <i class="fas fa-kaaba"></i>
                    </div>
                @endif
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <h4 class="fw-bold mb-0 text-white">{{ $brandName }}</h4>
                        <span class="badge bg-warning text-dark fw-bold text-uppercase px-2 py-1 extra-small">
                            {{ __('Official Umrah Coach Pass') }}
                        </span>
                    </div>
                    <p class="mb-0 extra-small opacity-75 mt-1">
                        {{ $shop->address ?? 'Kingdom of Saudi Arabia' }}
                        @if(!empty($shop->phone)) &bull; <i class="fas fa-phone-alt me-1"></i>{{ $shop->phone }} @endif
                        @if(!empty($shop->email)) &bull; {{ $shop->email }} @endif
                    </p>
                </div>
            </div>

            <div class="text-end">
                <div class="text-white-50 extra-small text-uppercase fw-semibold">{{ __('Booking Reference / رقم الحجز') }}</div>
                <div class="fs-5 fw-bold font-monospace text-white">{{ $booking->booking_reference }}</div>
                <div class="text-white-50 extra-small">{{ __('Issued / تاريخ الإصدار') }}: {{ $booking->created_at->format('d M Y') }}</div>
            </div>
        </div>

        <!-- 2. Dual-Zone Body: Main Voucher + Gate Stub -->
        <div class="ticket-body">
            
            <!-- Zone A: Main Travel Voucher (73%) -->
            <div class="ticket-main">
                
                <!-- Route & Schedule Hero Box -->
                <div class="route-hero">
                    <div class="row align-items-center g-2 text-center text-md-start">
                        <div class="col-md-5">
                            <span class="info-cell-label d-block"><i class="fas fa-map-marker-alt text-danger me-1"></i> {{ __('Departure Terminal / نقطة الانطلاق') }}</span>
                            <div class="route-city">{{ $trip ? $trip->departure_city : __('Departure City') }}</div>
                            <div class="small fw-semibold text-muted mt-1">
                                <i class="far fa-clock text-primary me-1"></i>
                                {{ $trip && $trip->departure_date ? $trip->departure_date->format('d M Y - h:i A') : 'Scheduled' }}
                            </div>
                        </div>

                        <div class="col-md-2 text-center route-icon-box my-2 my-md-0">
                            <i class="fas fa-bus fa-2x mb-1"></i>
                            <div class="extra-small text-muted fw-bold">
                                {{ $trip ? $trip->duration_days : 10 }} {{ __('Days / أيام') }}
                            </div>
                        </div>

                        <div class="col-md-5 text-md-end">
                            <span class="info-cell-label d-block"><i class="fas fa-kaaba text-success me-1"></i> {{ __('Destination / الوجهة') }}</span>
                            <div class="route-city text-primary">{{ $trip ? $trip->destination_city : __('Destination') }}</div>
                            <div class="small fw-semibold text-muted mt-1">
                                <i class="far fa-calendar-check text-success me-1"></i>
                                {{ $trip && $trip->return_date ? $trip->return_date->format('d M Y') : __('Return Included') }}
                            </div>
                        </div>
                    </div>

                    <!-- Bus Coach Details Strip -->
                    <div class="d-flex flex-wrap justify-content-between align-items-center pt-2 mt-2 border-top border-secondary-subtle extra-small text-muted">
                        <div>
                            <i class="fas fa-bus-alt text-primary me-1"></i>
                            <strong>{{ __('Coach Fleet / الحافلة') }}:</strong> {{ $trip ? $trip->bus_name : 'Luxury Coach Bus' }}
                            @if($trip && $trip->bus_number) &bull; <span class="badge bg-light text-dark border">{{ $trip->bus_number }}</span> @endif
                        </div>
                        <div>
                            <i class="fas fa-layer-group text-info me-1"></i>
                            <strong>{{ __('Trip Code / رمز الرحلة') }}:</strong> <span class="text-dark fw-bold">{{ $trip ? $trip->trip_code : '---' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Passenger & Accommodation Details Grid -->
                <div class="row g-3 mb-2">
                    <div class="col-md-6">
                        <div class="p-2 border rounded-3 h-100 bg-white">
                            <span class="info-cell-label"><i class="fas fa-user-circle text-primary me-1"></i> {{ __('Passenger Name / اسم الراكب') }}</span>
                            <div class="info-cell-value text-dark fs-6">{{ $booking->passenger_name }}</div>
                            <div class="extra-small text-muted mt-1">
                                <i class="fas fa-phone-alt text-success me-1"></i> {{ $booking->passenger_phone }}
                                &bull; <i class="fas fa-venus-mars text-secondary me-1"></i> {{ ucfirst(__($booking->passenger_gender)) }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-2 border rounded-3 h-100 bg-white">
                            <span class="info-cell-label"><i class="fas fa-id-card text-info me-1"></i> {{ __('National ID / Iqama / Passport') }}</span>
                            <div class="info-cell-value font-monospace text-dark">{{ $booking->passenger_id_number ?: '—' }}</div>
                            <div class="extra-small text-muted mt-1">
                                {{ __('Identity Document Verified for Boarding') }}
                            </div>
                        </div>
                    </div>

                    <!-- Accommodation / Hotel Package -->
                    <div class="col-12">
                        <div class="p-2 border rounded-3 bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <div>
                                <span class="info-cell-label d-block"><i class="fas fa-concierge-bell text-warning me-1"></i> {{ __('Accommodation Package / باقة الإقامة') }}</span>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    @if($booking->package_type === 'with_hotel')
                                        <span class="pkg-pill-hotel">
                                            <i class="fas fa-hotel"></i> {{ __('With Hotel Included') }}
                                        </span>
                                        <span class="fw-bold text-dark small">
                                            {{ $booking->hotel_name ?: ($trip->hotel_name ?? __('Designated Hotel')) }}
                                        </span>
                                        <span class="badge bg-light text-muted border extra-small">
                                            {{ ucfirst(str_replace('_', ' ', $booking->room_type ?: 'quad_sharing')) }}
                                        </span>
                                        @if($booking->nights_count)
                                            <span class="badge bg-light text-muted border extra-small">
                                                {{ $booking->nights_count }} {{ __('Nights') }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="pkg-pill-bus">
                                            <i class="fas fa-bus"></i> {{ __('Seat Only (Without Hotel)') }}
                                        </span>
                                        <span class="text-muted extra-small">{{ __('Passenger arranged independent accommodation.') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="text-end">
                                <span class="seat-badge-main">
                                    <i class="fas fa-chair"></i>
                                    <span>{{ __('Seat') }} #{{ $booking->seat_number }}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Breakdown Summary -->
                <div class="financial-strip">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('Description / البيان') }}</th>
                                <th class="text-center" style="width: 130px;">{{ __('Status / الحالة') }}</th>
                                <th class="text-end" style="width: 140px;">{{ __('Amount / المبلغ') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <strong>{{ __('Bus Coach Seat Ticket (Seat #:seat)', ['seat' => $booking->seat_number]) }}</strong>
                                    @if($trip) <span class="text-muted extra-small">({{ $trip->bus_name }})</span> @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border extra-small">{{ __('Confirmed') }}</span>
                                </td>
                                <td class="text-end font-monospace fw-semibold">{{ format_currency($booking->travel_fare) }}</td>
                            </tr>
                            @if($booking->hotel_expense > 0)
                                <tr>
                                    <td>
                                        <strong>{{ __('Hotel & Accommodations') }}</strong>
                                        <span class="text-muted extra-small">({{ $booking->hotel_name ?: 'Hotel Stay' }})</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border extra-small">{{ __('Reserved') }}</span>
                                    </td>
                                    <td class="text-end font-monospace fw-semibold">{{ format_currency($booking->hotel_expense) }}</td>
                                </tr>
                            @endif
                            @if($booking->extra_charges > 0)
                                <tr>
                                    <td>{{ __('Extra Charges & Service Fees') }}</td>
                                    <td class="text-center">—</td>
                                    <td class="text-end font-monospace">+ {{ format_currency($booking->extra_charges) }}</td>
                                </tr>
                            @endif
                            @if($booking->discount > 0)
                                <tr>
                                    <td class="text-danger">{{ __('Promotional Discount') }}</td>
                                    <td class="text-center">—</td>
                                    <td class="text-end text-danger font-monospace">- {{ format_currency($booking->discount) }}</td>
                                </tr>
                            @endif
                            <tr class="table-light fw-bold">
                                <td>{{ __('Total Voucher Fare / إجمالي القيمة') }}</td>
                                <td class="text-center">
                                    @if($booking->payment_status === 'paid')
                                        <span class="badge bg-success text-white extra-small">{{ __('Paid in Full') }}</span>
                                    @elseif($booking->payment_status === 'partial')
                                        <span class="badge bg-warning text-dark extra-small">{{ __('Partially Paid') }}</span>
                                    @else
                                        <span class="badge bg-danger text-white extra-small">{{ __('Unpaid') }}</span>
                                    @endif
                                </td>
                                <td class="text-end font-monospace text-primary fs-6">{{ format_currency($booking->total_amount) }}</td>
                            </tr>
                            <tr class="fw-semibold">
                                <td class="text-success"><i class="fas fa-check-circle me-1"></i> {{ __('Amount Paid / المبلغ المدفوع') }} ({{ ucfirst(str_replace('_', ' ', $booking->payment_method)) }})</td>
                                <td></td>
                                <td class="text-end text-success font-monospace">{{ format_currency($booking->paid_amount) }}</td>
                            </tr>
                            @if($booking->balance_due > 0)
                                <tr class="table-warning fw-bold text-danger">
                                    <td><i class="fas fa-exclamation-triangle me-1"></i> {{ __('Balance Due to Conductor / المتبقي للتحصيل') }}</td>
                                    <td class="text-center"><span class="badge bg-danger text-white extra-small">{{ __('Collect at Bus') }}</span></td>
                                    <td class="text-end font-monospace">{{ format_currency($booking->balance_due) }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Passenger Guidelines Box -->
                <div class="guidelines-box">
                    <div class="d-flex align-items-center gap-1 extra-small fw-bold text-dark text-uppercase mb-1">
                        <i class="fas fa-info-circle text-primary"></i>
                        <span>{{ __('Important Passenger Guidelines / تعليمات هامة للمسافرين') }}</span>
                    </div>
                    <ul class="extra-small text-muted mb-0 ps-3" style="line-height: 1.45;">
                        <li>{{ __('Please arrive at the coach departure terminal at least 60 minutes prior to scheduled departure.') }}</li>
                        <li>{{ __('Original national ID, Iqama or valid Passport with active Umrah permit is strictly mandatory.') }}</li>
                        <li>{{ __('Each traveler is permitted 1 standard luggage piece (max 25kg) and 1 personal handbag.') }}</li>
                        <li>{{ __('Present this physical or digital boarding pass to the driver/conductor before embarkation.') }}</li>
                    </ul>
                </div>
            </div>

            <!-- Zone B: Tear-Off Boarding Gate Stub (27%) -->
            <div class="ticket-stub">
                <div>
                    <!-- Stub Header -->
                    <div class="text-center pb-2 border-bottom mb-3">
                        <span class="badge bg-dark text-white extra-small text-uppercase px-2 py-1 mb-1">
                            {{ __('Gate Stub / قسيمة الصعود') }}
                        </span>
                        <div class="fw-bold text-dark small text-truncate">{{ $brandName }}</div>
                        <div class="font-monospace extra-small text-muted">{{ $booking->booking_reference }}</div>
                    </div>

                    <!-- Prominent Seat Callout -->
                    <div class="mb-3 text-center">
                        <span class="info-cell-label d-block text-center">{{ __('Seat # / رقم المقعد') }}</span>
                        <div class="seat-badge-stub my-1">
                            #{{ $booking->seat_number }}
                        </div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle extra-small">
                            {{ __('Confirmed Seat') }}
                        </span>
                    </div>

                    <!-- Passenger Stub Details -->
                    <div class="mb-2">
                        <span class="info-cell-label d-block">{{ __('Passenger / الراكب') }}</span>
                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.88rem;">{{ $booking->passenger_name }}</div>
                        <div class="extra-small text-muted font-monospace">{{ $booking->passenger_phone }}</div>
                    </div>

                    <div class="mb-2">
                        <span class="info-cell-label d-block">{{ __('Route / خط السير') }}</span>
                        <div class="fw-bold text-primary small">
                            {{ $trip ? $trip->departure_city : 'Origin' }} &rarr; {{ $trip ? $trip->destination_city : 'Destination' }}
                        </div>
                    </div>

                    <div class="mb-2">
                        <span class="info-cell-label d-block">{{ __('Departure / موعد الرحلة') }}</span>
                        <div class="fw-semibold text-dark extra-small">
                            {{ $trip && $trip->departure_date ? $trip->departure_date->format('d M Y - h:i A') : 'Scheduled' }}
                        </div>
                    </div>

                    <div class="mb-2">
                        <span class="info-cell-label d-block">{{ __('Package / نوع الباقة') }}</span>
                        <div class="extra-small fw-semibold text-dark">
                            @if($booking->package_type === 'with_hotel')
                                <i class="fas fa-hotel text-success me-1"></i> {{ __('With Hotel') }}
                            @else
                                <i class="fas fa-bus text-primary me-1"></i> {{ __('Seat Only') }}
                            @endif
                        </div>
                    </div>

                    @if($booking->balance_due > 0)
                        <div class="p-2 rounded bg-danger-subtle text-danger border border-danger-subtle extra-small fw-bold text-center mt-2">
                            {{ __('Due:') }} {{ format_currency($booking->balance_due) }}
                        </div>
                    @else
                        <div class="p-2 rounded bg-success-subtle text-success border border-success-subtle extra-small fw-bold text-center mt-2">
                            <i class="fas fa-check-circle me-1"></i> {{ __('Fare Paid') }}
                        </div>
                    @endif
                </div>

                <!-- Bottom Barcode & Verification -->
                <div class="pt-3 border-top text-center mt-3">
                    <div class="barcode-font">*{{ $booking->booking_reference }}*</div>
                    <div class="font-monospace extra-small text-muted" style="letter-spacing: 0.1em;">
                        {{ $booking->booking_reference }}
                    </div>
                    <div class="extra-small text-muted mt-1" style="font-size: 0.65rem;">
                        {{ __('Conductor Inspection Stub') }}
                    </div>
                </div>
            </div>

        </div>

        <!-- 3. Bottom Verification Strip -->
        <div class="p-2 px-4 bg-light border-top d-flex flex-wrap justify-content-between align-items-center extra-small text-muted">
            <div>
                <i class="fas fa-shield-alt text-success me-1"></i>
                <span>{{ __('Verified Official Boarding Document') }} &bull; {{ __('System Generated on') }}: {{ now()->format('d M Y, h:i A') }}</span>
            </div>
            <div class="text-end">
                <span>{{ __('Thank you for choosing :name. May Allah accept your Umrah!', ['name' => $brandName]) }}</span>
            </div>
        </div>
    </div>

</body>
</html>
