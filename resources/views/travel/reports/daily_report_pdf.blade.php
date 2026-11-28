@php
    $isAr = app()->getLocale() === 'ar';
    $dir = $isAr ? 'rtl' : 'ltr';

    $shop = $selectedOffice ?: ($currentShop ?: current_shop());
    $setting = $shop?->setting ?: \App\Models\Setting::withoutGlobalScopes()->where('shop_id', $shop?->id)->first();

    // Brand Name
    $brandName = shop_brand_name($shop, $setting);
    if (empty($brandName) || $brandName === 'CRE Quotation') {
        $brandName = $shop->name ?? 'Umrah & Travel Agency';
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

    // Logo resolution
    $logoPath = shop_logo_path($shop, $setting);
    if (!($logoPath && file_exists($logoPath))) {
        $logoPath = platform_logo_path();
    }

    $closingRef = 'CLS-' . date('Ymd', strtotime($startDate)) . ($selectedOffice ? '-B' . $selectedOffice->id : '');
@endphp
<!DOCTYPE html>
<html dir="{{ $dir }}" lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('Daily Staff Sales & Expenses Statement') }} - {{ $closingRef }}</title>
    <style>
        @page {
            margin: 4mm 4mm 4mm 4mm;
        }
        body {
            font-family: 'amiri', 'dejavusans', sans-serif;
            font-size: 7.2pt;
            color: #1e293b;
            direction: {{ $dir }};
            text-align: {{ $isAr ? 'right' : 'left' }};
            line-height: 1.15;
            padding: 0;
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td, th {
            vertical-align: middle;
            padding: 2px 3px;
        }

        /* Outer Box matching Voucher Ticket */
        .ticket-box {
            border: 1.2px solid #008cc0;
            padding: 4px;
            background-color: #ffffff;
        }

        /* Header */
        .header-table td {
            vertical-align: middle;
        }
        .agency-title {
            font-size: 10pt;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
            line-height: 1.1;
        }
        .agency-meta {
            font-size: 6pt;
            color: #475569;
            line-height: 1.2;
        }
        .report-badge {
            display: inline-block;
            background-color: #008cc0;
            color: #ffffff;
            font-weight: bold;
            font-size: 8.5pt;
            padding: 2px 7px;
            border-radius: 3px;
            letter-spacing: 0.5px;
        }
        .report-sub {
            font-size: 6.2pt;
            color: #64748b;
            margin-top: 1px;
        }

        /* Banner */
        .banner-table {
            margin-top: 3px;
            background-color: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 3px;
        }
        .banner-label {
            font-size: 5.8pt;
            color: #0369a1;
            font-weight: bold;
            text-transform: uppercase;
        }
        .banner-val {
            font-size: 7.5pt;
            font-weight: bold;
            color: #0c4a6e;
        }

        /* KPI Cards Grid */
        .kpi-table {
            margin-top: 3px;
            width: 100%;
        }
        .kpi-table td {
            padding: 1.5px;
        }
        .kpi-card {
            border: 1px solid #cbd5e1;
            border-radius: 3px;
            padding: 3px 5px;
            text-align: center;
            background-color: #f8fafc;
        }
        .kpi-label {
            font-size: 5.6pt;
            text-transform: uppercase;
            font-weight: bold;
            color: #64748b;
        }
        .kpi-num {
            font-size: 8.5pt;
            font-weight: bold;
            font-family: monospace;
            margin-top: 1px;
        }
        .kpi-primary { background-color: #eff6ff; border-color: #bfdbfe; color: #1d4ed8; }
        .kpi-success { background-color: #f0fdf4; border-color: #bbf7d0; color: #15803d; }
        .kpi-danger  { background-color: #fef2f2; border-color: #fecaca; color: #b91c1c; }
        .kpi-dark    { background-color: #0f172a; border-color: #0f172a; color: #ffffff; }

        /* Section Title Bar */
        .section-bar {
            margin-top: 4px;
            margin-bottom: 2px;
            background-color: #008cc0;
            color: #ffffff;
            padding: 2px 5px;
            font-size: 7pt;
            font-weight: bold;
            border-radius: 2px;
        }
        .section-bar-sub {
            background-color: #334155;
            color: #ffffff;
            padding: 1.5px 4px;
            font-size: 6.5pt;
            font-weight: bold;
            border-radius: 2px;
            margin-top: 4px;
            margin-bottom: 2px;
        }

        /* Data Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 6.4pt;
        }
        .data-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: bold;
            border: 0.8px solid #cbd5e1;
            padding: 2px 3px;
            text-align: {{ $isAr ? 'right' : 'left' }};
        }
        .data-table td {
            border: 0.8px solid #e2e8f0;
            padding: 2px 3px;
        }
        .data-table tr.total-row th,
        .data-table tr.total-row td {
            background-color: #e2e8f0;
            font-weight: bold;
            border-top: 1.2px solid #64748b;
            color: #0f172a;
        }
        .text-center { text-align: center !important; }
        .text-right { text-align: {{ $isAr ? 'left' : 'right' }} !important; }
        .text-left { text-align: {{ $isAr ? 'right' : 'left' }} !important; }
        .text-mono { font-family: monospace; }
        .text-success { color: #16a34a; }
        .text-danger { color: #dc2626; }
        .text-primary { color: #0284c7; }
        .fw-bold { font-weight: bold; }

        /* Signatures */
        .sig-table {
            margin-top: 5px;
            border-top: 1px dashed #94a3b8;
            padding-top: 3px;
        }
        .sig-box {
            text-align: center;
            font-size: 6pt;
            color: #475569;
        }
        .sig-line {
            margin-top: 16px;
            border-top: 0.8px solid #64748b;
            font-weight: bold;
            color: #1e293b;
            padding-top: 2px;
        }
    </style>
</head>
<body>
<div class="ticket-box">

    {{-- Header --}}
    <table class="header-table">
        <tr>
            <td style="width: 55px; vertical-align: middle;">
                @if($logoPath && file_exists($logoPath))
                    <img src="{{ $logoPath }}" style="max-height: 42px; max-width: 50px;" alt="Logo">
                @else
                    <div style="width: 42px; height: 42px; background-color: #008cc0; color: #fff; font-size: 11pt; font-weight: bold; line-height: 42px; text-align: center; border-radius: 4px;">
                        {{ mb_substr($brandName, 0, 1) }}
                    </div>
                @endif
            </td>
            <td style="vertical-align: middle;">
                <div class="agency-title">{{ $brandName }}</div>
                <div class="agency-meta">
                    @if($companyAddress) <span>{{ $companyAddress }}</span>@endif
                    @if($companyPhone) | <span>{{ __('Tel') }}: {{ $companyPhone }}</span>@endif
                    @if($companyCr) | <span>{{ __('CR') }}: {{ $companyCr }}</span>@endif
                    @if($companyVat) | <span>{{ __('VAT') }}: {{ $companyVat }}</span>@endif
                </div>
            </td>
            <td style="width: 175px; text-align: {{ $isAr ? 'left' : 'right' }}; vertical-align: middle;">
                <div class="report-badge">
                    @if(($reportType ?? 'full') === 'staff_summary')
                        {{ __('STAFF PERFORMANCE SUMMARY') }}
                    @else
                        {{ __('DAILY CLOSING STATEMENT') }}
                    @endif
                </div>
                <div class="report-sub">
                    <b>{{ __('Ref') }}:</b> <span class="text-mono">{{ $closingRef }}</span><br>
                    <b>{{ __('Printed') }}:</b> {{ now()->format('d/m/Y h:i A') }}
                </div>
            </td>
        </tr>
    </table>

    {{-- Banner / Parameters --}}
    <table class="banner-table">
        <tr>
            <td style="width: 28%;">
                <div class="banner-label">{{ __('Statement Period') }} / {{ __('الفترة') }}</div>
                <div class="banner-val">{{ $periodLabel }}</div>
            </td>
            <td style="width: 27%;">
                <div class="banner-label">{{ __('Office / Branch') }} / {{ __('الفرع / المكتب') }}</div>
                <div class="banner-val">{{ $selectedOffice ? $selectedOffice->name : __('All Offices & Branches') }}</div>
            </td>
            <td style="width: 25%;">
                <div class="banner-label">{{ __('Generated By') }} / {{ __('المستخدم') }}</div>
                <div class="banner-val">{{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role ?? 'Staff') }})</div>
            </td>
            <td style="width: 20%; text-align: {{ $isAr ? 'left' : 'right' }};">
                <div class="banner-label">{{ __('Active Staff') }} / {{ __('عدد الموظفين') }}</div>
                <div class="banner-val">{{ count($staffStats) }} {{ __('Staff Members') }}</div>
            </td>
        </tr>
    </table>

    {{-- 5 KPI Metric Cards --}}
    <table class="kpi-table">
        <tr>
            <td style="width: 20%;">
                <div class="kpi-card kpi-primary">
                    <div class="kpi-label">{{ __('Total Seats Booked') }}</div>
                    <div class="kpi-num">{{ number_format($grandSeats) }} <span style="font-size: 6pt;">{{ __('Seats') }}</span></div>
                </div>
            </td>
            <td style="width: 20%;">
                <div class="kpi-card">
                    <div class="kpi-label">{{ __('Gross Sales') }}</div>
                    <div class="kpi-num">{{ currency_symbol() }} {{ number_format($grandSales, 2) }}</div>
                </div>
            </td>
            <td style="width: 20%;">
                <div class="kpi-card kpi-success">
                    <div class="kpi-label">{{ __('Cash Collected') }} (IN)</div>
                    <div class="kpi-num text-success">+{{ currency_symbol() }} {{ number_format($grandCashIn, 2) }}</div>
                </div>
            </td>
            <td style="width: 20%;">
                <div class="kpi-card kpi-danger">
                    <div class="kpi-label">{{ __('Expenses Paid') }} (OUT)</div>
                    <div class="kpi-num text-danger">-{{ currency_symbol() }} {{ number_format($grandExpenses, 2) }}</div>
                </div>
            </td>
            <td style="width: 20%;">
                <div class="kpi-card kpi-dark">
                    <div class="kpi-label" style="color: #93c5fd;">{{ __('Net Cash Handover') }}</div>
                    <div class="kpi-num" style="color: #34d399;">{{ currency_symbol() }} {{ number_format($grandNetCash, 2) }}</div>
                </div>
            </td>
        </tr>
    </table>

    {{-- SECTION 1: Per-Employee Daily Performance & Cash Position Summary --}}
    <div class="section-bar">
        @if(($reportType ?? 'full') === 'staff_summary')
            {{ __('PER-EMPLOYEE DAILY PERFORMANCE & CASH SUMMARY') }} / {{ __('ملخص أداء الموظفين وحركة النقد اليومية') }}
        @else
            1. {{ __('STAFF DAILY SALES, SEATS & EXPENSES BREAKDOWN') }} / {{ __('ملخص مبيعات ومصاريف الموظفين') }}
        @endif
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 24px;" class="text-center">#</th>
                <th>{{ __('Employee / Agent') }}</th>
                <th>{{ __('Branch / Office') }}</th>
                <th class="text-center" style="width: 50px;">{{ __('Seats') }}</th>
                <th class="text-right" style="width: 70px;">{{ __('Gross Sales') }}</th>
                <th class="text-right" style="width: 70px;">{{ __('Cash In') }}</th>
                <th class="text-right" style="width: 65px;">{{ __('Due Balance') }}</th>
                <th class="text-right" style="width: 70px;">{{ __('Expenses') }}</th>
                <th class="text-right" style="width: 75px;">{{ __('Net Cash') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($staffStats as $stat)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="fw-bold">{{ $stat['name'] }}</td>
                    <td>{{ $stat['office'] }}</td>
                    <td class="text-center fw-bold text-mono">{{ $stat['seats_count'] }}</td>
                    <td class="text-right text-mono">{{ number_format($stat['gross_sales'], 2) }}</td>
                    <td class="text-right text-mono text-success fw-bold">+{{ number_format($stat['cash_collected'], 2) }}</td>
                    <td class="text-right text-mono text-danger">{{ number_format($stat['balance_due'], 2) }}</td>
                    <td class="text-right text-mono text-danger">-{{ number_format($stat['expenses'], 2) }}</td>
                    <td class="text-right text-mono fw-bold" style="background-color: #f8fafc; color: {{ $stat['net_cash'] >= 0 ? '#15803d' : '#b91c1c' }};">
                        {{ currency_symbol() }} {{ number_format($stat['net_cash'], 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 10px; color: #64748b;">
                        {{ __('No employee activity or bookings recorded for this date.') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <th colspan="3" class="text-right">{{ __('GRAND TOTAL') }} / {{ __('الإجمالي العام') }}:</th>
                <th class="text-center text-mono fw-bold">{{ $grandSeats }}</th>
                <th class="text-right text-mono fw-bold">{{ currency_symbol() }} {{ number_format($grandSales, 2) }}</th>
                <th class="text-right text-mono text-success fw-bold">+{{ currency_symbol() }} {{ number_format($grandCashIn, 2) }}</th>
                <th class="text-right text-mono text-danger fw-bold">{{ currency_symbol() }} {{ number_format($grandPendingDue, 2) }}</th>
                <th class="text-right text-mono text-danger fw-bold">-{{ currency_symbol() }} {{ number_format($grandExpenses, 2) }}</th>
                <th class="text-right text-mono fw-bold" style="background-color: #0f172a; color: #34d399;">
                    {{ currency_symbol() }} {{ number_format($grandNetCash, 2) }}
                </th>
            </tr>
        </tfoot>
    </table>

    @if(($reportType ?? 'full') !== 'staff_summary')
    {{-- SECTION 2: Detailed Passenger Bookings of the Day --}}
    @if($bookings->isNotEmpty())
        <div class="section-bar-sub">
            2. {{ __('DETAILED SEAT BOOKINGS') }} ({{ $bookings->count() }} {{ __('Seats') }}) / {{ __('تفاصيل حجوزات المقاعد') }}
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 22px;" class="text-center">#</th>
                    <th style="width: 58px;">{{ __('Booking #') }}</th>
                    <th>{{ __('Passenger / Customer') }}</th>
                    <th>{{ __('Trip Route') }}</th>
                    <th class="text-center" style="width: 35px;">{{ __('Seat') }}</th>
                    <th class="text-right" style="width: 55px;">{{ __('Total Fare') }}</th>
                    <th class="text-right" style="width: 55px;">{{ __('Paid') }}</th>
                    <th class="text-right" style="width: 50px;">{{ __('Due') }}</th>
                    <th style="width: 75px;">{{ __('Agent') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings->take(25) as $bIndex => $b)
                    <tr>
                        <td class="text-center">{{ $bIndex + 1 }}</td>
                        <td class="text-mono fw-bold">{{ $b->booking_reference }}</td>
                        <td>
                            <b>{{ $b->passenger_name }}</b>
                            @if($b->passenger_phone) <span style="font-size: 5.5pt; color: #64748b;">({{ $b->passenger_phone }})</span>@endif
                        </td>
                        <td>
                            {{ $b->trip?->origin_city ?? 'Riyadh' }} &rarr; {{ $b->trip?->destination_city ?? 'Makkah' }}
                            @if($b->trip?->trip_date) <span style="font-size: 5.5pt; color: #64748b;">({{ date('d/m', strtotime($b->trip->trip_date)) }})</span>@endif
                        </td>
                        <td class="text-center text-mono fw-bold">#{{ $b->seat_number }}</td>
                        <td class="text-right text-mono">{{ number_format($b->total_amount, 2) }}</td>
                        <td class="text-right text-mono text-success">{{ number_format($b->paid_amount, 2) }}</td>
                        <td class="text-right text-mono text-danger">{{ number_format($b->balance_due, 2) }}</td>
                        <td>{{ $b->salesman_name ?: ($b->creator?->name ?: __('Agent')) }}</td>
                    </tr>
                @endforeach
                @if($bookings->count() > 25)
                    <tr>
                        <td colspan="9" class="text-center" style="font-style: italic; color: #64748b; padding: 2px;">
                            ... {{ __('and :count more bookings in this period', ['count' => $bookings->count() - 25]) }} ...
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    @endif

    {{-- SECTION 3: Detailed Office Expenses of the Day --}}
    @if($expenses->isNotEmpty())
        <div class="section-bar-sub">
            3. {{ __('DETAILED OFFICE EXPENSES') }} ({{ $expenses->count() }} {{ __('Records') }}) / {{ __('تفاصيل المصاريف اليومية') }}
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 22px;" class="text-center">#</th>
                    <th style="width: 55px;">{{ __('Voucher #') }}</th>
                    <th>{{ __('Branch / Office') }}</th>
                    <th>{{ __('Category') }}</th>
                    <th>{{ __('Description') }}</th>
                    <th>{{ __('Recorded By') }}</th>
                    <th style="width: 60px;">{{ __('Method') }}</th>
                    <th class="text-right" style="width: 65px;">{{ __('Amount') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expenses->take(15) as $eIndex => $e)
                    <tr>
                        <td class="text-center">{{ $eIndex + 1 }}</td>
                        <td class="text-mono">EXP-{{ str_pad($e->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $e->shop?->name ?? '-' }}</td>
                        <td class="fw-bold">{{ $e->category ?: __('General') }}</td>
                        <td>{{ $e->description }}</td>
                        <td>{{ $e->user?->name ?: ($e->employee?->name ?: 'Staff') }}</td>
                        <td>{{ ucfirst($e->payment_method ?: 'cash') }}</td>
                        <td class="text-right text-mono text-danger fw-bold">-{{ number_format($e->amount, 2) }}</td>
                    </tr>
                @endforeach
                @if($expenses->count() > 15)
                    <tr>
                        <td colspan="8" class="text-center" style="font-style: italic; color: #64748b; padding: 2px;">
                            ... {{ __('and :count more expenses in this period', ['count' => $expenses->count() - 15]) }} ...
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    @endif
    @endif

    {{-- Signatures & Cash Handover Authorization --}}
    <table class="sig-table">
        <tr>
            <td style="width: 33%;" class="sig-box">
                <div>{{ __('Shift Cashier / Staff') }} / {{ __('أمين الصندوق') }}</div>
                <div class="sig-line">{{ auth()->user()->name }}</div>
            </td>
            <td style="width: 34%;" class="sig-box">
                <div>{{ __('Accountant / Cash Verifier') }} / {{ __('المحاسب المعتمد') }}</div>
                <div class="sig-line">{{ __('Signature & Date') }}</div>
            </td>
            <td style="width: 33%;" class="sig-box">
                <div>{{ __('Branch Manager Approval') }} / {{ __('مدير الفرع والختم') }}</div>
                <div class="sig-line">{{ __('Official Stamp & Approval') }}</div>
            </td>
        </tr>
    </table>

    <div style="margin-top: 4px; text-align: center; font-size: 5.5pt; color: #94a3b8;">
        {{ __('Official Daily Shift Closing Statement. Generated electronically via Umrah & Travel ERP.') }} &bull; {{ config('app.name') }}
    </div>

</div>
</body>
</html>
