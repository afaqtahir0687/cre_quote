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

    // High-reliability Base64 Logo Resolution (100% immune to host/port mismatches, CORS, intranet or print timeouts)
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
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $dir }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Boarding Pass') }} - {{ $booking->booking_reference }} - {{ $booking->passenger_name }}</title>
    
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    @if($isAr)
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    @endif
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts: Cairo for Arabic, Inter for Latin, Libre Barcode 39 for Barcode -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700;800&family=Libre+Barcode+39&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand-primary: #00aeef;
            --brand-primary-dark: #008cc0;
            --brand-navy: #0F172A;
            --brand-navy-light: #1E293B;
            --brand-gold: #D97706;
            --brand-gold-light: #FEF3C7;
            --brand-success: #10B981;
            --brand-danger: #EF4444;
            --brand-border: #E2E8F0;
            --brand-bg-soft: #F8FAFC;
        }

        body {
            font-family: {{ $isAr ? "'Cairo', sans-serif" : "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" }};
            background: #EDF2F7;
            color: #1E293B;
            margin: 0;
            padding: 24px 12px;
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
        }

        /* Screen Print Toolbar */
        .print-toolbar {
            max-width: 1040px;
            margin: 0 auto 16px auto;
            background: #FFFFFF;
            padding: 12px 18px;
            border-radius: 12px;
            border: 1px solid var(--brand-border);
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.05);
        }

        .lang-switch-btn {
            font-weight: 700;
            font-size: 0.82rem;
            padding: 5px 12px;
            border-radius: 6px;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* Outer Ticket Wrapper */
        .ticket-wrapper {
            max-width: 1040px;
            margin: 0 auto;
            background: #FFFFFF;
            border-radius: 16px;
            box-shadow: 0 16px 45px rgba(15, 23, 42, 0.09);
            border: 1px solid var(--brand-border);
            overflow: hidden;
            position: relative;
        }

        /* Top Luxury Header */
        .ticket-header-strip {
            background: #FFFFFF;
            border-bottom: 3px solid var(--brand-primary);
            padding: 20px 28px;
            position: relative;
        }

        .ticket-header-strip::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--brand-primary) 0%, var(--brand-gold) 50%, var(--brand-primary) 100%);
        }

        .agency-logo-img {
            max-height: 72px;
            max-width: 220px;
            object-fit: contain;
            display: block;
        }

        .agency-logo-placeholder {
            width: 64px;
            height: 64px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--brand-navy) 0%, var(--brand-primary-dark) 100%);
            color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            box-shadow: 0 4px 12px rgba(0, 174, 239, 0.25);
        }

        .agency-title {
            font-size: 1.35rem;
            font-weight: 900;
            color: var(--brand-navy);
            line-height: 1.2;
            margin-bottom: 4px;
            letter-spacing: -0.01em;
        }

        .agency-meta {
            font-size: 0.76rem;
            color: #64748B;
            line-height: 1.4;
        }

        .agency-meta span {
            display: inline-block;
        }

        .voucher-title-box {
            text-align: {{ $isAr ? 'left' : 'right' }};
        }

        .voucher-headline {
            font-size: 1.15rem;
            font-weight: 900;
            color: var(--brand-navy);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .voucher-ref-badge {
            background: #F1F5F9;
            border: 1px solid #CBD5E1;
            padding: 4px 12px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--brand-primary-dark);
            display: inline-block;
            letter-spacing: 1px;
        }

        /* Dual-Zone Ticket Body */
        .ticket-body {
            display: flex;
            flex-direction: row;
            align-items: stretch;
            background: #FFFFFF;
            position: relative;
        }

        .ticket-main {
            flex: 1 1 73%;
            width: 73%;
            padding: 24px 28px;
            border-right: 2px dashed #CBD5E1;
            position: relative;
            background: #FFFFFF;
        }

        [dir="rtl"] .ticket-main {
            border-right: none;
            border-left: 2px dashed #CBD5E1;
        }

        .ticket-stub {
            flex: 0 0 27%;
            width: 27%;
            padding: 22px 20px;
            background: #FAFCFE;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        /* Perforation Notches & Cut Line */
        .notch-top, .notch-bottom {
            position: absolute;
            width: 24px;
            height: 24px;
            background: #EDF2F7;
            border-radius: 50%;
            z-index: 10;
        }

        .notch-top {
            top: -12px;
            right: calc(27% - 12px);
        }

        [dir="rtl"] .notch-top {
            right: auto;
            left: calc(27% - 12px);
        }

        .notch-bottom {
            bottom: -12px;
            right: calc(27% - 12px);
        }

        [dir="rtl"] .notch-bottom {
            right: auto;
            left: calc(27% - 12px);
        }

        .cut-indicator {
            position: absolute;
            top: 6px;
            right: calc(27% - 11px);
            font-size: 11px;
            color: #94A3B8;
            transform: translateX(50%);
            background: #FFFFFF;
            padding: 2px 4px;
            border-radius: 4px;
            z-index: 5;
        }

        [dir="rtl"] .cut-indicator {
            right: auto;
            left: calc(27% - 11px);
            transform: translateX(-50%);
        }

        /* Route Hero Box */
        .route-hero-card {
            background: linear-gradient(135deg, #F8FAFC 0%, #F1F5F9 100%);
            border: 1px solid var(--brand-border);
            border-radius: 14px;
            padding: 16px 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
        }

        .route-city-label {
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            color: #64748B;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .route-city-name {
            font-size: 1.38rem;
            font-weight: 900;
            color: var(--brand-navy);
            line-height: 1.2;
        }

        .route-icon-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--brand-primary);
        }

        .route-arrow-line {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--brand-primary);
            font-size: 1.1rem;
        }

        .route-meta-strip {
            border-top: 1px solid #E2E8F0;
            margin-top: 12px;
            padding-top: 10px;
            font-size: 0.8rem;
            color: #475569;
        }

        /* Seat Callout Box */
        .seat-callout-hero {
            background: linear-gradient(135deg, #00aeef 0%, #0077b6 100%);
            color: #FFFFFF;
            padding: 8px 18px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 14px rgba(0, 174, 239, 0.28);
        }

        .seat-callout-hero .seat-num {
            font-size: 1.45rem;
            font-weight: 900;
            letter-spacing: 0.5px;
            line-height: 1;
        }

        .seat-callout-stub {
            background: linear-gradient(135deg, var(--brand-navy) 0%, #1E3A8A 100%);
            color: #FFFFFF;
            border-radius: 14px;
            padding: 14px 16px;
            text-align: center;
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.15);
            border: 2px solid #FEF3C7;
        }

        .seat-callout-stub .seat-num-large {
            font-size: 2.4rem;
            font-weight: 900;
            line-height: 1;
            letter-spacing: 1px;
            color: #FCD34D;
        }

        /* Information Cells */
        .info-card {
            background: #FFFFFF;
            border: 1px solid var(--brand-border);
            border-radius: 10px;
            padding: 10px 14px;
            height: 100%;
            transition: border-color 0.15s ease;
        }

        .info-card-label {
            font-size: 0.70rem;
            font-weight: 800;
            text-transform: uppercase;
            color: #64748B;
            letter-spacing: 0.04em;
            margin-bottom: 2px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .info-card-value {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--brand-navy);
            line-height: 1.3;
        }

        /* Package Badges */
        .badge-pkg-hotel {
            background: #ECFDF5;
            color: #065F46;
            border: 1px solid #A7F3D0;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 800;
            font-size: 0.82rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .badge-pkg-bus {
            background: #EFF6FF;
            color: #1E40AF;
            border: 1px solid #BFDBFE;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 800;
            font-size: 0.82rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* Financial Statement Table */
        .finance-card {
            border: 1px solid var(--brand-border);
            border-radius: 10px;
            background: #FFFFFF;
            overflow: hidden;
            margin-top: 16px;
        }

        .finance-card table {
            margin-bottom: 0;
            font-size: 0.86rem;
        }

        .finance-card thead th {
            background: #F8FAFC;
            color: #475569;
            font-weight: 700;
            font-size: 0.78rem;
            padding: 7px 14px;
            border-bottom: 1px solid var(--brand-border);
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .finance-card tbody td {
            padding: 7px 14px;
            border-bottom: 1px solid #F1F5F9;
        }

        .finance-card tbody tr:last-child td {
            border-bottom: none;
        }

        /* Passenger Guidelines */
        .guidelines-card {
            background: #F8FAFC;
            border-radius: 10px;
            padding: 10px 14px;
            border: 1px solid var(--brand-border);
            margin-top: 14px;
        }

        .guidelines-list {
            font-size: 0.74rem;
            color: #475569;
            margin-bottom: 0;
            padding-{{ $isAr ? 'right' : 'left' }}: 18px;
            line-height: 1.5;
        }

        /* Verification & Stamp Area */
        .stamp-box-area {
            border: 1px dashed #CBD5E1;
            border-radius: 10px;
            padding: 8px 12px;
            background: #FFFFFF;
            min-height: 70px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-align: center;
        }

        .stamp-box-label {
            font-size: 0.68rem;
            font-weight: 700;
            color: #94A3B8;
            text-transform: uppercase;
        }

        /* Barcode Simulation */
        .barcode-strip {
            font-family: 'Libre Barcode 39', cursive;
            font-size: 46px;
            letter-spacing: 2px;
            line-height: 0.9;
            color: var(--brand-navy);
            text-align: center;
            user-select: none;
        }

        /* Bottom Security Strip */
        .ticket-footer-strip {
            background: #F8FAFC;
            border-top: 1px solid var(--brand-border);
            padding: 10px 24px;
            font-size: 0.76rem;
            color: #64748B;
        }

        /* Print Media Styles */
        @media print {
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
                box-shadow: none !important;
                border: 1px solid #94A3B8 !important;
                border-radius: 10px !important;
                margin: 0 !important;
            }

            .notch-top, .notch-bottom, .cut-indicator {
                display: none !important;
            }

            .ticket-header-strip,
            .route-hero-card,
            .seat-callout-hero,
            .seat-callout-stub,
            .badge-pkg-hotel,
            .badge-pkg-bus,
            .finance-card thead th,
            .badge {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            @page {
                size: A4 portrait;
                margin: 8mm 10mm;
            }
        }
    </style>
</head>
<body>

    <!-- 1. Print Toolbar (Visible on Screen, Auto-Hidden on Print) -->
    <div class="print-toolbar d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('travel.bookings.show', $booking->id) }}" class="btn btn-outline-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-{{ $isAr ? 'right' : 'left' }} me-1"></i> {{ __('Back to Details') }}
            </a>
            <a href="{{ route('travel.bookings.index') }}" class="btn btn-light btn-sm border shadow-sm">
                <i class="fas fa-list me-1"></i> {{ __('Bookings Roster') }}
            </a>
        </div>

        <!-- Language Toggle -->
        <div class="d-flex align-items-center gap-2 bg-light p-1 rounded-3 border">
            <span class="small fw-bold text-muted px-2"><i class="fas fa-globe text-primary me-1"></i> {{ __('Language') }}:</span>
            <a href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}" 
               class="lang-switch-btn {{ !$isAr ? 'btn btn-primary text-white' : 'text-dark' }}">
                English
            </a>
            <a href="{{ request()->fullUrlWithQuery(['lang' => 'ar']) }}" 
               class="lang-switch-btn {{ $isAr ? 'btn btn-primary text-white' : 'text-dark' }}">
                العربية
            </a>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button onclick="window.print()" class="btn btn-primary btn-sm px-4 fw-bold shadow-sm" style="background: var(--brand-primary); border-color: var(--brand-primary);">
                <i class="fas fa-print me-1"></i> {{ __('Print Boarding Pass') }}
            </button>
            <button onclick="window.close()" class="btn btn-outline-dark btn-sm">
                <i class="fas fa-times me-1"></i> {{ __('Close') }}
            </button>
        </div>
    </div>

    <!-- 2. Main Boarding Pass Document -->
    <div class="ticket-wrapper">
        <!-- Top Perforation Notch Cutouts -->
        <div class="notch-top"></div>
        <div class="notch-bottom"></div>
        <div class="cut-indicator">✂</div>

        <!-- Header: Official Agency Brand + Voucher Identifier -->
        <div class="ticket-header-strip">
            <div class="row align-items-center g-3">
                <!-- Agency Logo & Details -->
                <div class="col-md-7 d-flex align-items-center gap-3">
                    @if($logoSrc)
                        <img src="{{ $logoSrc }}" alt="{{ $brandName }}" class="agency-logo-img">
                    @else
                        <div class="agency-logo-placeholder">
                            <i class="fas fa-kaaba"></i>
                        </div>
                    @endif
                    <div>
                        <div class="agency-title">{{ $brandName }}</div>
                        <div class="agency-meta">
                            <div>
                                <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                {{ tdb($companyAddress) }}@if($companyCity) &bull; {{ tdb($companyCity) }}@endif
                            </div>
                            <div class="mt-1">
                                @if($companyPhone)
                                    <span class="me-2"><i class="fas fa-phone-alt text-success me-1"></i>{{ $companyPhone }}</span>
                                @endif
                                @if($companyEmail)
                                    <span class="me-2"><i class="fas fa-envelope text-primary me-1"></i>{{ $companyEmail }}</span>
                                @endif
                            </div>
                            <div class="mt-1 d-flex flex-wrap gap-2">
                                @if($companyCr)
                                    <span class="badge bg-light text-dark border extra-small">
                                        <strong>{{ __('CR No') }}:</strong> {{ $companyCr }}
                                    </span>
                                @endif
                                @if($companyVat)
                                    <span class="badge bg-light text-dark border extra-small">
                                        <strong>{{ __('VAT No') }}:</strong> {{ $companyVat }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Voucher Headline & Reference -->
                <div class="col-md-5 voucher-title-box">
                    <span class="badge bg-warning text-dark fw-bold text-uppercase px-2 py-1 mb-1 extra-small">
                        <i class="fas fa-kaaba me-1"></i> {{ __('Official Umrah Coach Pass') }}
                    </span>
                    <div class="voucher-headline">{{ __('Boarding Pass & Travel Voucher') }}</div>
                    <div class="mt-1">
                        <span class="extra-small text-muted d-block">{{ __('Booking Reference / رقم الحجز') }}</span>
                        <span class="voucher-ref-badge">{{ $booking->booking_reference }}</span>
                    </div>
                    <div class="extra-small text-muted mt-1">
                        <i class="far fa-calendar-alt me-1"></i> {{ __('Issued / تاريخ الإصدار') }}: {{ $booking->created_at->format('d M Y, h:i A') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Dual-Zone Body: Passenger Voucher (73%) + Gate Stub (27%) -->
        <div class="ticket-body">
            
            <!-- ================= Zone A: Passenger Travel Voucher (73%) ================= -->
            <div class="ticket-main">
                
                <!-- Hero Route Card -->
                <div class="route-hero-card">
                    <div class="row align-items-center g-2 text-center text-md-{{ $isAr ? 'end' : 'start' }}">
                        <!-- Origin -->
                        <div class="col-md-5">
                            <span class="route-city-label"><i class="fas fa-map-pin text-danger me-1"></i> {{ __('Departure Terminal / نقطة الانطلاق') }}</span>
                            <div class="route-city-name">{{ $trip ? tdb($trip->departure_city) : __('Departure City') }}</div>
                            <div class="small fw-semibold text-muted mt-1">
                                <i class="far fa-clock text-primary me-1"></i>
                                {{ $trip && $trip->departure_date ? $trip->departure_date->format('d M Y - h:i A') : 'Scheduled' }}
                            </div>
                        </div>

                        <!-- Route Icon & Duration -->
                        <div class="col-md-2 text-center route-icon-container my-2 my-md-0">
                            <div class="route-arrow-line">
                                <i class="fas fa-bus fa-lg"></i>
                                <i class="fas fa-arrow-{{ $isAr ? 'left' : 'right' }}"></i>
                            </div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle extra-small fw-bold mt-1">
                                {{ $trip ? $trip->duration_days : 10 }} {{ __('Days / أيام') }}
                            </span>
                        </div>

                        <!-- Destination -->
                        <div class="col-md-5 text-md-{{ $isAr ? 'start' : 'end' }}">
                            <span class="route-city-label"><i class="fas fa-kaaba text-success me-1"></i> {{ __('Destination / الوجهة') }}</span>
                            <div class="route-city-name text-primary">{{ $trip ? tdb($trip->destination_city) : __('Destination') }}</div>
                            <div class="small fw-semibold text-muted mt-1">
                                <i class="far fa-calendar-check text-success me-1"></i>
                                {{ $trip && $trip->return_date ? $trip->return_date->format('d M Y') : __('Return Included') }}
                            </div>
                        </div>
                    </div>

                    <!-- Bus Fleet & Trip Code Strip -->
                    <div class="route-meta-strip d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <i class="fas fa-bus-alt text-primary me-1"></i>
                            <strong>{{ __('Coach Fleet / الحافلة') }}:</strong> {{ $trip ? tdb($trip->bus_name) : __('Luxury Executive Coach') }}
                            @if($trip && $trip->bus_number)
                                &bull; <span class="badge bg-white text-dark border">{{ $trip->bus_number }}</span>
                            @endif
                        </div>
                        <div>
                            <i class="fas fa-tag text-info me-1"></i>
                            <strong>{{ __('Trip Code / رمز الرحلة') }}:</strong> <span class="fw-bold text-dark">{{ $trip ? $trip->trip_code : '---' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Passenger Details & Seat Grid -->
                <div class="row g-2 mb-2">
                    <!-- Passenger Full Name -->
                    <div class="col-md-6">
                        <div class="info-card">
                            <span class="info-card-label"><i class="fas fa-user-circle text-primary"></i> {{ __('Passenger Name / اسم الراكب') }}</span>
                            <div class="info-card-value fs-6">{{ tdb($booking->passenger_name) }}</div>
                            <div class="extra-small text-muted mt-1">
                                <i class="fas fa-phone-alt text-success me-1"></i> {{ $booking->passenger_phone }}
                                &bull; <i class="fas fa-venus-mars text-secondary me-1"></i> {{ ucfirst(__($booking->passenger_gender)) }}
                            </div>
                        </div>
                    </div>

                    <!-- National ID / Iqama / Passport -->
                    <div class="col-md-6">
                        <div class="info-card">
                            <span class="info-card-label"><i class="fas fa-id-card text-info"></i> {{ __('National ID / Iqama / Passport') }}</span>
                            <div class="info-card-value font-monospace fs-6">{{ $booking->passenger_id_number ?: '—' }}</div>
                            <div class="extra-small text-muted mt-1">
                                <i class="fas fa-shield-alt text-success me-1"></i> {{ __('Identity Document Verified for Boarding') }}
                            </div>
                        </div>
                    </div>

                    <!-- Accommodation & Seat Callout Row -->
                    <div class="col-12">
                        <div class="info-card d-flex flex-wrap justify-content-between align-items-center gap-2 py-2">
                            <div>
                                <span class="info-card-label"><i class="fas fa-hotel text-warning"></i> {{ __('Accommodation Package / باقة الإقامة') }}</span>
                                <div class="d-flex flex-wrap align-items-center gap-2 mt-1">
                                    @if($booking->package_type === 'with_hotel')
                                        <span class="badge-pkg-hotel">
                                            <i class="fas fa-check-circle"></i> {{ __('With Hotel Included') }}
                                        </span>
                                        <span class="fw-bold text-dark small">
                                            {{ $booking->hotel_name ? tdb($booking->hotel_name) : ($trip->hotel_name ? tdb($trip->hotel_name) : __('Designated Hotel')) }}
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
                                        <span class="badge-pkg-bus">
                                            <i class="fas fa-bus"></i> {{ __('Seat Only (Without Hotel)') }}
                                        </span>
                                        <span class="text-muted extra-small">{{ __('Passenger arranged independent accommodation.') }}</span>
                                    @endif
                                </div>
                            </div>

                            <!-- High Visibility Seat Badge -->
                            <div class="text-end">
                                <div class="seat-callout-hero">
                                    <i class="fas fa-chair fa-lg"></i>
                                    <div>
                                        <div class="extra-small text-white-50 text-uppercase" style="font-size: 0.65rem;">{{ __('Seat') }}</div>
                                        <div class="seat-num">#{{ $booking->seat_number }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Statement Table -->
                <div class="finance-card">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('Description / البيان') }}</th>
                                <th class="text-center" style="width: 140px;">{{ __('Status / الحالة') }}</th>
                                <th class="text-end" style="width: 150px;">{{ __('Amount / المبلغ') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <strong>{{ __('Bus Coach Seat Ticket (Seat #:seat)', ['seat' => $booking->seat_number]) }}</strong>
                                    @if($trip) <span class="text-muted extra-small">({{ tdb($trip->bus_name) }})</span> @endif
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
                                        <span class="text-muted extra-small">({{ tdb($booking->hotel_name ?: 'Hotel Stay') }})</span>
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

                <!-- Passenger Guidelines & Verification Stamps -->
                <div class="row g-2 mt-2 align-items-stretch">
                    <!-- Guidelines Box -->
                    <div class="col-md-8">
                        <div class="guidelines-card h-100">
                            <div class="d-flex align-items-center gap-1 extra-small fw-bold text-dark text-uppercase mb-1">
                                <i class="fas fa-info-circle text-primary"></i>
                                <span>{{ __('Important Passenger Guidelines / تعليمات هامة للمسافرين') }}</span>
                            </div>
                            <ul class="guidelines-list">
                                <li>{{ __('Please arrive at the coach departure terminal at least 60 minutes prior to scheduled departure.') }}</li>
                                <li>{{ __('Original national ID, Iqama or valid Passport with active Umrah permit is strictly mandatory.') }}</li>
                                <li>{{ __('Each traveler is permitted 1 standard luggage piece (max 25kg) and 1 personal handbag.') }}</li>
                                <li>{{ __('Present this physical or digital boarding pass to the driver/conductor before embarkation.') }}</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Agency Stamp & Signature Box -->
                    <div class="col-md-4">
                        <div class="stamp-box-area h-100">
                            <span class="stamp-box-label">{{ __('Agency Official Stamp') }}</span>
                            <div class="my-auto py-2 text-center text-muted opacity-50">
                                <i class="fas fa-stamp fa-2x"></i>
                            </div>
                            <div class="border-top pt-1 extra-small text-muted">
                                {{ __('Authorized Signature & Date') }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ================= Zone B: Tear-Off Gate Stub (27%) ================= -->
            <div class="ticket-stub">
                <div>
                    <!-- Stub Header -->
                    <div class="text-center pb-2 border-bottom mb-2">
                        <span class="badge bg-dark text-white extra-small text-uppercase px-2 py-1 mb-1">
                            {{ __('Gate Stub / قسيمة الصعود') }}
                        </span>
                        <div class="fw-bold text-dark small text-truncate">{{ $brandName }}</div>
                        <div class="font-monospace extra-small text-muted">{{ $booking->booking_reference }}</div>
                    </div>

                    <!-- Prominent Seat Callout Box -->
                    <div class="seat-callout-stub my-2">
                        <div class="extra-small text-white-50 text-uppercase fw-bold" style="font-size: 0.68rem;">
                            {{ __('Seat # / رقم المقعد') }}
                        </div>
                        <div class="seat-num-large">#{{ $booking->seat_number }}</div>
                        <div class="badge bg-success text-white extra-small mt-1 px-2">
                            {{ __('Confirmed Seat') }}
                        </div>
                    </div>

                    <!-- Passenger Stub Details -->
                    <div class="mb-2">
                        <span class="info-card-label">{{ __('Passenger / الراكب') }}</span>
                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.88rem;">{{ tdb($booking->passenger_name) }}</div>
                        <div class="extra-small text-muted font-monospace">{{ $booking->passenger_phone }}</div>
                    </div>

                    <div class="mb-2">
                        <span class="info-card-label">{{ __('Route / خط السير') }}</span>
                        <div class="fw-bold text-primary small">
                            {{ $trip ? tdb($trip->departure_city) : __('Origin') }} &rarr; {{ $trip ? tdb($trip->destination_city) : __('Destination') }}
                        </div>
                    </div>

                    <div class="mb-2">
                        <span class="info-card-label">{{ __('Departure / موعد الرحلة') }}</span>
                        <div class="fw-semibold text-dark extra-small">
                            {{ $trip && $trip->departure_date ? $trip->departure_date->format('d M Y - h:i A') : 'Scheduled' }}
                        </div>
                    </div>

                    <div class="mb-2">
                        <span class="info-card-label">{{ __('Package / نوع الباقة') }}</span>
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
                            <i class="fas fa-exclamation-circle me-1"></i> {{ __('Due:') }} {{ format_currency($booking->balance_due) }}
                        </div>
                    @else
                        <div class="p-2 rounded bg-success-subtle text-success border border-success-subtle extra-small fw-bold text-center mt-2">
                            <i class="fas fa-check-circle me-1"></i> {{ __('Fare Paid') }}
                        </div>
                    @endif
                </div>

                <!-- Bottom Conductor Stub, Barcode & Check Box -->
                <div class="pt-2 border-top text-center mt-2">
                    <div class="barcode-strip">*{{ $booking->booking_reference }}*</div>
                    <div class="font-monospace extra-small text-muted" style="letter-spacing: 0.1em;">
                        {{ $booking->booking_reference }}
                    </div>
                    
                    <!-- Boarding Check Box -->
                    <div class="mt-2 p-1 border rounded bg-white extra-small text-muted fw-bold d-flex align-items-center justify-content-center gap-2">
                        <i class="far fa-square fa-lg text-primary"></i>
                        <span>{{ __('Checked & Boarded') }}</span>
                    </div>
                    <div class="extra-small text-muted mt-1" style="font-size: 0.62rem;">
                        {{ __('Conductor Inspection Stub') }}
                    </div>
                </div>
            </div>

        </div>

        <!-- 3. Bottom Security & Islamic Dua Strip -->
        <div class="ticket-footer-strip d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <i class="fas fa-shield-alt text-success me-1"></i>
                <span>{{ __('Verified Official Boarding Document') }} &bull; {{ __('System Generated on') }}: {{ now()->format('d M Y, h:i A') }}</span>
            </div>
            <div class="text-md-end fw-bold text-dark">
                <span>{{ __('Thank you for choosing :name. May Allah accept your Umrah!', ['name' => $brandName]) }}</span>
            </div>
        </div>
    </div>

</body>
</html>
