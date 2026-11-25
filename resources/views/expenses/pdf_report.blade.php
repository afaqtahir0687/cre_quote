<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Daily Office Expense Report') }}</title>
    <style>
        @page {
            header: page-header;
            footer: page-footer;
            margin-top: 15mm;
            margin-bottom: 15mm;
            margin-left: 8mm;
            margin-right: 8mm;
        }
        body {
            font-family: 'amiri', 'dejavusans', sans-serif;
            font-size: 8.5pt;
            color: #1e293b;
            line-height: 1.35;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .report-title {
            font-size: 14pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .sub-title {
            font-size: 8pt;
            color: #64748b;
            margin-top: 2px;
        }
        .meta-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 6px 10px;
            margin-bottom: 12px;
            width: 100%;
        }
        .meta-box table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-box td {
            font-size: 8pt;
            padding: 2px 4px;
        }
        .meta-lbl {
            color: #64748b;
            font-weight: bold;
            width: 15%;
        }
        .meta-val {
            color: #0f172a;
            font-weight: bold;
            width: 35%;
        }
        .kpi-row {
            width: 100%;
            margin-bottom: 12px;
        }
        .kpi-table {
            width: 100%;
            border-collapse: collapse;
        }
        .kpi-card {
            border: 1px solid #cbd5e1;
            background: #ffffff;
            border-radius: 4px;
            padding: 6px 10px;
            text-align: center;
        }
        .kpi-title {
            font-size: 7pt;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
        }
        .kpi-num {
            font-size: 11pt;
            font-weight: bold;
            color: #0f172a;
            margin-top: 2px;
        }
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        .table-data th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px 4px;
            text-align: left;
            border: 1px solid #0f172a;
        }
        html[dir="rtl"] .table-data th {
            text-align: right;
        }
        .table-data td {
            padding: 5px 4px;
            font-size: 8pt;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .table-data tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 7pt;
            font-weight: bold;
            border-radius: 3px;
            background-color: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
        }
        .text-end {
            text-align: right;
        }
        html[dir="rtl"] .text-end {
            text-align: left;
        }
        .text-center {
            text-align: center;
        }
        .total-row td {
            background-color: #f1f5f9 !important;
            font-weight: bold;
            border-top: 2px solid #0f172a;
            border-bottom: 2px double #0f172a;
            font-size: 9pt;
        }
        .signatures {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
        }
        .signatures td {
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 10px;
        }
        .sig-line {
            border-top: 1px dashed #64748b;
            margin-top: 35px;
            padding-top: 4px;
            font-size: 7.5pt;
            font-weight: bold;
            color: #334155;
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <table class="header-table">
        <tr>
            <td style="width: 60%; vertical-align: top;">
                @php
                    $brandName = shop_brand_name($currentShop, $setting);
                    $logo = shop_logo_url($currentShop, $setting);
                @endphp
                <div style="font-size: 13pt; font-weight: bold; color: #0f172a;">{{ $brandName }}</div>
                <div class="sub-title">
                    @if($setting && $setting->phone) {{ __('Phone') }}: <span dir="ltr">{{ $setting->phone }}</span> @endif
                    @if($setting && $setting->email) &bull; {{ $setting->email }} @endif
                    @if($setting && $setting->tax_number) &bull; {{ __('VAT / Tax #') }}: <span dir="ltr">{{ $setting->tax_number }}</span> @endif
                </div>
                @if($setting && $setting->address)
                    <div style="font-size: 7.5pt; color: #64748b;">{{ $setting->address }}</div>
                @endif
            </td>
            <td style="width: 40%; text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}; vertical-align: top;">
                <div class="report-title">{{ __('Daily Office Expenses') }}</div>
                <div style="font-size: 8pt; color: #64748b; margin-top: 3px;">
                    <strong>{{ __('Period') }}:</strong> {{ $periodLabel }}
                </div>
                <div style="font-size: 7pt; color: #94a3b8; margin-top: 2px;">
                    {{ __('Generated on') }}: {{ \Carbon\Carbon::now()->format('d M Y, h:i A') }}
                </div>
            </td>
        </tr>
    </table>

    {{-- Meta Box --}}
    <div class="meta-box">
        <table>
            <tr>
                <td class="meta-lbl">{{ __('Office / Branch') }}:</td>
                <td class="meta-val">
                    {{ $selectedOffice ? $selectedOffice->name : ($isAdmin ? __('All Business Offices & Branches') : auth()->user()->shop?->name) }}
                </td>
                <td class="meta-lbl">{{ __('Report Scope') }}:</td>
                <td class="meta-val">{{ $periodLabel }}</td>
            </tr>
            <tr>
                <td class="meta-lbl">{{ __('Total Expenses') }}:</td>
                <td class="meta-val" style="color: #dc2626; font-size: 9.5pt;">
                    <span dir="ltr">{{ format_currency($totalAmount) }}</span>
                </td>
                <td class="meta-lbl">{{ __('Records Count') }}:</td>
                <td class="meta-val">{{ $totalCount }} {{ __('Entries') }}</td>
            </tr>
        </table>
    </div>

    {{-- KPI Cards Row --}}
    <div class="kpi-row">
        <table class="kpi-table">
            <tr>
                <td style="width: 25%; padding-right: 4px;">
                    <div class="kpi-card" style="border-left: 3px solid #dc2626;">
                        <div class="kpi-title">{{ __('Grand Total Amount') }}</div>
                        <div class="kpi-num" style="color: #dc2626;" dir="ltr">{{ format_currency($totalAmount) }}</div>
                    </div>
                </td>
                <td style="width: 25%; padding: 0 4px;">
                    <div class="kpi-card" style="border-left: 3px solid #0284c7;">
                        <div class="kpi-title">{{ __('Total Expense Records') }}</div>
                        <div class="kpi-num">{{ $totalCount }}</div>
                    </div>
                </td>
                <td style="width: 50%; padding-left: 4px;">
                    <div class="kpi-card" style="text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};">
                        <div class="kpi-title">{{ __('Top Categories Breakdown') }}</div>
                        <div style="font-size: 7pt; margin-top: 3px; color: #334155;">
                            @foreach($categorySummary->take(4) as $cKey => $cData)
                                <span style="display: inline-block; margin-right: 6px; margin-left: 6px;">
                                    <strong>{{ $cData['label'] }}:</strong> <span dir="ltr">{{ format_currency($cData['total']) }}</span> ({{ $cData['count'] }})
                                </span>
                            @endforeach
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Detailed Expenses Table --}}
    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 4%; text-align: center;">#</th>
                <th style="width: 10%;">{{ __('Date') }}</th>
                <th style="width: 14%;">{{ __('Office / Branch') }}</th>
                <th style="width: 14%;">{{ __('Category') }}</th>
                <th style="width: 26%;">{{ __('Expense Description & Notes') }}</th>
                <th style="width: 12%;">{{ __('Staff') }}</th>
                <th style="width: 8%;">{{ __('Method') }}</th>
                <th style="width: 12%; text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};">{{ __('Amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $index => $exp)
            <tr>
                <td class="text-center" style="font-size: 7.5pt; color: #64748b;">{{ $index + 1 }}</td>
                <td style="font-weight: bold;">{{ $exp->expense_date->format('d/m/Y') }}</td>
                <td>{{ $exp->shop?->name ?: __('Office') }}</td>
                <td>
                    <span class="badge">{{ $exp->categoryLabel() }}</span>
                </td>
                <td>
                    <strong>{{ $exp->title }}</strong>
                    @if($exp->reference_no)
                        <div style="font-size: 6.5pt; color: #64748b;">Ref: {{ $exp->reference_no }}</div>
                    @endif
                    @if($exp->notes)
                        <div style="font-size: 6.5pt; color: #475569; font-style: italic;">{{ $exp->notes }}</div>
                    @endif
                </td>
                <td>{{ $exp->user?->name ?: __('Staff') }}</td>
                <td style="font-size: 7pt; color: #475569;">{{ $exp->paymentMethodLabel() }}</td>
                <td class="text-end" style="font-weight: bold; color: #0f172a;">
                    <span dir="ltr">{{ format_currency($exp->amount) }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center" style="padding: 15px; color: #64748b;">
                    {{ __('No expense records found for the selected period.') }}
                </td>
            </tr>
            @endforelse

            {{-- Grand Total Row --}}
            <tr class="total-row">
                <td colspan="7" class="text-end" style="padding: 7px 6px;">
                    <strong>{{ __('TOTAL EXPENSES') }} ({{ $totalCount }} {{ __('Entries') }}):</strong>
                </td>
                <td class="text-end" style="color: #dc2626; font-size: 10pt; padding: 7px 4px;">
                    <span dir="ltr">{{ format_currency($totalAmount) }}</span>
                </td>
            </tr>
        </tbody>
    </table>

    {{-- Signatures & Approvals --}}
    <table class="signatures">
        <tr>
            <td>
                <div class="sig-line">
                    {{ __('Prepared By (Staff)') }}<br>
                    <span style="font-weight: normal; font-size: 7pt; color: #64748b;">{{ auth()->user()->name }}</span>
                </div>
            </td>
            <td>
                <div class="sig-line">
                    {{ __('Reviewed By (Accountant)') }}<br>
                    <span style="font-weight: normal; font-size: 7pt; color: #64748b;">{{ __('Signature & Date') }}</span>
                </div>
            </td>
            <td>
                <div class="sig-line">
                    {{ __('Authorized Approval (Branch Manager / Admin)') }}<br>
                    <span style="font-weight: normal; font-size: 7pt; color: #64748b;">{{ __('Signature & Stamp') }}</span>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
