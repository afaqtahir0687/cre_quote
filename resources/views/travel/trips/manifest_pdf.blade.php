@php
    $isAr = app()->getLocale() === 'ar';
    $dir = $isAr ? 'rtl' : 'ltr';

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
<html lang="{{ app()->getLocale() }}" dir="{{ $dir }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Trip Manifest') }} - {{ $trip->trip_code }}</title>
    <style>
        body {
            font-family: 'amiri', 'dejavusans', sans-serif;
            font-size: 7.5pt;
            color: #111827;
            margin: 0;
            padding: 0;
            direction: {{ $dir }};
            line-height: 1.2;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }

        /* Header block */
        .header-table td {
            vertical-align: top;
            padding: 3px;
        }
        .driver-cell {
            border: 1px solid #1f2937;
            background-color: #f9fafb;
            padding: 4px;
            font-size: 7pt;
        }
        .meta-cell {
            border: 1px solid #1f2937;
            background-color: #f9fafb;
            padding: 4px;
            font-size: 7pt;
        }
        .center-cell {
            text-align: center;
            vertical-align: middle;
        }

        /* Manifest Main Grid Table */
        .manifest-table {
            margin-top: 5px;
        }
        .manifest-table th {
            background-color: #1f2937;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
            padding: 3px 2px;
            border: 0.5px solid #111827;
            font-size: 6.8pt;
        }
        .manifest-table td {
            border: 0.5px solid #4b5563;
            padding: 2.2px 2px;
            font-size: 6.8pt;
        }

        .sn-col { width: 5%; font-weight: bold; text-align: center; }
        .name-col { width: 18%; font-weight: bold; }
        .nat-col { width: 7%; text-align: center; font-weight: bold; }
        .iqama-col { width: 12%; text-align: center; }
        .mobile-col { width: 12%; text-align: center; }
        .tkt-col { width: 8%; text-align: center; font-weight: bold; }
        .day-col { width: 6%; text-align: center; }
        .loc-col { width: 10%; text-align: center; }
        .unp-col { width: 7%; text-align: right; color: #dc2626; font-weight: bold; }
        .sar-col { width: 7%; text-align: right; font-weight: bold; }
        .htl-col { width: 8%; text-align: center; }

        /* Summary Footer */
        .summary-table {
            margin-top: 4px;
            font-size: 7.2pt;
        }
        .summary-table td {
            border: 1px solid #1f2937;
            padding: 3px 5px;
            text-align: center;
            font-weight: bold;
            background-color: #e5e7eb;
        }

        .signatures-table {
            margin-top: 6px;
            font-size: 7pt;
        }
        .signatures-table td {
            border: 0.5px dashed #9ca3af;
            padding: 4px;
            text-align: center;
            vertical-align: bottom;
            height: 30px;
        }
    </style>
</head>
<body>

    <!-- Top Header (Driver - Center Title - Trip Details) -->
    <table class="header-table">
        <tr>
            <!-- Driver Details -->
            <td style="width: 33%;" class="driver-cell">
                <div style="font-weight: bold; font-size: 8pt; margin-bottom: 2px;">{{ __('DRIVER / طاقم السائقين') }}</div>
                <div><strong>1.</strong> {{ $trip->driver_1_name ?: 'ADNAN KHAN' }} &bull; <strong>{{ $trip->driver_1_nationality ?: 'PAK' }}</strong> &bull; {{ $trip->driver_1_phone ?: '2580796130' }}</div>
                <div style="margin-top: 2px;"><strong>2.</strong> {{ $trip->driver_2_name ?: 'SHOWKAT' }} &bull; <strong>{{ $trip->driver_2_nationality ?: 'PAK' }}</strong> &bull; {{ $trip->driver_2_phone ?: '2620061750' }}</div>
            </td>

            <!-- Center Heading -->
            <td style="width: 34%;" class="center-cell">
                @if($logoSrc)
                    <div style="text-align: center; margin-bottom: 2px;">
                        <img src="{{ $logoSrc }}" style="max-height: 28px; max-width: 110px;" alt="Logo">
                    </div>
                @endif
                <div style="font-size: 10pt; font-weight: bold; color: #0A6FB7;">{{ $trip->shop?->name ?? 'UMRAH TRAVEL AGENCY' }}</div>
                <div style="font-size: 8.5pt; font-weight: bold; margin-top: 1px;">{{ __('TRIP MANIFEST & BUS STATEMENT') }}</div>
                <div style="font-size: 7.5pt; font-weight: bold; color: #FA7436;">
                    {{ $trip->departure_city }} &rarr; {{ $trip->destination_city }} ({{ $trip->duration_days }} {{ __('Days') }})
                </div>
                <div style="font-size: 6.5pt; color: #4b5563;">{{ $trip->title }} &bull; {{ $trip->trip_code }}</div>
            </td>

            <!-- Trip Meta -->
            <td style="width: 33%;" class="meta-cell">
                <div style="font-weight: bold; font-size: 8pt; text-align: right; margin-bottom: 2px;">{{ __('TRIP DETAILS / بيانات الرحلة') }}</div>
                <div style="text-align: right;"><strong>{{ __('DATE / التاريخ') }}:</strong> {{ $trip->departure_date ? $trip->departure_date->format('d/m/Y') : date('d/m/Y') }}</div>
                <div style="text-align: right; margin-top: 2px;"><strong>{{ __('TIME / الوقت') }}:</strong> {{ $trip->departure_date ? $trip->departure_date->format('h:i:s A') : '07:00:00 PM' }}</div>
                <div style="text-align: right; margin-top: 2px;"><strong>{{ __('BUS / الحافلة') }}:</strong> <span style="color: #0A6FB7; font-weight: bold;">{{ $trip->bus_number ?: '3285VJA' }}</span> ({{ $trip->bus_name }})</div>
            </td>
        </tr>
    </table>

    <!-- Passenger Manifest Table (Complete full headings) -->
    <table class="manifest-table">
        <thead>
            <tr>
                <th class="sn-col">{{ __('Seat #') }}<br><span style="font-size: 5.5pt; font-weight: normal;">{{ __('المقعد') }}</span></th>
                <th class="name-col">{{ __('Passenger Name') }}<br><span style="font-size: 5.5pt; font-weight: normal;">{{ __('اسم الراكب') }}</span></th>
                <th class="nat-col">{{ __('Nationality') }}<br><span style="font-size: 5.5pt; font-weight: normal;">{{ __('الجنسية') }}</span></th>
                <th class="iqama-col">{{ __('Iqama / Passport') }}<br><span style="font-size: 5.5pt; font-weight: normal;">{{ __('رقم الهوية / الجواز') }}</span></th>
                <th class="mobile-col">{{ __('Mobile Number') }}<br><span style="font-size: 5.5pt; font-weight: normal;">{{ __('رقم الجوال') }}</span></th>
                <th class="tkt-col">{{ __('Ticket #') }}<br><span style="font-size: 5.5pt; font-weight: normal;">{{ __('رقم التذكرة') }}</span></th>
                <th class="day-col">{{ __('Duration') }}<br><span style="font-size: 5.5pt; font-weight: normal;">{{ __('المدة') }}</span></th>
                <th class="loc-col">{{ __('Pickup Office') }}<br><span style="font-size: 5.5pt; font-weight: normal;">{{ __('موقع الركوب') }}</span></th>
                <th class="unp-col">{{ __('Unpaid') }}<br><span style="font-size: 5.5pt; font-weight: normal;">{{ __('المتبقي') }}</span></th>
                <th class="sar-col">{{ __('Total (SAR)') }}<br><span style="font-size: 5.5pt; font-weight: normal;">{{ __('المبلغ (ريال)') }}</span></th>
                <th class="htl-col">{{ __('Hotel') }}<br><span style="font-size: 5.5pt; font-weight: normal;">{{ __('الفندق') }}</span></th>
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

                        $dayTag = ($trip->duration_days ?: 3) . 'DAY';
                        $loc = $b->pickup_location ?: ($b->shop?->name ?: 'OFICE');
                        if (strlen($loc) > 11) {
                            $loc = substr($loc, 0, 10) . '.';
                        }

                        $tkt = preg_replace('/[^0-9]/', '', $b->booking_reference);
                        $tkt = !empty($tkt) ? substr($tkt, -4) : $b->id;

                        $htl = $b->package_type === 'with_hotel' ? ($b->effective_hotel_name ?: 'HTL') : '--';
                        if (strlen($htl) > 11) {
                            $htl = substr($htl, 0, 10) . '.';
                        }
                    }
                @endphp
                <tr>
                    <td class="sn-col">{{ $sn }}</td>
                    <td class="name-col">
                        @if($b)
                            {{ strtoupper($b->passenger_name) }}
                        @else
                            <span style="color: #9ca3af; font-weight: normal;">-- AVAILABLE --</span>
                        @endif
                    </td>
                    <td class="nat-col">{{ $b ? $nat : '' }}</td>
                    <td class="iqama-col">{{ $b ? ($b->passenger_id_number ?: '--') : '' }}</td>
                    <td class="mobile-col">{{ $b ? ($b->passenger_phone ?: '--') : '' }}</td>
                    <td class="tkt-col">{{ $b ? $tkt : '' }}</td>
                    <td class="day-col">{{ $b ? $dayTag : '' }}</td>
                    <td class="loc-col">{{ $b ? $loc : '' }}</td>
                    <td class="unp-col">{{ $b ? ((float)$b->balance_due > 0 ? number_format($b->balance_due, 0) : '0') : '' }}</td>
                    <td class="sar-col">{{ $b ? number_format($b->total_amount, 0) : '' }}</td>
                    <td class="htl-col">{{ $b ? $htl : '' }}</td>
                </tr>
            @endfor
        </tbody>
    </table>

    <!-- Totals Summary Bar -->
    <table class="summary-table">
        <tr>
            <td style="width: 25%;">{{ __('BOOKED SEATS') }}: <span style="color: #0A6FB7;">{{ $bookedCount }} / {{ $totalSeats }}</span></td>
            <td style="width: 25%;">{{ __('TOTAL COLLECTED (PAID)') }}: <span style="color: #059669;">SAR {{ number_format($sumPaid, 2) }}</span></td>
            <td style="width: 25%; color: #dc2626;">{{ __('TOTAL UNPAID') }}: SAR {{ number_format($sumUnpaid, 2) }}</td>
            <td style="width: 25%; color: #0A6FB7;">{{ __('GROSS TRIP VALUE (SAR)') }}: SAR {{ number_format($sumTotal, 2) }}</td>
        </tr>
    </table>

    <!-- Signatures -->
    <table class="signatures-table">
        <tr>
            <td style="width: 33%;">
                <div style="font-weight: bold; margin-bottom: 18px;">{{ __('Driver 1 Signature / توقيع واستلام السائق') }}</div>
                <div style="color: #6b7280; font-size: 6.5pt;">{{ $trip->driver_1_name ?: 'Adnan Khan' }} ____________________</div>
            </td>
            <td style="width: 34%;">
                <div style="font-weight: bold; margin-bottom: 18px;">{{ __('Bus Conductor / موجه ومسؤول الحافلة') }}</div>
                <div style="color: #6b7280; font-size: 6.5pt;">________________________________________</div>
            </td>
            <td style="width: 33%;">
                <div style="font-weight: bold; margin-bottom: 18px;">{{ __('Agency Stamp / ختم وتوقيع المنشأة') }}</div>
                <div style="color: #6b7280; font-size: 6.5pt;">{{ $trip->shop?->name ?? 'Verified Agency Official' }}</div>
            </td>
        </tr>
    </table>

    <div style="text-align: center; font-size: 6pt; color: #6b7280; margin-top: 3px;">
        {{ __('Official Bus Trip Manifest & Passenger Statement Sheet') }} &bull; {{ __('Generated on:') }} {{ date('d M Y - h:i A') }} &bull; {{ __('Trip Code:') }} {{ $trip->trip_code }}
    </div>

</body>
</html>
