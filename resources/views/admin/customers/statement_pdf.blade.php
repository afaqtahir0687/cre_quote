@php
    $userTz = request()->cookie('user_timezone', config('app.timezone'));
    $setting = \App\Models\Setting::first();
    $totalBilled = $invoices->sum('grand_total');
    $totalPaid = $invoices->sum('amount_paid');
    $totalDue = $totalBilled - $totalPaid;
@endphp
<!DOCTYPE html>
<html dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <title>Statement of Account - {{ $customer->name }}</title>
    <style>
        @page { margin: 15px 25px; }
        .pdf-footer {
            border: 1px solid #d32f2f; color: red; font-weight: bold; font-size: 10px;
            padding: 10px 15px; border-radius: 10px; text-align: center;
            white-space: pre-line; line-height: 1.3; margin-top: 10px; margin-bottom: 10px;
        }
        .powered-by { text-align: center; font-size: 10px; color: red; margin-top: 5px; }
        body {
            font-family: 'amiri', 'Helvetica', 'Arial', sans-serif;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}; color: #000; font-size: 12px; line-height: 1.4; }
        .invoice-box { width: 100%; margin: auto; }
        .header-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .header-table td { vertical-align: top; border: none; }
        .info-table { width: 100%; border-collapse: collapse; font-size: 12px; }
        .info-table td { padding: 3px 0; vertical-align: top; border: none; }
        .info-label { display: inline-block; width: 120px; font-weight: bold; }
        .logo { max-height: 90px; margin-bottom: 10px; }
        .items-table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px; }
        .items-table th, .items-table td { padding: 8px 10px; border: 1px solid #000000; }
        .items-table th { background-color: #00aeef; color: #fff; font-weight: bold; text-align: left; }
        .items-table th.bg-gray { background-color: #e6e6e6; color: #333; }
        .items-table tbody tr td { background-color: #ffffff; vertical-align: middle; }
        .items-table tbody td.col-blue { background-color: #00aeef !important; color: #fff; }
        .items-table tbody td.col-gray { background-color: #e6e6e6 !important; }
        .items-table td.center { text-align: center; }
        .items-table td.right { text-align: right; }
        .totals-table { width: 320px; border-collapse: collapse; font-size: 13px; margin-top: 20px; margin-left: auto; margin-right: 0; border: 1px solid #c0c0c0; }
        .totals-table td { padding: 8px 10px; border: none; border-bottom: 1px solid #e0e0e0; }
        .totals-table tr:last-child td { border-bottom: none; background-color: #f8f9fa; }
        @if(app()->getLocale() === 'ar')
        .totals-label { text-align: right !important; font-weight: bold; background-color: #fdfdfd; border-left: 1px solid #e0e0e0 !important; border-right: none !important; width: 150px; padding-right: 12px !important; }
        .totals-value { text-align: left !important; font-size: 14px; padding-left: 12px !important; }
        .items-table th { text-align: right; }
@else
        .totals-label { text-align: left; font-weight: bold; background-color: #fdfdfd; border-right: 1px solid #e0e0e0; width: 140px; }
        .totals-value { text-align: right; font-size: 14px; }
@endif
        .text-colored { color: #00aeef; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <!-- Top Section: Logo + Company Details + Statement Header -->
        <table class="header-table" style="margin-bottom: 5px;">
            <tr>
                <td style="width: 25%; vertical-align: top;">
                    <img src="{{ shop_logo_path(null, $setting) }}" class="logo" style="max-height: 80px; max-width: 160px; object-fit: contain; margin-bottom: 0;" />
                </td>
                <td style="width: 45%; vertical-align: top; padding-left: 15px; padding-right: 15px;">
                    <div style="color: #00aeef; font-weight: bold; font-size: 13px; text-transform: uppercase; margin-bottom: 5px; letter-spacing: 0.5px;">
                        {{ __('COMPANY DETAILS') }}
                    </div>
                    <table class="info-table" style="font-size: 11px;">
                        <tr>
                            <td style="width: 32%; font-weight: bold;"><span class="info-label">{{ __('Company') }}</span></td>
                            <td style="width: 5%">:</td>
                            <td style="width: 63%"><strong>{{ tdb($setting->company_name ?? 'My Company') }}</strong></td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;"><span class="info-label">{{ __('Address') }}</span></td>
                            <td>:</td>
                            <td style="font-size: 10px;">{{ tdb($setting->company_address ?? 'Address not set') }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;"><span class="info-label">{{ __('City / Country') }}</span></td>
                            <td>:</td>
                            <td>{{ tdb($setting->company_city ?? '') }} - {{ tdb($setting->company_country ?? '') }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;"><span class="info-label">{{ __('Telephone') }}</span></td>
                            <td>:</td>
                            <td>{{ $setting->company_phone ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;"><span class="info-label">{{ __('VAT Number') }}</span></td>
                            <td>:</td>
                            <td>{{ $setting->company_vat_no ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;"><span class="info-label">{{ __('CR Number') }}</span></td>
                            <td>:</td>
                            <td>{{ $setting->company_cr_no ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 30%; vertical-align: top;">
                    <h1 style="margin: 0 0 15px 0; font-size: 18px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; color: #000; text-align: right;">
                        {{ __('STATEMENT OF ACCOUNT') }}
                    </h1>
                    <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                        <tr>
                            <td style="text-align: left; font-weight: bold; width: 28%;">{{ __('Date') }} &nbsp;:</td>
                            <td style="text-align: left; border-bottom: 1px solid #000; height: 16px;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="height: 12px;" colspan="2"></td>
                        </tr>
                        <tr>
                            <td style="text-align: left; font-weight: bold; width: 28%;">{{ __('Time') }} &nbsp;:</td>
                            <td style="text-align: left; border-bottom: 1px solid #000; height: 16px;">&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Colored Gradient Divider Line -->
        <div style="height: 3px; background: linear-gradient(to right, #00aeef, #d90d91); margin: 8px 0 15px 0;"></div>

        <!-- Row 2: Statement Details + Customer Details -->
        <table class="header-table" style="margin-bottom: 15px;">
            <tr>
                <!-- Left: Statement Details -->
                <td style="width: 50%; vertical-align: top; padding-right: 15px;">
                    <div style="color: #00aeef; font-weight: bold; font-size: 13px; text-transform: uppercase; margin-bottom: 5px; letter-spacing: 0.5px;">
                        {{ __('STATEMENT DETAILS') }}
                    </div>
                    <table class="info-table" style="font-size: 11px;">
                        <tr>
                            <td style="width: 35%; font-weight: bold;"><span class="info-label">{{ __('Statement Date') }}</span></td>
                            <td style="width: 5%">:</td>
                            <td style="width: 60%; color: #00aeef; font-weight: bold;">{{ \Carbon\Carbon::now()->setTimezone($userTz ?? 'Asia/Karachi')->format('d-M-Y H:i A') }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;"><span class="info-label">{{ __('Generated By') }}</span></td>
                            <td>:</td>
                            <td><span style="font-weight: bold; color: #D90D91 !important;">{{ auth()->user()?->name ? tdb(auth()->user()->name) : __('Admin') }}</span></td>
                        </tr>
                    </table>
                </td>

                <!-- Right: Customer Details -->
                <td style="width: 50%; vertical-align: top; padding-left: 15px;">
                    <div style="color: #00aeef; font-weight: bold; font-size: 13px; text-transform: uppercase; margin-bottom: 5px; letter-spacing: 0.5px;">
                        {{ __('CUSTOMER DETAILS') }}
                    </div>
                    <table class="info-table" style="font-size: 11px;">
                        <tr>
                            <td style="width: 35%; font-weight: bold;"><span class="info-label">{{ __('Customer Company') }}</span></td>
                            <td style="width: 5%">:</td>
                            <td style="width: 60%"><strong>{{ tdb($customer->defaultCompany()?->company_name ?? ($customer->company_name ?? '')) }}</strong></td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;"><span class="info-label">{{ __('Customer Name') }}</span></td>
                            <td>:</td>
                            <td><strong style="font-size: 13px;">{{ tdb($customer->name ?? '') }}</strong></td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;"><span class="info-label">{{ __('Telephone') }}</span></td>
                            <td>:</td>
                            <td style="color: #e11d48; font-weight: bold;">{{ $customer->formatted_phone ?: ($customer->phone ?? '') }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;"><span class="info-label">{{ __('VAT Number') }}</span></td>
                            <td>:</td>
                            <td>{{ $customer->defaultCompany()?->vat_no ?? ($customer->vat_no ?? '') }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;"><span class="info-label">{{ __('CR Number') }}</span></td>
                            <td>:</td>
                            <td>{{ $customer->defaultCompany()?->cr_no ?? ($customer->cr_no ?? '') }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;"><span class="info-label">{{ __('Address') }}</span></td>
                            <td>:</td>
                            <td>{{ tdb($customer->defaultCompany()?->address ?? ($customer->address ?? '')) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">#</th>
                    <th style="width: 15%; text-align: center;">{{ __('Date') }}</th>
                    <th style="width: 20%;">{{ __('Invoice No') }}</th>
                    <th style="width: 15%; text-align: center;" class="bg-gray">{{ __('Status') }}</th>
                    <th style="width: 15%; text-align: center;" class="bg-gray">{{ __('Total Amount') }}</th>
                    <th style="width: 15%; text-align: center;" class="bg-gray">{{ __('Total Paid') }}</th>
                    <th style="width: 15%; text-align: right;">{{ __('Total Balance') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $index => $invoice)
                    <tr>
                        <td class="col-blue center">{{ $index + 1 }}</td>
                        <td class="center">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d-M-Y') }}</td>
                        <td><strong>{{ $invoice->invoice_number }}</strong></td>
                        <td class="col-gray center">{{ __(ucfirst($invoice->status)) }}</td>
                        <td class="col-gray center">{{ number_format($invoice->grand_total, 2) }}</td>
                        <td class="col-gray center">{{ number_format($invoice->amount_paid, 2) }}</td>
                        <td class="col-blue right">{{ number_format($invoice->balance_due, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <tr>
                <td style="width: 50%; vertical-align: middle; text-align: center; border: none; padding-right: 20px;">
                    @include('partials.pdf_bank_details', ['setting' => $setting])
                </td>
                <td style="width: 50%; vertical-align: top; border: none;">
                    <table class="totals-table" style="margin-top: 0; width: 100%;">
                        <tr>
                            <td class="totals-label">{{ __('Total Invoiced') }}</td>
                            <td class="totals-value">{{ number_format($totalBilled, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="totals-label">{{ __('Total Paid') }}</td>
                            <td class="totals-value" style="color: #28a745;">{{ number_format($totalPaid, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="totals-label">{{ __('Total Balance') }}</td>
                            <td class="totals-value" style="color: #dc3545; font-weight: bold; font-size: 16px;">{{ number_format($totalDue, 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

    </div>

    <htmlpagefooter name="page-footer">
        @if($setting && $setting->pdf_footer)
            <div class="pdf-footer">{{ tdb($setting->pdf_footer) }}</div>
        @endif
        <div class="powered-by">
            <div style="font-size: 8.5px; color: #777; margin-bottom: 2px;">{{ __('Vat not available on this software in KSA just only use for shop inside use for account.') }}</div>
            Powered by <a href="https://www.crelogics.com/"
                style="text-decoration: none; color: #007bff;">crelogics.com</a> | <a
                href="https://rightfreelancer.com/"
                style="text-decoration: none; color: #007bff;">rightfreelancer.com</a>
        </div>
    </htmlpagefooter>
    <sethtmlpagefooter name="page-footer" value="on" show-this-page="1" />
</body>
</html>




