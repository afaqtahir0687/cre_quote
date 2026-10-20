@php
    $userTz = request()->cookie('user_timezone', config('app.timezone'));
    $setting = \App\Models\Setting::first();
@endphp
<!DOCTYPE html>
<html dir="ltr">
<head>
    <meta charset="utf-8">
    <title>Job Orders Statement - {{ $dateFilter ? \Carbon\Carbon::parse($dateFilter)->format('d-M-Y') : 'All' }}</title>
    <style>
        @page {
            margin: 15px 25px;
        }

        .pdf-footer {
            border: 1px solid #d32f2f;
            color: red;
            font-weight: bold;
            font-size: 10px;
            padding: 10px 15px;
            border-radius: 10px;
            text-align: center;
            white-space: pre-line;
            line-height: 1.3;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .powered-by {
            text-align: center;
            font-size: 10px;
            color: red;
            margin-top: 5px;
        }

        body {
            font-family: 'amiri', sans-serif;
            color: #000;
            font-size: 12px;
            line-height: 1.4;
        }

        .invoice-box {
            width: 100%;
            margin: auto;
        }

        .header-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: top;
            border: none;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .info-table td {
            padding: 3px 0;
            vertical-align: top;
            border: none;
        }

        .info-label {
            display: inline-block;
            width: 120px;
            font-weight: bold;
        }

        .logo {
            max-height: 90px;
            margin-bottom: 10px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 11px;
        }

        .items-table th,
        .items-table td {
            padding: 7px 8px;
            border: 1px solid #000000;
        }

        .items-table th {
            background-color: #00aeef;
            color: #fff;
            font-weight: bold;
            text-align: left;
        }

        .items-table th.bg-gray {
            background-color: #e6e6e6;
            color: #333;
        }

        .items-table tbody tr td {
            background-color: #f1f1f1;
            vertical-align: middle;
        }

        .items-table tbody td.col-blue {
            background-color: #00aeef !important;
            color: #fff;
        }

        .text-colored {
            color: #00aeef;
        }

        .items-table tbody td.col-gray {
            background-color: #e6e6e6 !important;
        }

        .items-table td.center {
            text-align: center;
        }

        .items-table td.right {
            text-align: right;
        }

        .totals-table {
            width: 340px;
            border-collapse: collapse;
            font-size: 13px;
            margin-top: 20px;
            margin-left: auto;
            margin-right: 0;
            border: 1px solid #c0c0c0;
        }

        .totals-table td {
            padding: 7px 10px;
            border: none;
            border-bottom: 1px solid #e0e0e0;
        }

        .totals-table tr:last-child td {
            border-bottom: none;
            background-color: #f8f9fa;
        }

        .totals-label {
            text-align: left;
            font-weight: bold;
            background-color: #fdfdfd;
            border-right: 1px solid #e0e0e0;
            width: 160px;
        }

        .totals-value {
            text-align: right;
            font-size: 13px;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <!-- Header -->
        <table class="header-table" style="margin-bottom: 25px;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <img src="{{ $setting && $setting->company_logo ? public_path($setting->company_logo) : public_path('uploads/logo/newsign-logo.png') }}"
                        class="logo" />
                </td>
                <td style="width: 50%; vertical-align: top; padding-left: 30px;">
                    <table class="info-table">
                        <tr>
                            <td style="width: 40%"><span class="info-label">Statement Date</span></td>
                            <td style="width: 5%">:</td>
                            <td style="width: 55%" class="text-colored">
                                <strong>{{ $dateFilter ? \Carbon\Carbon::parse($dateFilter)->format('d-M-Y') : 'All Dates' }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="info-label">Generated At</span></td>
                            <td>:</td>
                            <td>{{ \Carbon\Carbon::now()->setTimezone($userTz ?? 'Asia/Karachi')->format('d-M-Y h:i A') }}</td>
                        </tr>
                        <tr>
                            <td><span class="info-label">Generated By</span></td>
                            <td>:</td>
                            <td><span style="font-weight: bold; color: #D90D91 !important;">{{ auth()->user()->name ?? 'Admin' }}</span></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">#</th>
                    <th style="width: 13%; text-align: center;">Date</th>
                    <th style="width: 20%;">Job Order No.</th>
                    <th style="width: 24%;">Customer and Company</th>
                    <th style="width: 12%; text-align: center;" class="bg-gray">Total Amount</th>
                    <th style="width: 12%; text-align: center;" class="bg-gray">Total Paid</th>
                    <th style="width: 14%; text-align: right;">Total Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jobOrders as $index => $jobOrder)
                    <tr>
                        <td class="col-blue center">{{ $index + 1 }}</td>
                        <td class="center">{{ $jobOrder->order_date ? \Carbon\Carbon::parse($jobOrder->order_date)->format('d-M-Y') : '-' }}</td>
                        <td>
                            <strong>{{ $jobOrder->order_number }}</strong>
                        </td>
                        <td>
                            <strong>{{ $jobOrder->customer->name ?? 'N/A' }}</strong>
                            @if(!empty($jobOrder->customer->company_name))
                                <div style="font-size: 9px; color: #555; margin-top: 2px;">{{ $jobOrder->customer->company_name }}</div>
                            @endif
                            @if(!empty($jobOrder->customer->phone))
                                <div style="font-size: 9px; color: #777; margin-top: 1px;">Ph: {{ $jobOrder->customer->phone }}</div>
                            @endif
                        </td>
                        <td class="col-gray center">{{ number_format($jobOrder->grand_total ?? $jobOrder->total_amount, 2) }}</td>
                        <td class="col-gray center">{{ number_format($jobOrder->paid_amount, 2) }}</td>
                        <td class="col-blue right">{{ number_format($jobOrder->remaining_balance, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Bottom Totals & Stamp -->
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <tr>
                <td style="width: 50%; vertical-align: middle; text-align: center; border: none; padding-right: 20px;">
                    <img src="{{ public_path('uploads/logo/stamp_logo.jpeg') }}" style="max-width: 260px; height: auto;" alt="Stamp">
                </td>
                <td style="width: 50%; vertical-align: top; border: none;">
                    <table class="totals-table" style="margin-top: 0; width: 100%;">
                        <tr>
                            <td class="totals-label">Total Job Orders</td>
                            <td class="totals-value"><strong>{{ $totalOrders }}</strong></td>
                        </tr>
                        <tr>
                            <td class="totals-label">Total Billed</td>
                            <td class="totals-value">{{ number_format($totalBilled, 2) }} SAR</td>
                        </tr>
                        @if($totalDiscount > 0)
                        <tr>
                            <td class="totals-label">Total Discount</td>
                            <td class="totals-value" style="color: #d97706;">-{{ number_format($totalDiscount, 2) }} SAR</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="totals-label">Total Paid</td>
                            <td class="totals-value" style="color: #28a745; font-weight: bold;">{{ number_format($totalPaid, 2) }} SAR</td>
                        </tr>
                        <tr>
                            <td class="totals-label">Total Outstanding Due</td>
                            <td class="totals-value" style="color: #dc3545; font-weight: bold; font-size: 15px;">{{ number_format($totalBalance, 2) }} SAR</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <htmlpagefooter name="page-footer">
        @if($setting && $setting->pdf_footer)
            <div class="pdf-footer">{{ $setting->pdf_footer }}</div>
        @endif
        <div class="powered-by">
            Powered by <a href="https://www.crelogics.com/"
                style="text-decoration: none; color: #007bff;">crelogics.com</a> | <a
                href="https://rightfreelancer.com/"
                style="text-decoration: none; color: #007bff;">rightfreelancer.com</a>
        </div>
    </htmlpagefooter>
    <sethtmlpagefooter name="page-footer" value="on" show-this-page="1" />
</body>
</html>
