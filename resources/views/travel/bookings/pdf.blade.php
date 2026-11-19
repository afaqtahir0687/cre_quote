@php
    $isAr = app()->getLocale() === 'ar';
    $dir = $isAr ? 'rtl' : 'ltr';

    $shop = $booking->shop;
    $setting = $shop?->setting ?: \App\Models\Setting::withoutGlobalScopes()->where('shop_id', $shop?->id)->first();
    $trip = $booking->trip;

    // Brand Name
    $brandName = shop_brand_name($shop, $setting);
    if (empty($brandName) || $brandName === 'CRE Quotation') {
        $brandName = $shop->name ?? 'Umrah & Travel Agency';
    }

    // Company registration details
    $companyCr = $setting->company_cr_no ?? $shop->cr_number ?? null;
    $companyVat = $setting->company_vat_no ?? $shop->vat_number ?? null;
    $companyPhone = $setting->company_phone ?? $shop->phone ?? null;
    $companyEmail = $setting->company_email ?? $shop->email ?? null;
    $companyAddress = $setting->company_address ?? $shop->address ?? 'Kingdom of Saudi Arabia';
    $companyCity = $setting->company_city ?? '';

    // Logo resolution
    $logoPath = shop_logo_path($shop, $setting);
    if (!($logoPath && file_exists($logoPath))) {
        $logoPath = platform_logo_path();
    }

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
<html dir="{{ $dir }}" lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('Ticket Voucher') }} - {{ $booking->booking_reference }}</title>
    <style>
        @page {
            margin: 5mm 6mm;
        }
        body {
            font-family: 'amiri', 'dejavusans', sans-serif;
            font-size: 9pt;
            color: #111827;
            direction: {{ $dir }};
            text-align: {{ $isAr ? 'right' : 'left' }};
            line-height: 1.25;
            padding: 0;
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td, th {
            vertical-align: top;
            padding: 2px 4px;
        }

        /* Outer Box */
        .ticket-box {
            border: 1.5px solid #008cc0;
            padding: 6px 8px;
            background-color: #ffffff;
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
        .pnr-box {
            text-align: {{ $isAr ? 'left' : 'right' }};
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
        .seat-highlight-box {
            background-color: #008cc0;
            color: #ffffff;
            text-align: center;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
        }
        .seat-numbers-text {
            font-size: 13pt;
            font-weight: bold;
            color: #ffffff;
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
            font-size: 7.5pt;
            font-weight: bold;
            text-align: center;
            padding: 2px 4px;
            border-radius: 3px;
        }
        .footer-note {
            text-align: center;
            font-size: 6.5pt;
            color: #94a3b8;
            margin-top: 3px;
        }
    </style>
</head>
<body>

<div class="ticket-box">
    <!-- Top Header: Logo + Agency Details + PNR -->
    <table class="header-table">
        <tr>
            <td style="width: 50%; vertical-align: middle;">
                <table style="width: 100%;">
                    <tr>
                        @if($logoPath && file_exists($logoPath))
                            <td style="width: 48px; vertical-align: middle; padding: 0;">
                                <img src="{{ $logoPath }}" style="max-height: 38px; max-width: 45px; object-fit: contain;" />
                            </td>
                        @endif
                        <td style="vertical-align: middle; padding-{{ $isAr ? 'right' : 'left' }}: 6px;">
                            <div class="agency-title">{{ $brandName }}</div>
                            <div class="agency-meta">
                                {{ tdb($companyAddress) }}@if($companyCity) - {{ tdb($companyCity) }}@endif
                                @if($companyPhone) &bull; {{ __('Tel') }}: <span dir="ltr">{{ $companyPhone }}</span>@endif
                                @if($companyCr || $companyVat)
                                    <br>
                                    @if($companyCr) <strong>{{ __('CR') }}:</strong> <span dir="ltr">{{ $companyCr }}</span> @endif
                                    @if($companyVat) <strong>{{ __('VAT') }}:</strong> <span dir="ltr">{{ $companyVat }}</span> @endif
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
            <td style="width: 45%;">
                <div class="city-label">{{ __('Origin / نقطة الانطلاق') }}</div>
                <div class="city-title">{{ $trip ? tdb($trip->departure_city) : __('Departure City') }}</div>
                <div class="trip-meta" style="font-size: 6.5pt; white-space: nowrap;">
                    <strong>{{ __('Departure') }}:</strong>
                    <span dir="ltr">{{ $trip && $trip->departure_date ? $trip->departure_date->format('d M Y - h:i A') : __('Scheduled') }}</span>
                </div>
            </td>
            <td style="width: 10%; text-align: center; vertical-align: middle;">
                <div style="font-size: 11pt; font-weight: bold; color: #008cc0;">{!! $isAr ? '&larr;' : '&rarr;' !!}</div>
                <div style="font-size: 6.5pt; font-weight: bold; color: #0369a1; white-space: nowrap;">
                    {{ $trip ? $trip->duration_days : 10 }} {{ __('Days') }}
                </div>
            </td>
            <td style="width: 45%; text-align: {{ $isAr ? 'left' : 'right' }};">
                <div class="city-label">{{ __('Destination / الوجهة') }}</div>
                <div class="city-title">{{ $trip ? tdb($trip->destination_city) : __('Destination') }}</div>
                <div class="trip-meta" style="font-size: 6.5pt; white-space: nowrap;">
                    <strong>{{ __('Coach Fleet') }}:</strong>
                    {{ $trip ? tdb($trip->bus_name) : __('Luxury Coach') }}
                    @if($trip && $trip->bus_number) (<span dir="ltr">{{ $trip->bus_number }}</span>) @endif
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
                        &bull; <strong style="color: #008cc0;">{{ __('Seat') }}: <span dir="ltr">#{{ $booking->seat_number }}</span></strong>
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
                                        <span dir="ltr">#{{ $gb->seat_number }}</span>
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
                                    <span style="font-weight: normal; font-size: 6.8pt; color: #334155;">({{ __($booking->room_type ?: 'quad_sharing') }})</span>
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
                            <div style="font-size: 6pt; color: #64748b; text-transform: uppercase;">{{ __('Agent / مسؤول الحجز') }}:</div>
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
                <th style="font-size: 6.5pt; text-align: {{ $isAr ? 'right' : 'left' }};">
    {{ __('Description / البيان') }}
</th>
<th style="width: 100px; font-size: 6.5pt; text-align: center;">
    {{ __('Status / الحالة') }}
</th>
<th style="width: 120px; font-size: 6.5pt; text-align: {{ $isAr ? 'left' : 'right' }};">
    {{ __('Amount / المبلغ') }} (SAR)
</th>
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
    </div>

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
