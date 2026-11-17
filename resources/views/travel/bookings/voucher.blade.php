@php
    $locale = request('lang', app()->getLocale());
    if (in_array($locale, ['ar', 'en'])) {
        app()->setLocale($locale);
    }
    $isAr = app()->getLocale() === 'ar';
    $dir = $isAr ? 'rtl' : 'ltr';

    $shop = $booking->shop;
    $setting = $shop?->setting ?: \App\Models\Setting::withoutGlobalScopes()->where('shop_id', $shop?->id)->first();
    $trip = $booking->trip;

    // Resolve Brand Name
    $brandName = shop_brand_name($shop, $setting);
    if (empty($brandName) || $brandName === 'CRE Quotation') {
        $brandName = $shop->name ?? 'Umrah & Travel Agency';
    }

    // High-reliability Base64 Logo Resolution
    $logoSrc = null;
    $logoPath = shop_logo_path($shop, $setting);
    if ($logoPath && file_exists($logoPath)) {
        $mime = @mime_content_type($logoPath) ?: 'image/jpeg';
        $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
    } elseif ($pPath = platform_logo_path()) {
        if (file_exists($pPath)) {
            $mime = @mime_content_type($pPath) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($pPath));
        }
    }
    if (!$logoSrc) {
        $logoSrc = shop_logo_url($shop, $setting);
    }

    // Company registration details
    $companyCr = $setting->company_cr_no ?? $shop->cr_number ?? null;
    $companyVat = $setting->company_vat_no ?? $shop->vat_number ?? null;
    $companyPhone = $setting->company_phone ?? $shop->phone ?? null;
    $companyEmail = $setting->company_email ?? $shop->email ?? null;
    $companyAddress = $setting->company_address ?? $shop->address ?? 'Kingdom of Saudi Arabia';
    $companyCity = $setting->company_city ?? '';

    // Group seats resolution
    $groupBookings = isset($groupBookings) && $groupBookings->isNotEmpty()
        ? $groupBookings
        : \App\Models\TravelBooking::where('booking_reference', $booking->booking_reference)
            ->where('travel_trip_id', $booking->travel_trip_id)
            ->where('booking_status', 'confirmed')
            ->orderBy('seat_number')
            ->get();

    $seatNumbers = $groupBookings->pluck('seat_number')->all();
    if (empty($seatNumbers)) {
        $seatNumbers = [$booking->seat_number];
    }
    $seatsCount = count($seatNumbers);
    $seatNumbersText = '#' . implode(', #', $seatNumbers);

    $totalTravelFare = (float) $groupBookings->sum('travel_fare');
    $totalHotelExpense = (float) $groupBookings->sum('hotel_expense');
    $totalExtraCharges = (float) $groupBookings->sum('extra_charges');
    $totalDiscount = (float) $groupBookings->sum('discount');
    $totalTaxAmount = (float) $groupBookings->sum('tax_amount');
    $totalVoucherAmount = (float) $groupBookings->sum('total_amount');
    $totalPaidAmount = (float) $groupBookings->sum('paid_amount');
    $totalBalanceDue = (float) $groupBookings->sum('balance_due');
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $dir }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Passenger Ticket') }} - {{ $booking->booking_reference }} - {{ $booking->passenger_name }}</title>
    
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    @if($isAr)
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    @endif
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts: Cairo & Libre Barcode 39 -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Libre+Barcode+39&family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand-primary: #00aeef;
            --brand-primary-dark: #008cc0;
            --brand-navy: #0F172A;
            --brand-navy-light: #1E293B;
            --brand-gold: #D97706;
            --brand-success: #10B981;
            --brand-danger: #EF4444;
            --brand-border: #CBD5E1;
            --brand-bg-soft: #F8FAFC;
        }

        body {
            font-family: {{ $isAr ? "'Cairo', sans-serif" : "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" }};
            background: #E2E8F0;
            color: #0F172A;
            margin: 0;
            padding: 16px 8px;
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
        }

        /* Toolbar on Screen */
        .print-toolbar {
            max-width: 620px;
            margin: 0 auto 12px auto;
            background: #FFFFFF;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid var(--brand-border);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
        }

        .lang-switch-btn {
            font-weight: 700;
            font-size: 0.8rem;
            padding: 4px 10px;
            border-radius: 6px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        /* A5 Compact Ticket Wrapper */
        .ticket-wrapper {
            max-width: 620px;
            margin: 0 auto;
            background: #FFFFFF;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.10);
            border: 1.5px solid var(--brand-primary-dark);
            overflow: hidden;
            position: relative;
        }

        /* Top Header */
        .ticket-header {
            padding: 10px 14px;
            background: #FFFFFF;
            border-bottom: 2px solid var(--brand-primary);
        }

        .agency-logo-img {
            max-height: 48px;
            max-width: 140px;
            object-fit: contain;
            display: block;
        }

        .agency-logo-placeholder {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--brand-navy) 0%, var(--brand-primary-dark) 100%);
            color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }

        .agency-title {
            font-size: 1.1rem;
            font-weight: 900;
            color: var(--brand-navy);
            line-height: 1.15;
            margin-bottom: 2px;
        }

        .agency-meta {
            font-size: 0.72rem;
            color: #475569;
            line-height: 1.25;
        }

        .pnr-badge-box {
            background: #F0F9FF;
            border: 1.5px solid #00aeef;
            padding: 4px 8px;
            border-radius: 6px;
            text-align: {{ $isAr ? 'left' : 'right' }};
        }

        .pnr-code {
            font-family: monospace;
            font-size: 0.98rem;
            font-weight: 800;
            color: var(--brand-primary-dark);
            letter-spacing: 0.5px;
            line-height: 1;
        }

        /* Journey Banner */
        .journey-banner {
            background: linear-gradient(135deg, #F0F9FF 0%, #E0F2FE 100%);
            border-bottom: 1px solid #BAE6FD;
            padding: 8px 14px;
        }

        .city-label {
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            color: #0369A1;
            letter-spacing: 0.5px;
            margin-bottom: 1px;
        }

        .city-name {
            font-size: 1.15rem;
            font-weight: 900;
            color: #0C4A6E;
            line-height: 1.15;
        }

        .journey-meta {
            font-size: 0.74rem;
            color: #334155;
            font-weight: 600;
        }

        /* Passenger & Seat Box */
        .passenger-seat-grid {
            padding: 8px 14px;
            border-bottom: 1px solid var(--brand-border);
            background: #FFFFFF;
        }

        .field-lbl {
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            color: #64748B;
            letter-spacing: 0.03em;
            margin-bottom: 1px;
        }

        .field-val {
            font-size: 0.88rem;
            font-weight: 800;
            color: var(--brand-navy);
            line-height: 1.2;
        }

        .seat-badge-hero {
            background: linear-gradient(135deg, #00aeef 0%, #008cc0 100%);
            color: #FFFFFF;
            padding: 6px 12px;
            border-radius: 6px;
            text-align: center;
            box-shadow: 0 3px 8px rgba(0, 174, 239, 0.25);
        }

        .seat-badge-hero .seat-txt {
            font-size: 1.25rem;
            font-weight: 900;
            letter-spacing: 0.5px;
            line-height: 1;
        }

        /* Package Details */
        .package-strip {
            padding: 5px 14px;
            background: #F8FAFC;
            border-bottom: 1px solid var(--brand-border);
            font-size: 0.75rem;
        }

        /* Financial Statement */
        .fare-table-card {
            padding: 8px 14px;
            background: #FFFFFF;
        }

        .ticket-table {
            width: 100%;
            margin-bottom: 0;
            font-size: 0.78rem;
            border-collapse: collapse;
        }

        .ticket-table th {
            background: #F1F5F9;
            color: #334155;
            font-weight: 700;
            font-size: 0.70rem;
            padding: 4px 8px;
            border: 1px solid #CBD5E1;
            text-transform: uppercase;
        }

        .ticket-table td {
            padding: 4px 8px;
            border: 1px solid #E2E8F0;
            vertical-align: middle;
        }

        .ticket-table .total-row td {
            background: #F8FAFC;
            font-weight: 800;
            font-size: 0.82rem;
            border-top: 1.5px solid #CBD5E1;
        }

        /* Passenger Guidelines */
        .guidelines-strip {
            padding: 6px 14px;
            background: #FFFBEB;
            border-top: 1px solid #FEF3C7;
            border-bottom: 1px solid #FEF3C7;
            font-size: 0.68rem;
            color: #92400E;
            line-height: 1.35;
        }

        /* Perforated Conductor Tear Stub */
        .cut-line {
            padding: 4px 14px;
            background: #F1F5F9;
            text-align: center;
            font-size: 0.65rem;
            font-weight: 700;
            color: #64748B;
            letter-spacing: 1px;
            border-top: 1.5px dashed #94A3B8;
            border-bottom: 1.5px dashed #94A3B8;
        }

        .conductor-stub {
            padding: 8px 14px;
            background: #FAFCFE;
            position: relative;
        }

        .stub-seat-box {
            background: var(--brand-navy);
            color: #FCD34D;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 1.05rem;
            font-weight: 900;
            text-align: center;
            line-height: 1.1;
        }

        .barcode-strip {
            font-family: 'Libre Barcode 39', cursive;
            font-size: 34px;
            letter-spacing: 2px;
            line-height: 0.85;
            color: var(--brand-navy);
            text-align: center;
            user-select: none;
            margin-top: 2px;
        }

        /* Bottom Security Footer */
        .ticket-footer {
            padding: 6px 14px;
            background: #F8FAFC;
            border-top: 1px solid var(--brand-border);
            font-size: 0.68rem;
            color: #64748B;
        }

        /* Print Media Styles: Guaranteed 1-page A5 print */
        @media print {
            @page {
                size: A5 portrait;
                margin: 4mm 5mm;
            }

            body {
                background: #FFFFFF !important;
                padding: 0 !important;
                margin: 0 !important;
                color: #000000 !important;
            }

            .print-toolbar {
                display: none !important;
            }

            .ticket-wrapper {
                max-width: 100% !important;
                width: 100% !important;
                box-shadow: none !important;
                border: 1px solid #334155 !important;
                border-radius: 4px !important;
                margin: 0 !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .ticket-header,
            .journey-banner,
            .seat-badge-hero,
            .stub-seat-box,
            .ticket-table th,
            .ticket-table .total-row td,
            .guidelines-strip,
            .badge {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>

    <!-- 1. Screen Toolbar (Hidden on Print) -->
    <div class="print-toolbar d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('travel.bookings.show', $booking->id) }}" class="btn btn-outline-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-{{ $isAr ? 'right' : 'left' }} me-1"></i> {{ __('Back') }}
            </a>
            <a href="{{ route('travel.bookings.index') }}" class="btn btn-light btn-sm border shadow-sm">
                <i class="fas fa-list me-1"></i> {{ __('Roster') }}
            </a>
        </div>

        <!-- Language Switcher -->
        <div class="d-flex align-items-center gap-1 bg-light p-1 rounded border">
            <a href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}" 
               class="lang-switch-btn {{ !$isAr ? 'bg-primary text-white' : 'text-dark' }}">
                English
            </a>
            <a href="{{ request()->fullUrlWithQuery(['lang' => 'ar']) }}" 
               class="lang-switch-btn {{ $isAr ? 'bg-primary text-white' : 'text-dark' }}">
                العربية
            </a>
        </div>

        <div class="d-flex align-items-center gap-2">
            <!-- Download A5 PDF via mPDF -->
            <a href="{{ route('travel.bookings.pdf', ['booking' => $booking->id, 'lang' => app()->getLocale()]) }}" 
               class="btn btn-danger btn-sm px-3 shadow-sm fw-bold">
                <i class="fas fa-file-pdf me-1"></i> {{ __('A5 PDF') }}
            </a>
            <!-- Browser Print A5 -->
            <button onclick="window.print()" class="btn btn-primary btn-sm px-3 shadow-sm fw-bold" style="background: var(--brand-primary); border-color: var(--brand-primary);">
                <i class="fas fa-print me-1"></i> {{ __('Print Ticket') }}
            </button>
            <button onclick="window.close()" class="btn btn-outline-dark btn-sm">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- 2. Main A5 Boarding Ticket (Faisal Movers / Intercity Bus Format) -->
    <div class="ticket-wrapper">

        <!-- Top Header: Agency Details + PNR Identifier -->
        <div class="ticket-header">
            <div class="row align-items-center g-2">
                <div class="col-7 d-flex align-items-center gap-2">
                    @if($logoSrc)
                        <img src="{{ $logoSrc }}" alt="{{ $brandName }}" class="agency-logo-img">
                    @else
                        <div class="agency-logo-placeholder">
                            <i class="fas fa-bus-alt"></i>
                        </div>
                    @endif
                    <div>
                        <div class="agency-title">{{ $brandName }}</div>
                        <div class="agency-meta">
                            <div><i class="fas fa-map-marker-alt text-danger me-1"></i> {{ tdb($companyAddress) }}@if($companyCity) - {{ tdb($companyCity) }}@endif</div>
                            <div>
                                @if($companyPhone) <span class="me-2"><i class="fas fa-phone-alt text-success me-1"></i> {{ $companyPhone }}</span> @endif
                                @if($companyCr) <span class="badge bg-light text-dark border p-0 px-1"><strong>{{ __('CR') }}:</strong> {{ $companyCr }}</span> @endif
                                @if($companyVat) <span class="badge bg-light text-dark border p-0 px-1"><strong>{{ __('VAT') }}:</strong> {{ $companyVat }}</span> @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-5">
                    <div class="pnr-badge-box">
                        <div class="text-uppercase fw-bold text-primary" style="font-size: 0.65rem;">
                            {{ __('Official Passenger Ticket') }}
                        </div>
                        <div class="pnr-code">{{ $booking->booking_reference }}</div>
                        <div class="text-muted" style="font-size: 0.62rem; margin-top: 1px;">
                            <i class="far fa-calendar-alt me-1"></i> {{ $booking->created_at->format('d M Y, h:i A') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Journey Banner: Origin -> Destination + Bus Fleet -->
        <div class="journey-banner">
            <div class="row align-items-center g-2 text-center text-md-{{ $isAr ? 'end' : 'start' }}">
                <div class="col-5">
                    <div class="city-label"><i class="fas fa-map-pin text-danger me-1"></i> {{ __('Origin / نقطة الانطلاق') }}</div>
                    <div class="city-name">{{ $trip ? tdb($trip->departure_city) : __('Departure City') }}</div>
                    <div class="journey-meta mt-1">
                        <i class="far fa-clock text-primary me-1"></i>
                        {{ $trip && $trip->departure_date ? $trip->departure_date->format('d M Y - h:i A') : __('Scheduled') }}
                    </div>
                </div>

                <div class="col-2 text-center">
                    <div class="text-primary fs-5"><i class="fas fa-bus"></i> &rarr;</div>
                    <span class="badge bg-primary text-white extra-small" style="font-size: 0.65rem;">
                        {{ $trip ? $trip->duration_days : 10 }} {{ __('Days') }}
                    </span>
                </div>

                <div class="col-5 text-md-{{ $isAr ? 'start' : 'end' }}">
                    <div class="city-label"><i class="fas fa-kaaba text-success me-1"></i> {{ __('Destination / الوجهة') }}</div>
                    <div class="city-name text-primary">{{ $trip ? tdb($trip->destination_city) : __('Destination') }}</div>
                    <div class="journey-meta mt-1">
                        <i class="fas fa-bus-alt text-secondary me-1"></i>
                        {{ $trip ? tdb($trip->bus_name) : __('Coach') }}
                        @if($trip && $trip->bus_number) &bull; <strong>{{ $trip->bus_number }}</strong> @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Passenger & Allocated Seats Grid -->
        <div class="passenger-seat-grid">
            <div class="row align-items-center g-2">
                <div class="col-5">
                    <div class="field-lbl"><i class="fas fa-user text-primary me-1"></i> {{ __('Passenger Name / اسم الراكب') }}</div>
                    <div class="field-val text-truncate">{{ tdb($booking->passenger_name) }}</div>
                    <div class="text-muted" style="font-size: 0.70rem;">
                        <i class="fas fa-phone-alt text-success me-1"></i> {{ $booking->passenger_phone }} &bull; {{ ucfirst(__($booking->passenger_gender)) }}
                    </div>
                </div>

                <div class="col-3">
                    <div class="field-lbl"><i class="fas fa-id-card text-info me-1"></i> {{ __('ID / Iqama / Passport') }}</div>
                    <div class="field-val font-monospace">{{ $booking->passenger_id_number ?: '—' }}</div>
                    <div class="text-muted" style="font-size: 0.70rem;">
                        {{ __('Trip') }}: <strong>{{ $trip ? $trip->trip_code : '—' }}</strong>
                    </div>
                </div>

                <div class="col-4 text-end">
                    <div class="seat-badge-hero">
                        <div class="text-uppercase text-white-50" style="font-size: 0.62rem; font-weight: 800;">
                            {{ $seatsCount > 1 ? __('Allocated Seats (' . $seatsCount . ')') : __('Allocated Seat') }}
                        </div>
                        <div class="seat-txt">{{ $seatNumbersText }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Package & Accommodation Strip -->
        <div class="package-strip d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <strong>{{ __('Package') }}:</strong>
                @if($booking->package_type === 'with_hotel')
                    <span class="badge bg-success text-white"><i class="fas fa-hotel me-1"></i> {{ __('With Hotel') }}</span>
                    <span class="fw-bold ms-1">{{ $booking->hotel_name ? tdb($booking->hotel_name) : ($trip->hotel_name ? tdb($trip->hotel_name) : __('Hotel Accommodation')) }}</span>
                    <span class="text-muted">({{ ucfirst(str_replace('_', ' ', $booking->room_type ?: 'quad_sharing')) }})</span>
                    @if($booking->nights_count) &bull; {{ $booking->nights_count }} {{ __('Nights') }} @endif
                @else
                    <span class="badge bg-primary text-white"><i class="fas fa-bus me-1"></i> {{ __('Seat Only (Transportation Only)') }}</span>
                @endif
            </div>
            <div class="text-muted font-monospace" style="font-size: 0.70rem;">
                {{ __('Issued') }}: {{ $booking->created_at->format('d/m/Y') }}
            </div>
        </div>

        <!-- Financial Statement (Itemized Table) -->
        <div class="fare-table-card">
            <table class="ticket-table">
                <thead>
                    <tr>
                        <th>{{ __('Description / البيان') }}</th>
                        <th class="text-center" style="width: 90px;">{{ __('Status') }}</th>
                        <th class="text-end" style="width: 120px;">{{ __('Amount') }} (SAR)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong>{{ __('Bus Seat Ticket Fare') }}</strong> 
                            ({{ $seatNumbersText }} - {{ $seatsCount }} {{ __('Seat(s)') }})
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-primary border p-0 px-1">{{ __('Confirmed') }}</span>
                        </td>
                        <td class="text-end font-monospace fw-bold">{{ format_currency($totalTravelFare) }}</td>
                    </tr>
                    @if($totalHotelExpense > 0)
                        <tr>
                            <td>{{ __('Hotel Accommodation Expense') }}</td>
                            <td class="text-center"><span class="badge bg-light text-success border p-0 px-1">{{ __('Reserved') }}</span></td>
                            <td class="text-end font-monospace fw-bold">{{ format_currency($totalHotelExpense) }}</td>
                        </tr>
                    @endif
                    @if($totalExtraCharges > 0)
                        <tr>
                            <td>{{ __('Extra Charges & Service Fees') }}</td>
                            <td class="text-center">—</td>
                            <td class="text-end font-monospace">+ {{ format_currency($totalExtraCharges) }}</td>
                        </tr>
                    @endif
                    @if($totalDiscount > 0)
                        <tr>
                            <td class="text-danger">{{ __('Promotional Discount') }}</td>
                            <td class="text-center">—</td>
                            <td class="text-end text-danger font-monospace">- {{ format_currency($totalDiscount) }}</td>
                        </tr>
                    @endif
                    <tr class="total-row">
                        <td>{{ __('Total Voucher Fare / إجمالي القيمة') }}</td>
                        <td class="text-center">
                            @if($booking->payment_status === 'paid')
                                <span class="badge bg-success text-white p-0 px-1">{{ __('PAID') }}</span>
                            @elseif($booking->payment_status === 'partial')
                                <span class="badge bg-warning text-dark p-0 px-1">{{ __('PARTIAL') }}</span>
                            @else
                                <span class="badge bg-danger text-white p-0 px-1">{{ __('UNPAID') }}</span>
                            @endif
                        </td>
                        <td class="text-end font-monospace text-primary fs-6">{{ format_currency($totalVoucherAmount) }}</td>
                    </tr>
                    <tr>
                        <td class="text-success"><i class="fas fa-check-circle me-1"></i> {{ __('Amount Paid / المبلغ المدفوع') }} ({{ ucfirst(str_replace('_', ' ', $booking->payment_method)) }})</td>
                        <td></td>
                        <td class="text-end text-success font-monospace fw-bold">{{ format_currency($totalPaidAmount) }}</td>
                    </tr>
                    @if($totalBalanceDue > 0)
                        <tr class="table-warning fw-bold text-danger">
                            <td><i class="fas fa-exclamation-triangle me-1"></i> {{ __('Balance Due at Bus / المتبقي للتحصيل') }}</td>
                            <td class="text-center"><span class="badge bg-danger text-white p-0 px-1">{{ __('Collect') }}</span></td>
                            <td class="text-end font-monospace">{{ format_currency($totalBalanceDue) }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Passenger Guidelines Strip -->
        <div class="guidelines-strip">
            <strong><i class="fas fa-info-circle me-1"></i> {{ __('Travel Guidelines') }}:</strong>
            1. {{ __('Arrive at departure terminal 30-45 mins before departure.') }}
            &bull; 2. {{ __('Original Iqama/Passport is mandatory.') }}
            &bull; 3. {{ __('Baggage limit: 1 piece (max 25kg) + 1 hand luggage.') }}
        </div>

        <!-- Perforated Cut Line -->
        <div class="cut-line">
            ✂ - - - - - - - - - - - - - - - {{ __('TEAR-OFF CONDUCTOR STUB / قسيمة السائق') }} - - - - - - - - - - - - - - - ✂
        </div>

        <!-- Conductor Inspection Tear Stub -->
        <div class="conductor-stub">
            <div class="row align-items-center g-2">
                <div class="col-5">
                    <div class="fw-bold text-dark small text-truncate">{{ $brandName }}</div>
                    <div style="font-size: 0.72rem;"><strong>{{ __('Passenger') }}:</strong> {{ tdb($booking->passenger_name) }}</div>
                    <div style="font-size: 0.72rem;"><strong>{{ __('Phone') }}:</strong> {{ $booking->passenger_phone }}</div>
                    <div style="font-size: 0.72rem;">
                        <strong>{{ __('Route') }}:</strong> {{ $trip ? tdb($trip->departure_city) : '' }} &rarr; {{ $trip ? tdb($trip->destination_city) : '' }}
                    </div>
                </div>

                <div class="col-4 text-center">
                    <div class="extra-small text-muted text-uppercase fw-bold">{{ __('Allocated Seat(s)') }}</div>
                    <div class="stub-seat-box">
                        {{ $seatNumbersText }}
                    </div>
                    <div class="mt-1" style="font-size: 0.70rem;">
                        @if($totalBalanceDue > 0)
                            <span class="badge bg-danger text-white">{{ __('DUE') }}: {{ format_currency($totalBalanceDue) }}</span>
                        @else
                            <span class="badge bg-success text-white">{{ __('FARE PAID') }}</span>
                        @endif
                    </div>
                </div>

                <div class="col-3 text-end">
                    <div class="p-1 border rounded bg-white text-center" style="font-size: 0.65rem;">
                        <i class="far fa-square text-primary"></i> <strong>{{ __('Boarded / صعد') }}</strong>
                        <div class="text-muted mt-2 border-top pt-1" style="font-size: 0.60rem;">{{ __('Conductor Sig') }}</div>
                    </div>
                </div>

                <!-- Code 39 Barcode (NO QR CODE) -->
                <div class="col-12 text-center pt-1">
                    <div class="barcode-strip">*{{ $booking->booking_reference }}*</div>
                    <div class="font-monospace text-muted" style="font-size: 0.68rem; letter-spacing: 1px;">
                        {{ $booking->booking_reference }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Verification Footer -->
        <div class="ticket-footer d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-shield-alt text-success me-1"></i>
                <span>{{ __('Verified Official Travel Voucher') }}</span>
            </div>
            <div>
                <span>{{ __('Thank you for choosing :name', ['name' => $brandName]) }}</span>
            </div>
        </div>

    </div>

</body>
</html>
