@php
    $shop = $trip->shop;
    $setting = $shop?->setting;
    $logoPath = shop_logo_path($shop, $setting);
    if (!($logoPath && file_exists($logoPath))) {
        $logoPath = platform_logo_path();
    }
    $logoSrc = null;
    if ($logoPath && file_exists($logoPath)) {
        $mime = @mime_content_type($logoPath) ?: 'image/png';
        $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
    }
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Trip Passenger Manifest') }} - {{ $trip->trip_code }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1e293b;
            font-size: 0.85rem;
        }

        .action-bar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        .manifest-paper {
            background: #ffffff;
            max-width: 1050px;
            margin: 24px auto;
            padding: 24px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: 1px solid #cbd5e1;
        }

        .driver-box, .meta-box {
            border: 1.5px solid #1e293b;
            padding: 8px 12px;
            background: #f8fafc;
            border-radius: 4px;
        }

        .driver-box-title, .meta-box-title {
            font-weight: 800;
            font-size: 0.8rem;
            color: #0f172a;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 3px;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .company-name {
            font-size: 1.35rem;
            font-weight: 800;
            color: #0A6FB7;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin: 0;
        }
        .manifest-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: #0f172a;
            margin: 2px 0;
            text-transform: uppercase;
        }
        .route-subtext {
            font-size: 0.95rem;
            font-weight: 700;
            color: #FA7436;
        }

        /* Manifest Grid Table */
        .manifest-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.78rem;
            margin-top: 8px;
        }
        .manifest-table th {
            background-color: #1e293b !important;
            color: #ffffff !important;
            font-weight: 700;
            text-align: center;
            vertical-align: middle;
            padding: 6px 4px;
            border: 1px solid #0f172a;
            font-size: 0.74rem;
            text-transform: uppercase;
        }
        .manifest-table td {
            border: 1px solid #475569;
            padding: 4px 6px;
            vertical-align: middle;
        }
        .manifest-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .sn-col { width: 4%; font-weight: bold; text-align: center; }
        .name-col { width: 19%; font-weight: 700; }
        .nat-col { width: 5%; text-align: center; font-weight: 700; }
        .iqama-col { width: 12%; text-align: center; font-family: monospace; }
        .mobile-col { width: 12%; text-align: center; }
        .tkt-col { width: 8%; text-align: center; font-weight: bold; }
        .day-col { width: 6%; text-align: center; }
        .loc-col { width: 10%; text-align: center; }
        .unp-col { width: 7%; text-align: right; color: #dc2626; font-weight: bold; }
        .sar-col { width: 7%; text-align: right; font-weight: bold; }
        .htl-col { width: 10%; text-align: center; }

        .summary-bar {
            background: #e2e8f0;
            border: 1.5px solid #1e293b;
            padding: 8px 12px;
            margin-top: 8px;
            border-radius: 4px;
        }

        .sign-box {
            border: 1px dashed #94a3b8;
            padding: 10px;
            text-align: center;
            border-radius: 4px;
            height: 75px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .manifest-paper {
                box-shadow: none !important;
                border: none !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
        }
    </style>
</head>
<body>

    <!-- Sticky Top Action Bar (No-Print) -->
    <div class="action-bar no-print">
        <div class="container-fluid px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('travel.trips.show', $trip->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Seat Layout') }}
                </a>
                <span class="badge bg-primary fs-6">{{ $trip->trip_code }}</span>
                <span class="fw-bold text-dark">{{ $trip->title }}</span>
            </div>

            <div class="d-flex align-items-center gap-2">
                <!-- Language Toggle -->
                <div class="btn-group btn-group-sm">
                    <a href="{{ request()->fullUrlWithQuery(['lang' => 'ar']) }}" class="btn {{ app()->getLocale() === 'ar' ? 'btn-primary' : 'btn-outline-primary' }}">
                        العربية
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}" class="btn {{ app()->getLocale() === 'en' ? 'btn-primary' : 'btn-outline-primary' }}">
                        English
                    </a>
                </div>

                <!-- Print Sheet -->
                <button onclick="window.print()" class="btn btn-info btn-sm text-white">
                    <i class="fas fa-print me-1"></i> {{ __('Print Sheet') }}
                </button>

                <!-- Download Official PDF -->
                <a href="{{ route('travel.trips.manifest_pdf', ['trip' => $trip->id, 'lang' => app()->getLocale(), 'download' => 1]) }}" class="btn btn-danger btn-sm">
                    <i class="fas fa-file-pdf me-1"></i> {{ __('Download PDF (A4)') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Printable Paper Sheet -->
    <div class="manifest-paper">
        
        <!-- Header Row (Driver on Left, Heading in Center, Meta on Right) -->
        <div class="row g-3 align-items-start mb-3">
            <!-- Left: Driver Info Block -->
            <div class="col-4">
                <div class="driver-box">
                    <div class="driver-box-title">{{ __('DRIVER / طاقم السائقين') }}</div>
                    <table class="w-100" style="font-size: 0.78rem;">
                        <tr>
                            <td class="fw-bold" style="width: 14px;">1</td>
                            <td class="fw-bold">{{ $trip->driver_1_name ?: 'ADNAN KHAN' }}</td>
                            <td class="text-center fw-bold text-primary" style="width: 40px;">{{ $trip->driver_1_nationality ?: 'PAK' }}</td>
                            <td class="text-end font-monospace">{{ $trip->driver_1_phone ?: '2580796130' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold" style="width: 14px;">2</td>
                            <td class="fw-bold">{{ $trip->driver_2_name ?: 'SHOWKAT' }}</td>
                            <td class="text-center fw-bold text-primary" style="width: 40px;">{{ $trip->driver_2_nationality ?: 'PAK' }}</td>
                            <td class="text-end font-monospace">{{ $trip->driver_2_phone ?: '2620061750' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Center: Agency & Trip Headline -->
            <div class="col-4 text-center">
                @if($logoSrc)
                    <div class="mb-2">
                        <img src="{{ $logoSrc }}" alt="{{ $trip->shop?->name ?? 'Company Logo' }}" style="max-height: 48px; max-width: 160px; object-fit: contain;">
                    </div>
                @endif
                <h1 class="company-name">{{ $trip->shop?->name ?? 'UMRAH TRAVEL AGENCY' }}</h1>
                <h2 class="manifest-title">{{ __('TRIP MANIFEST & BUS STATEMENT') }}</h2>
                <div class="route-subtext">
                    {{ $trip->departure_city }} &rarr; {{ $trip->destination_city }}
                    &bull; {{ $trip->duration_days }} {{ __('Days') }}
                </div>
                <div class="text-muted extra-small mt-1">
                    {{ $trip->title }} ({{ $trip->trip_code }})
                </div>
            </div>

            <!-- Right: Date, Bus, Time Meta Box -->
            <div class="col-4">
                <div class="meta-box">
                    <div class="meta-box-title">{{ __('TRIP DETAILS / بيانات الرحلة') }}</div>
                    <table class="w-100" style="font-size: 0.78rem;">
                        <tr>
                            <td class="text-muted fw-bold">{{ __('DATE / التاريخ') }}:</td>
                            <td class="text-end fw-bold">
                                {{ $trip->departure_date ? $trip->departure_date->format('d/m/Y') : date('d/m/Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-bold">{{ __('TIME / الوقت') }}:</td>
                            <td class="text-end fw-bold">
                                {{ $trip->departure_date ? $trip->departure_date->format('h:i:s A') : '07:00:00 PM' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-bold">{{ __('BUS / الحافلة') }}:</td>
                            <td class="text-end fw-bold text-primary">
                                {{ $trip->bus_number ?: '3285VJA' }} ({{ $trip->bus_name }})
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Manifest Table (Columns matching screenshot) -->
        <table class="manifest-table">
            <thead>
                <tr>
                    <th class="sn-col">{{ __('Seat #') }}<br><span style="font-size: 0.65rem;">{{ __('المقعد') }}</span></th>
                    <th class="name-col">{{ __('Passenger Name') }}<br><span style="font-size: 0.65rem;">{{ __('اسم الراكب') }}</span></th>
                    <th class="nat-col">{{ __('Nationality') }}<br><span style="font-size: 0.65rem;">{{ __('الجنسية') }}</span></th>
                    <th class="iqama-col">{{ __('Iqama / Passport') }}<br><span style="font-size: 0.65rem;">{{ __('رقم الهوية / الجواز') }}</span></th>
                    <th class="mobile-col">{{ __('Mobile Number') }}<br><span style="font-size: 0.65rem;">{{ __('رقم الجوال') }}</span></th>
                    <th class="tkt-col">{{ __('Ticket #') }}<br><span style="font-size: 0.65rem;">{{ __('رقم التذكرة') }}</span></th>
                    <th class="day-col">{{ __('Type/Dur') }}<br><span style="font-size: 0.65rem;">{{ __('النوع/المدة') }}</span></th>
                    <th class="loc-col">{{ __('Pickup & Drop') }}<br><span style="font-size: 0.65rem;">{{ __('الركوب والنزول') }}</span></th>
                    <th class="unp-col">{{ __('Unpaid') }}<br><span style="font-size: 0.65rem;">{{ __('المتبقي') }}</span></th>
                    <th class="sar-col">{{ __('Total (SAR)') }}<br><span style="font-size: 0.65rem;">{{ __('المبلغ (ريال)') }}</span></th>
                    <th class="htl-col">{{ __('Hotel') }}<br><span style="font-size: 0.65rem;">{{ __('الفندق') }}</span></th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalSeats = (int) ($trip->total_seats ?: 49);
                    $bookingsBySeat = $bookings->keyBy('seat_number');
                    $sumTotal = 0;
                    $sumPaid = 0;
                    $sumUnpaid = 0;
                    $bookedCount = 0;
                @endphp

                @for($sn = 1; $sn <= $totalSeats; $sn++)
                    @php
                        $b = $bookingsBySeat->get($sn);
                        if ($b) {
                            $bookedCount++;
                            $sumTotal += (float)$b->total_amount;
                            $sumPaid += (float)$b->paid_amount;
                            $sumUnpaid += (float)$b->balance_due;

                            $nat = strtoupper(trim($b->passenger_nationality ?: 'SAU'));
                            if (strlen($nat) > 4) {
                                $nat = substr($nat, 0, 3);
                            }

                            $dayTag = $b->isOneWay() ? '1-WAY' : (($trip->duration_days ?: 3) . 'D');
                            $loc = $b->pickup_location ?: ($b->shop?->name ?: 'OFICE');
                            $drop = $b->dropoff_location ?: '';
                            $retDrop = $b->return_dropoff_location ?: '';
                            if (strlen($loc) > 10) {
                                $loc = substr($loc, 0, 9) . '.';
                            }
                            if (strlen($drop) > 10) {
                                $drop = substr($drop, 0, 9) . '.';
                            }
                            if (strlen($retDrop) > 10) {
                                $retDrop = substr($retDrop, 0, 9) . '.';
                            }

                            $tkt = preg_replace('/[^0-9]/', '', $b->booking_reference);
                            $tkt = !empty($tkt) ? substr($tkt, -4) : $b->id;

                            $htl = $b->package_type === 'with_hotel' ? ($b->effective_hotel_name ?: 'HTL') : '--';
                            if (strlen($htl) > 12) {
                                $htl = substr($htl, 0, 11) . '.';
                            }
                        }
                    @endphp
                    @php
                        $sClass = $b ? ($b->seat_class ?: $trip->getSeatClass($sn)) : $trip->getSeatClass($sn);
                        $classColor = $sClass === 'vip' ? '#b45309' : ($sClass === 'business' ? '#1d4ed8' : '#047857');
                    @endphp
                    <tr>
                        <td class="sn-col">
                            <strong>{{ $sn }}</strong>
                            <span style="font-size: 0.65rem; color: {{ $classColor }}; display: block; font-weight: bold;">{{ strtoupper(substr($sClass, 0, 3)) }}</span>
                        </td>
                        <td class="name-col">
                            @if($b)
                                {{ strtoupper($b->passenger_name) }}
                            @else
                                <span class="text-muted fw-normal">-- {{ __('AVAILABLE') }} --</span>
                            @endif
                        </td>
                        <td class="nat-col">{{ $b ? $nat : '' }}</td>
                        <td class="iqama-col">{{ $b ? ($b->passenger_id_number ?: '--') : '' }}</td>
                        <td class="mobile-col">{{ $b ? ($b->passenger_phone ?: '--') : '' }}</td>
                        <td class="tkt-col">{{ $b ? $tkt : '' }}</td>
                        <td class="day-col">{{ $b ? $dayTag : '' }}</td>
                        <td class="loc-col" style="font-size: 0.65rem; line-height: 1.1;">
                            @if($b)
                                <div style="white-space: nowrap;"><span class="text-success fw-bold">{{ __('P:') }}</span> {{ $loc }}</div>
                                @if(!empty($drop))
                                    <div style="white-space: nowrap; color: #dc2626;"><span class="fw-bold">{{ __('D:') }}</span> {{ $drop }}</div>
                                @endif
                                @if(!empty($retDrop))
                                    <div style="white-space: nowrap; color: #b45309;"><span class="fw-bold">{{ __('R:') }}</span> {{ $retDrop }}</div>
                                @endif
                            @endif
                        </td>
                        <td class="unp-col">
                            @if($b)
                                {{ (float)$b->balance_due > 0 ? number_format($b->balance_due, 0) : '0' }}
                            @endif
                        </td>
                        <td class="sar-col">
                            @if($b)
                                {{ number_format($b->total_amount, 0) }}
                            @endif
                        </td>
                        <td class="htl-col">{{ $b ? $htl : '' }}</td>
                    </tr>
                @endfor
            </tbody>
        </table>

        <!-- Summary Totals Bar -->
        <div class="summary-bar">
            <div class="row text-center fw-bold small">
                <div class="col-3">
                    <span class="text-muted">{{ __('BOOKED SEATS') }}:</span>
                    <span class="text-primary fs-6 ms-1">{{ $bookedCount }} / {{ $totalSeats }}</span>
                </div>
                <div class="col-3">
                    <span class="text-muted">{{ __('TOTAL COLLECTED (PAID)') }}:</span>
                    <span class="text-success fs-6 ms-1">SAR {{ number_format($sumPaid, 2) }}</span>
                </div>
                <div class="col-3">
                    <span class="text-muted">{{ __('TOTAL UNPAID') }}:</span>
                    <span class="text-danger fs-6 ms-1">SAR {{ number_format($sumUnpaid, 2) }}</span>
                </div>
                <div class="col-3">
                    <span class="fw-bold" style="color: #0A6FB7;">{{ __('GROSS TRIP VALUE (SAR)') }}:</span>
                    <span class="fs-6 ms-1 fw-bold" style="color: #0A6FB7;">SAR {{ number_format($sumTotal, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Signatures & Stamps -->
        <div class="row g-3 mt-3">
            <div class="col-4">
                <div class="sign-box">
                    <strong class="extra-small text-uppercase">{{ __('Driver 1 Signature & Receipt / استلام السائق') }}</strong>
                    <span class="text-muted extra-small">{{ $trip->driver_1_name ?: 'Adnan Khan' }} ____________________</span>
                </div>
            </div>
            <div class="col-4">
                <div class="sign-box">
                    <strong class="extra-small text-uppercase">{{ __('Bus Conductor / موجه ومسؤول الحافلة') }}</strong>
                    <span class="text-muted extra-small">________________________________________</span>
                </div>
            </div>
            <div class="col-4">
                <div class="sign-box">
                    <strong class="extra-small text-uppercase">{{ __('Agency Stamp / ختم المنشأة المعتمد') }}</strong>
                    <span class="text-muted extra-small">{{ $trip->shop?->name ?? 'Verified Agency Official' }}</span>
                </div>
            </div>
        </div>

        <!-- Generated Timestamp -->
        <div class="text-center text-muted extra-small mt-3 pt-2 border-top">
            {{ __('Official Bus Trip Manifest & Passenger Statement Sheet') }} &bull; {{ __('Generated on:') }} {{ date('d M Y - h:i A') }} &bull; {{ __('Trip Code:') }} {{ $trip->trip_code }}
        </div>

    </div>

</body>
</html>
