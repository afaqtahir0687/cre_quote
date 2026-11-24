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
    $companyCr = $shop->cr_number ?? $setting->company_cr_no ?? null;
    $companyLicense = $shop->license_number ?? $setting->company_license_no ?? null;
    $companyTagline = $shop->tagline ?? $setting->company_tagline ?? null;
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
    
    <!-- Google Fonts: Cairo & Amiri -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Amiri:wght@400;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: {{ $isAr ? "'Cairo', 'Amiri', sans-serif" : "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" }};
            background: #E2E8F0;
            color: #0F172A;
            margin: 0;
            padding: 16px 8px;
            -webkit-font-smoothing: antialiased;
        }

        /* Screen Print Toolbar */
        .print-toolbar {
            max-width: 600px;
            margin: 0 auto 12px auto;
            background: #FFFFFF;
            padding: 10px 14px;
            border-radius: 8px;
            border: 1px solid #CBD5E1;
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

        /* A5 Ticket Card */
        .ticket-box {
            max-width: 600px;
            margin: 0 auto;
            border: 1.5px solid #008cc0;
            padding: 8px 10px;
            background-color: #ffffff;
            border-radius: 4px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
            font-size: 9pt;
            line-height: 1.25;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td, th {
            vertical-align: top;
            padding: 2px 4px;
        }

        /* Header */
        .header-table td {
            vertical-align: middle;
        }

        .agency-title {
            font-size: 13pt;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
            line-height: 1.1;
        }

        .agency-meta {
            font-size: 7.5pt;
            color: #475569;
            line-height: 1.25;
        }

        .pnr-badge {
            display: inline-block;
            background-color: #008cc0;
            color: #ffffff;
            font-weight: bold;
            font-size: 10pt;
            padding: 3px 8px;
            border-radius: 4px;
            letter-spacing: 0.5px;
        }

        /* Route Banner */
        .route-table {
            margin-top: 5px;
            background-color: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 4px;
        }

        .route-table td {
            padding: 4px 6px;
        }

        .city-label {
            font-size: 7pt;
            color: #0369a1;
            font-weight: bold;
            text-transform: uppercase;
        }

        .city-title {
            font-size: 11pt;
            font-weight: bold;
            color: #0c4a6e;
        }

        .trip-meta {
            font-size: 7.5pt;
            color: #334155;
        }

        /* Passenger & Seat Box */
        .info-grid-table {
            margin-top: 5px;
            border: 1px solid #e2e8f0;
            background-color: #fafafa;
        }

        .info-grid-table td {
            padding: 4px 6px;
            border: 1px solid #e2e8f0;
            font-size: 8pt;
        }

        .field-lbl {
            font-size: 7pt;
            color: #64748b;
            font-weight: bold;
            text-transform: uppercase;
        }

        .field-val {
            font-size: 8.5pt;
            font-weight: bold;
            color: #0f172a;
        }

        .seat-numbers-text {
            font-size: 13pt;
            font-weight: bold;
            color: #008cc0;
            line-height: 1;
        }

        /* Financial Statement */
        .fare-table {
            margin-top: 5px;
            border: 1px solid #cbd5e1;
        }

        .fare-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-size: 7.5pt;
            font-weight: bold;
            border: 1px solid #cbd5e1;
            padding: 3px 5px;
            text-transform: uppercase;
        }

        .fare-table td {
            border: 1px solid #e2e8f0;
            padding: 3px 5px;
            font-size: 8pt;
        }

        .fare-total-row td {
            background-color: #f8fafc;
            font-weight: bold;
            font-size: 8.5pt;
        }

        /* Guidelines */
        .rules-box {
            margin-top: 4px;
            background-color: #fffbeb;
            border: 1px solid #fef3c7;
            padding: 3px 6px;
            font-size: 7pt;
            color: #92400e;
            line-height: 1.25;
        }

        /* Conductor Tear Stub */
        .cut-line {
            border-top: 1.5px dashed #94a3b8;
            margin: 6px 0 5px 0;
            text-align: center;
            font-size: 7pt;
            color: #64748b;
            letter-spacing: 1px;
        }

        .stub-box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
        }

        .stub-table td {
            padding: 2px 4px;
            font-size: 7.5pt;
        }

        .stub-seat-box {
            background-color: #0f172a;
            color: #fcd34d;
            font-size: 11pt;
            font-weight: bold;
            text-align: center;
            padding: 3px 6px;
            border-radius: 3px;
        }

        .footer-note {
            text-align: center;
            font-size: 6.5pt;
            color: #94a3b8;
            margin-top: 3px;
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

            .ticket-box {
                max-width: 100% !important;
                width: 100% !important;
                box-shadow: none !important;
                border: 1.5px solid #008cc0 !important;
                padding: 6px 8px !important;
                margin: 0 !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .pnr-badge,
            .route-table,
            .info-grid-table,
            .fare-table th,
            .fare-total-row td,
            .rules-box,
            .stub-box,
            .stub-seat-box {
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
                <i class="fas fa-file-pdf me-1"></i> {{ __('Download A5 PDF') }}
            </a>
            <!-- Browser Print A5 -->
            <button onclick="window.print()" class="btn btn-primary btn-sm px-3 shadow-sm fw-bold" style="background: #00aeef; border-color: #00aeef;">
                <i class="fas fa-print me-1"></i> {{ __('Print Ticket (A5)') }}
            </button>
            <button onclick="window.close()" class="btn btn-outline-dark btn-sm">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- 2. Main A5 Boarding Ticket Box -->
    <div class="ticket-box">

        <!-- Top Header: Logo + Agency Details + PNR -->
        <table class="header-table">
            <tr>
                <td style="width: 50%; vertical-align: middle;">
                    <table style="width: 100%;">
                        <tr>
                            @if($logoSrc)
                                <td style="width: 85px; vertical-align: middle; padding: 0; text-align: center;">
                                    <img src="{{ $logoSrc }}" style="max-height: 60px; max-width: 80px; object-fit: contain;" alt="Logo" />
                                </td>
                            @endif
                            <td style="vertical-align: middle; padding-{{ $isAr ? 'right' : 'left' }}: 8px;">
                                <div class="agency-title">{{ $brandName }}</div>
                                @if($companyTagline)
                                    <div style="font-size: 6.8pt; color: #0284c7; font-style: italic; margin-top: -1px; margin-bottom: 2px; font-weight: 500;">{{ tdb($companyTagline) }}</div>
                                @endif
                                <div class="agency-meta">
                                    {{ tdb($companyAddress) }}@if($companyCity) - {{ tdb($companyCity) }}@endif
                                    @if($companyPhone) &bull; {{ __('Tel') }}: <span dir="ltr">{{ $companyPhone }}</span>@endif
                                    @if($companyCr || $companyLicense || $companyVat)
                                        <br>
                                        @if($companyCr) <strong>{{ __('CR') }}:</strong> <span dir="ltr">{{ $companyCr }}</span> @endif
                                        @if($companyLicense) &bull; <strong>{{ __('License') }}:</strong> <span dir="ltr">{{ $companyLicense }}</span> @endif
                                        @if($companyVat) &bull; <strong>{{ __('VAT') }}:</strong> <span dir="ltr">{{ $companyVat }}</span> @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width: 50%; vertical-align: middle; text-align: {{ $isAr ? 'left' : 'right' }};">
                    <div style="font-size: 6.8pt; font-weight: bold; color: #008cc0; text-transform: uppercase; white-space: nowrap;">
                        {{ __('PASSENGER TICKET & BOARDING PASS') }}
                    </div>
                    <div style="font-size: 6.5pt; color: #64748b; margin-top: 1px;">
                        {{ __('Booking Reference / رقم الحجز') }}
                    </div>
                    <div class="pnr-badge" dir="ltr">
                        {{ $booking->booking_reference }}
                    </div>
                    <div style="font-size: 6.5pt; color: #64748b; margin-top: 2px;">
                        {{ __('Date') }}: <span dir="ltr">{{ $booking->created_at->format('d M Y, h:i A') }}</span>
                        &bull; <strong>{{ __('Agent') }}:</strong> {{ tdb($booking->effective_salesman) }}@if($booking->effective_salesman_phone) (<span dir="ltr">{{ $booking->effective_salesman_phone }}</span>)@endif
                    </div>
                </td>
            </tr>
        </table>

        <!-- Journey Banner: Origin -> Destination + Bus Fleet -->
        <table class="route-table">
            <tr>
                <td style="width: 42%;">
                    <div class="city-label">{{ __('Origin / نقطة الانطلاق') }}</div>
                    <div class="city-title">{{ $trip ? tdb($trip->departure_city) : __('Departure City') }}</div>
                    <div class="trip-meta" style="font-size: 6.5pt; white-space: nowrap;">
                        <strong>{{ __('Boarding') }}:</strong> {{ $booking->effective_pickup_location }}
                    </div>
                </td>
                <td style="width: 16%; text-align: center; vertical-align: middle;">
                    @if($booking->isOneWay())
                        <div style="font-size: 10pt; font-weight: bold; color: #0284c7;">{!! $isAr ? '&larr;' : '&rarr;' !!}</div>
                        <div style="font-size: 5.8pt; font-weight: bold; color: #0369a1; background: #e0f2fe; padding: 1px 4px; border-radius: 3px; white-space: nowrap;">
                            {{ __('ONE WAY / ذهاب') }}
                        </div>
                    @else
                        <div style="font-size: 10pt; font-weight: bold; color: #059669;">&#8644;</div>
                        <div style="font-size: 5.8pt; font-weight: bold; color: #047857; background: #d1fae5; padding: 1px 4px; border-radius: 3px; white-space: nowrap;">
                            {{ __('ROUND / عودة') }} ({{ $trip ? $trip->duration_days : 10 }}D)
                        </div>
                    @endif
                </td>
                <td style="width: 42%; text-align: {{ $isAr ? 'left' : 'right' }};">
                    <div class="city-label">{{ __('Destination / الوجهة') }}</div>
                    <div class="city-title">{{ $trip ? tdb($trip->destination_city) : __('Destination') }}</div>
                    <div class="trip-meta" style="font-size: 6.5pt; white-space: nowrap;">
                        @if($booking->dropoff_location)
                            <strong style="color: #b91c1c;">{{ __('Drop-off') }}:</strong> {{ $booking->dropoff_location }}
                        @else
                            <strong>{{ __('Coach Fleet') }}:</strong> {{ $trip ? tdb($trip->bus_name) : __('Luxury Coach') }}
                        @endif
                        @if($booking->isRoundTrip() && $booking->return_dropoff_location)
                            <br><strong style="color: #b45309;">{{ __('Return Drop') }}:</strong> {{ $booking->return_dropoff_location }}
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <!-- Passenger Grid -->
        <table class="info-grid-table">
            <tr>
                <td style="width: 55%;">
                    <div class="field-lbl">{{ $seatsCount > 1 ? __('Lead Booker / المسافر الرئيسي') : __('Passenger Name / اسم الراكب') }}</div>
                    <div class="field-val" style="white-space: nowrap; font-size: 8pt;">{{ tdb($booking->passenger_name) }}</div>
                    <div style="font-size: 7pt; color: #64748b; white-space: nowrap;">
                        {{ __('Phone') }}: <span dir="ltr">{{ $booking->passenger_phone }}</span> &bull; {{ $booking->passenger_gender === 'female' ? __('Female') : __('Male') }}
                    </div>
                </td>
                <td style="width: 45%;">
                    <div class="field-lbl">{{ __('ID / Iqama / Passport') }}</div>
                    <div class="field-val" style="font-family: monospace; font-size: 8pt;">{{ $booking->passenger_id_number ?: '—' }}</div>
                    <div style="font-size: 7pt; color: #64748b;">
                        {{ __('Trip Code') }}: <span dir="ltr">{{ $trip ? $trip->trip_code : '—' }}</span>
                        @if($seatsCount == 1)
                            &bull; <strong style="color: #008cc0;">{{ __('Seat') }}: <span dir="ltr">#{{ $booking->seat_number }} ({{ strtoupper($booking->seat_class ?: 'ECO') }})</span></strong>
                        @endif
                    </div>
                </td>
            </tr>
            @if($seatsCount > 1)
                <tr>
                    <td colspan="2" style="padding: 0; border-top: 1px solid #bae6fd;">
                        @if($seatsCount <= 4)
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr style="background-color: #f0f9ff;">
                                    <th style="width: 12%; font-size: 6.5pt; color: #0369a1; text-align: center; padding: 2px 4px; border: 1px solid #e0f2fe;">{{ __('Seat #') }}</th>
                                    <th style="width: 46%; font-size: 6.5pt; color: #0369a1; text-align: {{ $isAr ? 'right' : 'left' }}; padding: 2px 4px; border: 1px solid #e0f2fe;">{{ __('Passenger Name / اسم الراكب') }}</th>
                                    <th style="width: 16%; font-size: 6.5pt; color: #0369a1; text-align: center; padding: 2px 4px; border: 1px solid #e0f2fe;">{{ __('Gender') }}</th>
                                    <th style="width: 26%; font-size: 6.5pt; color: #0369a1; text-align: {{ $isAr ? 'left' : 'right' }}; padding: 2px 4px; border: 1px solid #e0f2fe;">{{ __('ID / Iqama') }}</th>
                                </tr>
                                @foreach($groupBookings as $gb)
                                    <tr>
                                        <td style="text-align: center; font-weight: bold; color: #008cc0; font-size: 6.8pt; padding: 1.5px 4px; border: 1px solid #e2e8f0;">
                                            <span dir="ltr">#{{ $gb->seat_number }} ({{ strtoupper($gb->seat_class ?: 'ECO') }})</span>
                                        </td>
                                        <td style="font-weight: bold; font-size: 6.8pt; padding: 1.5px 4px; border: 1px solid #e2e8f0; white-space: nowrap;">
                                            {{ tdb($gb->passenger_name) }}
                                        </td>
                                        <td style="text-align: center; font-size: 6.5pt; padding: 1.5px 4px; border: 1px solid #e2e8f0;">
                                            {{ $gb->passenger_gender === 'female' ? __('Female') : __('Male') }}
                                        </td>
                                        <td style="font-family: monospace; font-size: 6.5pt; text-align: {{ $isAr ? 'left' : 'right' }}; padding: 1.5px 4px; border: 1px solid #e2e8f0;">
                                            {{ $gb->passenger_id_number ?: '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        @else
                            {{-- 2-column layout for 5 or more passengers --}}
                            @php
                                $chunks = $groupBookings->chunk(2);
                            @endphp
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr style="background-color: #f0f9ff;">
                                    <th style="width: 8%; font-size: 6pt; color: #0369a1; text-align: center; padding: 1.5px 2px; border: 1px solid #e0f2fe;">{{ __('Seat') }}</th>
                                    <th style="width: 26%; font-size: 6pt; color: #0369a1; text-align: {{ $isAr ? 'right' : 'left' }}; padding: 1.5px 2px; border: 1px solid #e0f2fe;">{{ __('Passenger Name') }}</th>
                                    <th style="width: 16%; font-size: 6pt; color: #0369a1; text-align: {{ $isAr ? 'left' : 'right' }}; padding: 1.5px 2px; border: 1px solid #e0f2fe;">{{ __('ID / Iqama') }}</th>
                                    <th style="width: 8%; font-size: 6pt; color: #0369a1; text-align: center; padding: 1.5px 2px; border: 1px solid #e0f2fe;">{{ __('Seat') }}</th>
                                    <th style="width: 26%; font-size: 6pt; color: #0369a1; text-align: {{ $isAr ? 'right' : 'left' }}; padding: 1.5px 2px; border: 1px solid #e0f2fe;">{{ __('Passenger Name') }}</th>
                                    <th style="width: 16%; font-size: 6pt; color: #0369a1; text-align: {{ $isAr ? 'left' : 'right' }}; padding: 1.5px 2px; border: 1px solid #e0f2fe;">{{ __('ID / Iqama') }}</th>
                                </tr>
                                @foreach($chunks as $chunk)
                                    @php
                                        $p1 = $chunk->values()->get(0);
                                        $p2 = $chunk->values()->get(1);
                                    @endphp
                                    <tr>
                                        <td style="text-align: center; font-weight: bold; color: #008cc0; font-size: 6.5pt; padding: 1px 2px; border: 1px solid #e2e8f0;">
                                            <span dir="ltr">#{{ $p1->seat_number }}</span>
                                        </td>
                                        <td style="font-weight: bold; font-size: 6.2pt; padding: 1px 2px; border: 1px solid #e2e8f0; white-space: nowrap;">
                                            {{ tdb($p1->passenger_name) }}
                                        </td>
                                        <td style="font-family: monospace; font-size: 5.8pt; text-align: {{ $isAr ? 'left' : 'right' }}; padding: 1px 2px; border: 1px solid #e2e8f0;">
                                            {{ $p1->passenger_id_number ?: '—' }}
                                        </td>
                                        @if($p2)
                                            <td style="text-align: center; font-weight: bold; color: #008cc0; font-size: 6.5pt; padding: 1px 2px; border: 1px solid #e2e8f0;">
                                                <span dir="ltr">#{{ $p2->seat_number }}</span>
                                            </td>
                                            <td style="font-weight: bold; font-size: 6.2pt; padding: 1px 2px; border: 1px solid #e2e8f0; white-space: nowrap;">
                                                {{ tdb($p2->passenger_name) }}
                                            </td>
                                            <td style="font-family: monospace; font-size: 5.8pt; text-align: {{ $isAr ? 'left' : 'right' }}; padding: 1px 2px; border: 1px solid #e2e8f0;">
                                                {{ $p2->passenger_id_number ?: '—' }}
                                            </td>
                                        @else
                                            <td colspan="3" style="border: 1px solid #e2e8f0; background-color: #fafafa;"></td>
                                        @endif
                                    </tr>
                                @endforeach
                            </table>
                        @endif
                    </td>
                </tr>
            @endif
            <tr>
                <td colspan="2" style="background-color: #f8fafc; padding: 3px 6px; border-top: 1px solid #bae6fd;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="width: 65%; vertical-align: top; padding: 0;">
                                <span class="field-lbl" style="font-size: 6pt; color: #0369a1; text-transform: uppercase;">{{ __('Package & Accommodation / الفندق والإقامة') }}:</span>
                                @if($booking->package_type === 'with_hotel')
                                    <div style="font-size: 7.5pt; font-weight: bold; color: #065f46; margin-top: 1px;">
                                        <strong>{{ __('Hotel / الفندق') }}:</strong> {{ tdb($booking->effective_hotel_name ?: __('Hotel Accommodation')) }}
                                        <span style="font-weight: normal; font-size: 6.8pt; color: #334155;">({{ $booking->roomTypeLabel() }})</span>
                                        @if($booking->nights_count) - {{ $booking->nights_count }} {{ __('Nights') }} @endif
                                    </div>
                                    @if($booking->effective_hotel_address)
                                        <div style="font-size: 6.5pt; color: #475569; margin-top: 1px;">
                                            <strong>{{ __('Hotel Address / عنوان الفندق') }}:</strong>
                                            {{ tdb($booking->effective_hotel_address) }}
                                        </div>
                                    @endif
                                @else
                                    <div style="font-size: 7.5pt; font-weight: bold; color: #1e40af; margin-top: 1px;">
                                        {{ __('Seat Only (Transportation Only / مقعد فقط بدون فندق)') }}
                                    </div>
                                @endif
                            </td>
                            <td style="width: 35%; text-align: {{ $isAr ? 'left' : 'right' }}; vertical-align: top; padding: 0;">
                                <div style="font-size: 6pt; color: #64748b; text-transform: uppercase;">{{ __('Salesman / مسؤول الحجز') }}:</div>
                                <div style="font-size: 7.5pt; font-weight: bold; color: #0f172a; margin-top: 1px;">
                                    {{ tdb($booking->effective_salesman) }}
                                </div>
                                @if($booking->effective_salesman_phone)
                                    <div style="font-size: 6.8pt; color: #0284c7; margin-top: 1px;">
                                        <span dir="ltr">{{ $booking->effective_salesman_phone }}</span>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Itemized Financial Breakdown (SAR) -->
        <table class="fare-table">
            <thead>
                <tr>
                    <th style="text-align: {{ $isAr ? 'right' : 'left' }};">{{ __('Description / البيان') }}</th>
                    <th style="width: 100px; text-align: center;">{{ __('Status / الحالة') }}</th>
                    <th style="width: 120px; text-align: {{ $isAr ? 'left' : 'right' }};">{{ __('Amount / المبلغ') }} (SAR)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        {{ __('Bus Seat Ticket Fare') }} (<span dir="ltr">{{ $seatNumbersText }}</span> - {{ $seatsCount }} {{ $seatsCount > 1 ? __('Seat(s)') : __('Seat') }})
                    </td>
                    <td style="text-align: center; font-size: 7.5pt; color: #0284c7;">
                        {{ __('Confirmed') }}
                    </td>
                    <td style="text-align: {{ $isAr ? 'left' : 'right' }}; font-weight: bold;">
                        <span dir="ltr">{{ format_currency($totalTravelFare) }}</span>
                    </td>
                </tr>
                @if($totalHotelExpense > 0)
                    <tr>
                        <td>{{ __('Hotel Accommodation Expense') }}</td>
                        <td style="text-align: center; font-size: 7.5pt; color: #059669;">{{ __('Reserved') }}</td>
                        <td style="text-align: {{ $isAr ? 'left' : 'right' }}; font-weight: bold;"><span dir="ltr">{{ format_currency($totalHotelExpense) }}</span></td>
                    </tr>
                @endif
                @if($totalExtraCharges > 0)
                    <tr>
                        <td>{{ __('Extra Charges & Service Fees') }}</td>
                        <td style="text-align: center;">—</td>
                        <td style="text-align: {{ $isAr ? 'left' : 'right' }};">+ <span dir="ltr">{{ format_currency($totalExtraCharges) }}</span></td>
                    </tr>
                @endif
                @if($totalDiscount > 0)
                    <tr>
                        <td style="color: #b91c1c;">{{ __('Promotional Discount') }}</td>
                        <td style="text-align: center;">—</td>
                        <td style="text-align: {{ $isAr ? 'left' : 'right' }}; color: #b91c1c;">- <span dir="ltr">{{ format_currency($totalDiscount) }}</span></td>
                    </tr>
                @endif
                <tr class="fare-total-row">
                    <td>{{ __('Total Voucher Fare / إجمالي القيمة') }}</td>
                    <td style="text-align: center;">
                        @if($booking->payment_status === 'paid')
                            <strong style="color: #16a34a;">{{ __('PAID') }}</strong>
                        @elseif($booking->payment_status === 'partial')
                            <strong style="color: #d97706;">{{ __('PARTIAL') }}</strong>
                        @else
                            <strong style="color: #dc2626;">{{ __('UNPAID') }}</strong>
                        @endif
                    </td>
                    <td style="text-align: {{ $isAr ? 'left' : 'right' }}; color: #008cc0;">
                        <span dir="ltr">{{ format_currency($totalVoucherAmount) }}</span>
                    </td>
                </tr>
                <tr>
                    <td style="color: #16a34a;">
                        {{ __('Amount Paid / المبلغ المدفوع') }} ({{ __($booking->payment_method ?: 'cash') }})
                    </td>
                    <td></td>
                    <td style="text-align: {{ $isAr ? 'left' : 'right' }}; color: #16a34a; font-weight: bold;">
                        <span dir="ltr">{{ format_currency($totalPaidAmount) }}</span>
                    </td>
                </tr>
                @if($totalBalanceDue > 0)
                    <tr style="background-color: #fef2f2; font-weight: bold;">
                        <td style="color: #dc2626;">
                            {{ __('Balance Due at Bus / المتبقي للتحصيل عند الحافلة') }}
                        </td>
                        <td style="text-align: center; color: #dc2626; font-size: 7.5pt;">{{ __('Collect') }}</td>
                        <td style="text-align: {{ $isAr ? 'left' : 'right' }}; color: #dc2626;">
                            <span dir="ltr">{{ format_currency($totalBalanceDue) }}</span>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- Important Guidelines -->
        <div class="rules-box">
            <strong>{{ __('Travel Guidelines') }}:</strong>
            1. {{ __('Arrive at the departure terminal at least 30-45 minutes before departure.') }}
            2. {{ __('Original Iqama/Passport is mandatory.') }}
            3. {{ __('Baggage limit: 1 standard piece (max 25kg) + 1 hand luggage.') }}
            4. {{ __('Madinah historical Ziyarat tour is optional with separate charges of SAR 10 per passenger.') }}
        </div>

        @php
            $tripConditions = $trip?->description ?: \App\Models\TravelTrip::defaultConditions();
            $conditionBlocks = \App\Models\TravelTrip::parseConditionsBlocks($tripConditions);
            $rtlBlocks = array_values(array_filter($conditionBlocks, fn($b) => $b['type'] === 'rtl'));
            $ltrBlocks = array_values(array_filter($conditionBlocks, fn($b) => $b['type'] === 'ltr'));
            $hasBoth = !empty($rtlBlocks) && !empty($ltrBlocks);
        @endphp
        @if(!empty($conditionBlocks))
            <!-- Trip Overview / Terms & Conditions Note -->
            <div class="trip-overview-box" style="margin-top: 3px; background-color: #f8fafc; border: 1px solid #cbd5e1; border-{{ $isAr ? 'right' : 'left' }}: 3px solid #0284c7; padding: 4px 6px; font-size: 6.8pt; color: #1e293b; line-height: 1.25; border-radius: 2px;">
                <div style="font-weight: bold; color: #0369a1; margin-bottom: 2px;">
                    <i class="fas fa-file-contract me-1"></i>{{ __('Terms & Conditions / الشروط والأحكام') }}:
                </div>
                @if($hasBoth)
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="width: 50%; vertical-align: top; padding: 0 6px 0 0; direction: ltr; text-align: left; font-size: 6.5pt; color: #334155; line-height: 1.25; border-right: 1px dashed #cbd5e1;">
                                @foreach($ltrBlocks as $block)
                                    <div>{!! nl2br(e(implode("\n", $block['lines']))) !!}</div>
                                @endforeach
                            </td>
                            <td style="width: 50%; vertical-align: top; padding: 0 0 0 6px; direction: rtl; text-align: right; font-family: 'Cairo', 'Amiri', 'Traditional Arabic', Tahoma, sans-serif; font-size: 6.5pt; color: #334155; line-height: 1.25;">
                                @foreach($rtlBlocks as $block)
                                    <div dir="rtl" style="direction: rtl; text-align: right;">{!! nl2br(e(implode("\n", $block['lines']))) !!}</div>
                                @endforeach
                            </td>
                        </tr>
                    </table>
                @else
                    @foreach($conditionBlocks as $block)
                        @if($block['type'] === 'rtl')
                            <div dir="rtl" style="direction: rtl; text-align: right; font-family: 'Cairo', 'Amiri', 'Traditional Arabic', Tahoma, sans-serif; font-size: 6.5pt; color: #334155; margin-bottom: 3px;">
                                {!! nl2br(e(implode("\n", $block['lines']))) !!}
                            </div>
                        @else
                            <div dir="ltr" style="direction: ltr; text-align: left; font-size: 6.5pt; color: #334155; margin-bottom: 3px;">
                                {!! nl2br(e(implode("\n", $block['lines']))) !!}
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>
        @endif

        <!-- Perforation Cut Line -->
        <div class="cut-line">
            ✂ - - - - - - - - - - - - - - - - - - - - {{ __('TEAR-OFF CONDUCTOR STUB / قسيمة السائق') }} - - - - - - - - - - - - - - - - - - - - ✂
        </div>

        <!-- Tear-Off Conductor Inspection Counterfoil (NO BARCODE LINES) -->
        <div class="stub-box">
            <table class="stub-table">
                <tr>
                    <td style="width: 48%;">
                        <div style="font-weight: bold; font-size: 7.5pt; color: #0f172a;">{{ $brandName }}</div>
                        @if($seatsCount > 1)
                            <div style="font-size: 6.5pt; white-space: nowrap;"><strong>{{ __('Booker') }}:</strong> {{ tdb($booking->passenger_name) }}</div>
                            @if($seatsCount <= 3)
                                <div style="font-size: 5.8pt; color: #334155; margin-bottom: 1px;">
                                    @foreach($groupBookings as $gb)
                                        <span style="white-space: nowrap; margin-{{ $isAr ? 'left' : 'right' }}: 4px;"><span dir="ltr">#{{ $gb->seat_number }}</span>: {{ tdb($gb->passenger_name) }}</span>
                                    @endforeach
                                </div>
                            @else
                                <div style="font-size: 5.8pt; color: #334155; margin-bottom: 1px; white-space: nowrap;">
                                    <strong>{{ __('Group') }}:</strong> {{ $seatsCount }} {{ __('Passengers Booked') }}
                                </div>
                            @endif
                        @else
                            <div style="font-size: 6.5pt; white-space: nowrap;"><strong>{{ __('Passenger') }}:</strong> {{ tdb($booking->passenger_name) }}</div>
                        @endif
                        <div style="font-size: 6.5pt; white-space: nowrap;"><strong>{{ __('Phone') }}:</strong> <span dir="ltr">{{ $booking->passenger_phone }}</span></div>
                        <div style="font-size: 6.5pt; white-space: nowrap;"><strong>{{ __('Route') }}:</strong> {{ $trip ? tdb($trip->departure_city) : '' }} {!! $isAr ? '&larr;' : '&rarr;' !!} {{ $trip ? tdb($trip->destination_city) : '' }}</div>
                        <div style="font-size: 6.2pt; color: #475569; white-space: nowrap;"><strong>{{ __('Agent') }}:</strong> {{ tdb($booking->effective_salesman) }}@if($booking->effective_salesman_phone) (<span dir="ltr">{{ $booking->effective_salesman_phone }}</span>)@endif @if($booking->package_type === 'with_hotel' && $booking->effective_hotel_name) &bull; <strong>{{ __('Hotel') }}:</strong> {{ tdb($booking->effective_hotel_name) }}@endif</div>
                    </td>
                    <td style="width: 28%; text-align: center;">
                        <div style="font-size: 6pt; color: #64748b; text-transform: uppercase;">{{ __('Allocated Seat(s)') }}</div>
                        <div class="stub-seat-box" dir="ltr">
                            {{ $seatNumbersText }}
                        </div>
                        <div style="font-size: 6.5pt; margin-top: 2px;">
                            @if($totalBalanceDue > 0)
                                <strong style="color: #dc3545;">{{ __('DUE') }}: <span dir="ltr">{{ format_currency($totalBalanceDue) }}</span></strong>
                            @else
                                <strong style="color: #16a34a;">{{ __('FARE PAID') }}</strong>
                            @endif
                        </div>
                    </td>
                    <td style="width: 24%; text-align: {{ $isAr ? 'left' : 'right' }}; vertical-align: middle;">
                        <div style="border: 1px solid #cbd5e1; background-color: #ffffff; padding: 4px; text-align: center;">
                            <div style="font-size: 6.5pt; color: #64748b;">[ &nbsp; ] {{ __('Boarded / صعد') }}</div>
                            <div style="font-size: 6pt; color: #94a3b8; margin-top: 6px;">{{ __('Conductor Signature') }}</div>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- Booking Reference Centered - NO BARCODE LINES -->
            <div style="text-align: center; margin-top: 3px;">
                <div style="font-size: 7pt; font-family: monospace; font-weight: bold; color: #475569; letter-spacing: 1px;" dir="ltr">
                    {{ $booking->booking_reference }}
                </div>
            </div>
        </div>

        <div class="footer-note" dir="{{ $dir }}">
            {{ __('Thank you for traveling with us. Verified Official Travel Voucher.') }} &bull; <span dir="ltr">{{ now()->format('d M Y, h:i A') }}</span>
        </div>
    </div>

</body>
</html>
