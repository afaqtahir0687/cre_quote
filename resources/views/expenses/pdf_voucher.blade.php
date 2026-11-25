@php
    $isAr = app()->getLocale() === 'ar';
    $dir = $isAr ? 'rtl' : 'ltr';

    $shop = $expense->shop ?: current_shop();
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

    $voucherNumber = 'EXP-' . str_pad($expense->id, 5, '0', STR_PAD_LEFT);
@endphp
<!DOCTYPE html>
<html dir="{{ $dir }}" lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('Payment Voucher') }} - {{ $voucherNumber }}</title>
    <style>
        @page {
            margin: 2.5mm 3.5mm;
        }
        body {
            font-family: 'amiri', 'dejavusans', sans-serif;
            font-size: 7.5pt;
            color: #111827;
            direction: {{ $dir }};
            text-align: {{ $isAr ? 'right' : 'left' }};
            line-height: 1.18;
            padding: 0;
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td, th {
            vertical-align: top;
            padding: 1.5px 3px;
        }

        /* Outer Box matching Travel Voucher Ticket */
        .ticket-box {
            border: 1.2px solid #008cc0;
            padding: 3px 5px;
            background-color: #ffffff;
        }

        /* Header */
        .header-table td {
            vertical-align: middle;
        }
        .agency-title {
            font-size: 9.5pt;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
            line-height: 1.1;
        }
        .agency-meta {
            font-size: 6pt;
            color: #475569;
            line-height: 1.15;
        }
        .pnr-box {
            text-align: {{ $isAr ? 'left' : 'right' }};
        }
        .pnr-badge {
            display: inline-block;
            background-color: #008cc0;
            color: #ffffff;
            font-weight: bold;
            font-size: 8.5pt;
            padding: 1.5px 6px;
            border-radius: 3px;
            letter-spacing: 0.5px;
            font-family: monospace;
        }

        /* Banner matching Route Table */
        .banner-table {
            margin-top: 3px;
            background-color: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 3px;
        }
        .banner-table td {
            padding: 2.5px 5px;
            vertical-align: middle;
        }
        .field-label {
            font-size: 6pt;
            color: #0369a1;
            font-weight: bold;
            text-transform: uppercase;
        }
        .field-title {
            font-size: 9pt;
            font-weight: bold;
            color: #0c4a6e;
        }
        .highlight-amount-box {
            background-color: #059669;
            color: #ffffff;
            text-align: center;
            padding: 3px 6px;
            border-radius: 3px;
            font-weight: bold;
        }
        .highlight-amount-text {
            font-size: 11pt;
            font-weight: bold;
            color: #ffffff;
            line-height: 1;
            font-family: monospace;
        }

        /* Info Grid Table */
        .info-grid-table {
            margin-top: 3px;
            border: 1px solid #e2e8f0;
            background-color: #fafafa;
        }
        .info-grid-table td {
            padding: 2.5px 5px;
            border: 1px solid #e2e8f0;
            font-size: 7.2pt;
        }
        .grid-lbl {
            font-size: 5.8pt;
            color: #64748b;
            font-weight: bold;
            text-transform: uppercase;
        }
        .grid-val {
            font-size: 7.5pt;
            font-weight: bold;
            color: #0f172a;
        }

        /* Financial Statement Table */
        .fare-table {
            margin-top: 3px;
            border: 1px solid #cbd5e1;
        }
        .fare-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-size: 6.2pt;
            font-weight: bold;
            border: 1px solid #cbd5e1;
            padding: 2px 4px;
            text-transform: uppercase;
        }
        .fare-table td {
            border: 1px solid #e2e8f0;
            padding: 2.5px 4px;
            font-size: 6.8pt;
        }
        .fare-total-row td {
            background-color: #f8fafc;
            font-weight: bold;
            font-size: 7.5pt;
            border-top: 1.5px solid #008cc0;
        }

        /* Notes Box */
        .notes-box {
            margin-top: 3px;
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            padding: 2.5px 5px;
            font-size: 6pt;
            color: #1e40af;
            line-height: 1.2;
            border-radius: 2px;
        }

        /* Signatures Section */
        .signatures-table {
            margin-top: 6px;
            border-collapse: collapse;
            width: 100%;
        }
        .signatures-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 2px 4px;
        }
        .sig-box {
            border-top: 1px dashed #64748b;
            margin-top: 24px;
            padding-top: 2px;
            font-size: 6.5pt;
            font-weight: bold;
            color: #334155;
        }
        .sig-sub {
            font-size: 5.8pt;
            color: #64748b;
            font-weight: normal;
        }

        /* Footer Bar */
        .footer-strip {
            margin-top: 4px;
            padding-top: 2px;
            border-top: 0.5px solid #e2e8f0;
            font-size: 5.5pt;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="ticket-box">
    <!-- Top Header: Logo + Agency Details + Voucher Number -->
    <table class="header-table">
        <tr>
            <td style="width: 58%; vertical-align: middle;">
                <table style="width: 100%;">
                    <tr>
                        @if($logoPath && file_exists($logoPath))
                            <td style="width: 68px; vertical-align: middle; padding: 0; text-align: center;">
                                <img src="{{ $logoPath }}" style="max-height: 44px; max-width: 65px; object-fit: contain;" alt="Logo" />
                            </td>
                        @endif
                        <td style="vertical-align: middle; padding-{{ $isAr ? 'right' : 'left' }}: 5px;">
                            <div class="agency-title">{{ $brandName }}</div>
                            @if($companyTagline)
                                <div style="font-size: 6.5pt; color: #0284c7; font-style: italic; margin-top: -1px; margin-bottom: 1px;">{{ tdb($companyTagline) }}</div>
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
            <td style="width: 42%; vertical-align: middle; text-align: {{ $isAr ? 'left' : 'right' }};">
                <div style="font-size: 6.2pt; font-weight: bold; color: #008cc0; text-transform: uppercase; white-space: nowrap;">
                    {{ __('PAYMENT VOUCHER / سند صرف مالي') }}
                </div>
                <div style="font-size: 6pt; color: #64748b; margin-top: 1px;">
                    {{ __('Voucher Number / رقم السند') }}
                </div>
                <div class="pnr-badge" dir="ltr">
                    {{ $voucherNumber }}
                </div>
                <div style="font-size: 6pt; color: #64748b; margin-top: 1px;">
                    {{ __('Date') }}: <span dir="ltr"><strong>{{ $expense->expense_date->format('d M Y') }}</strong></span>
                    &bull; <strong>{{ __('Staff') }}:</strong> {{ $expense->user?->name ?: __('Staff') }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Banner: Office & Staff + Category + Highlighted Amount -->
    <table class="banner-table">
        <tr>
            <td style="width: 42%;">
                <div class="field-label">{{ __('Office / Branch / الفرع أو المكتب') }}</div>
                <div class="field-title">{{ $expense->shop?->name ?: __('Head Office') }}</div>
                <div style="font-size: 6.5pt; color: #334155; margin-top: 1px;">
                    <strong>{{ __('Recorded By') }}:</strong> {{ $expense->user?->name ?: __('Staff Member') }}
                    @if($expense->employee?->designation) ({{ $expense->employee->designation }}) @endif
                </div>
            </td>
            <td style="width: 26%; text-align: center; vertical-align: middle;">
                <div style="font-size: 6pt; font-weight: bold; color: #0369a1; background-color: #e0f2fe; padding: 2px 5px; border-radius: 3px; display: inline-block;">
                    {{ $expense->categoryLabel() }}
                </div>
                <div style="font-size: 6pt; color: #475569; margin-top: 2px;">
                    {{ __('Payment') }}: <strong>{{ $expense->paymentMethodLabel() }}</strong>
                </div>
            </td>
            <td style="width: 32%; text-align: {{ $isAr ? 'left' : 'right' }};">
                <div class="highlight-amount-box">
                    <div style="font-size: 6pt; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('AMOUNT PAID / المبلغ المصروف') }}</div>
                    <div class="highlight-amount-text" dir="ltr">{{ format_currency($expense->amount) }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Expense Details Grid -->
    <table class="info-grid-table">
        <tr>
            <td style="width: 55%;">
                <div class="grid-lbl">{{ __('Expense Title / البند والوصف') }}</div>
                <div class="grid-val" style="font-size: 8pt; color: #0f172a;">{{ $expense->title }}</div>
                <div style="font-size: 6.8pt; color: #64748b; margin-top: 1px;">
                    <strong>{{ __('Category') }}:</strong> {{ $expense->categoryLabel() }}
                </div>
            </td>
            <td style="width: 45%;">
                <div class="grid-lbl">{{ __('Payment Method & Reference / المرجع وطريقة الدفع') }}</div>
                <div class="grid-val" style="font-size: 7.5pt;">
                    {{ $expense->paymentMethodLabel() }}
                    @if($expense->reference_no)
                        &bull; <span dir="ltr" style="font-family: monospace; color: #0284c7;">#{{ $expense->reference_no }}</span>
                    @endif
                </div>
                <div style="font-size: 6.8pt; color: #64748b; margin-top: 1px;">
                    <strong>{{ __('Date of Expense') }}:</strong> <span dir="ltr">{{ $expense->expense_date->format('d/m/Y') }}</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Detailed Expense Financial Statement -->
    <table class="fare-table">
        <thead>
            <tr>
                <th style="width: 45%; text-align: {{ $isAr ? 'right' : 'left' }};">{{ __('Description / البيان') }}</th>
                <th style="width: 20%; text-align: center;">{{ __('Category / التصنيف') }}</th>
                <th style="width: 15%; text-align: center;">{{ __('Payment Method') }}</th>
                <th style="width: 20%; text-align: {{ $isAr ? 'left' : 'right' }};">{{ __('Amount / المبلغ') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>{{ $expense->title }}</strong>
                    @if($expense->reference_no)
                        <div style="font-size: 6pt; color: #64748b;">Ref/Invoice: {{ $expense->reference_no }}</div>
                    @endif
                </td>
                <td style="text-align: center;">{{ $expense->categoryLabel() }}</td>
                <td style="text-align: center;">{{ $expense->paymentMethodLabel() }}</td>
                <td style="text-align: {{ $isAr ? 'left' : 'right' }}; font-weight: bold; font-family: monospace;">
                    <span dir="ltr">{{ format_currency($expense->amount) }}</span>
                </td>
            </tr>

            <!-- Total Row -->
            <tr class="fare-total-row">
                <td colspan="3" style="text-align: {{ $isAr ? 'left' : 'right' }};">
                    <strong>{{ __('NET AMOUNT PAID / صافي المبلغ المنصرف') }}:</strong>
                </td>
                <td style="text-align: {{ $isAr ? 'left' : 'right' }}; color: #059669; font-size: 8.5pt;">
                    <span dir="ltr">{{ format_currency($expense->amount) }}</span>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Notes & Remarks (matching voucher notes box) -->
    @if($expense->notes)
        <table class="notes-box">
            <tr>
                <td>
                    <strong>{{ __('Remarks & Details / ملاحظات وتفاصيل الصرف') }}:</strong>
                    <span>{{ $expense->notes }}</span>
                </td>
            </tr>
        </table>
    @endif

    @if($expense->attachment)
        <table style="margin-top: 3px; background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 2px; padding: 2px 4px;">
            <tr>
                <td style="font-size: 6pt; color: #166534;">
                    <strong style="color: #15803d;">&#10003; {{ __('Receipt / Bill Verified') }}:</strong>
                    {{ __('Official bill attachment is on file with this voucher.') }}
                    (<span dir="ltr">{{ basename($expense->attachment) }}</span>)
                </td>
            </tr>
        </table>
    @endif

    <!-- Official Signatures -->
    <table class="signatures-table">
        <tr>
            <td>
                <div class="sig-box">
                    {{ __('Prepared By (Staff)') }}<br>
                    <span class="sig-sub">{{ $expense->user?->name ?: __('Staff Member') }}</span>
                </div>
            </td>
            <td>
                <div class="sig-box">
                    {{ __('Reviewed & Checked By') }}<br>
                    <span class="sig-sub">{{ __('Accountant / تدقيق الحسابات') }}</span>
                </div>
            </td>
            <td>
                <div class="sig-box">
                    {{ __('Authorized Approval') }}<br>
                    <span class="sig-sub">{{ __('Manager Signature & Stamp / الختم والاعتماد') }}</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Footer Security / System Strip -->
    <div class="footer-strip">
        {{ $brandName }} &bull; {{ __('System Generated Official Expense Voucher') }} &bull;
        {{ __('Printed on') }}: <span dir="ltr">{{ \Carbon\Carbon::now()->format('d M Y, h:i A') }}</span> &bull;
        ID: <span dir="ltr">{{ $voucherNumber }}</span>
    </div>
</div>

</body>
</html>
