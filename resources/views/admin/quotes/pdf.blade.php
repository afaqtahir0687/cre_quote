@php $userTz = request()->cookie('user_timezone', config('app.timezone')); @endphp
<!DOCTYPE html>
<html dir="ltr">

<head>
    <meta charset="utf-8">
    <title>{{ $quote->quote_number }}</title>
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
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #000;
            font-size: 12px;
            line-height: 1.4;
        }

        .arabic {
            font-family: 'Amiri', 'DejaVu Sans', sans-serif;
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
            font-size: 12px;
        }

        .items-table th,
        .items-table td {
            padding: 8px 10px;
            border: 1px solid #000000;
        }

        .items-table th {
            background-color: #00aeef;
            /* ← change */
            color: #fff;
            font-weight: bold;
            text-align: left;
        }

        .items-table tbody td.col-blue {
            background-color: #00aeef !important;
            /* ← change */
            color: #fff;
        }

        .text-colored {
            color: #00aeef;
            /* ← change */
        }

        .items-table th.bg-gray {
            background-color: #e6e6e6;
            color: #333;
        }

        .items-table tbody tr td {
            background-color: #f1f1f1;
            vertical-align: top;
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
            width: 320px;
            border-collapse: collapse;
            font-size: 13px;
            margin-top: 20px;
            margin-left: auto;
            margin-right: 0;
            border: 1px solid #c0c0c0;
        }

        .right-align {
            text-align: right;
        }

        .totals-table td {
            padding: 8px 10px;
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
            width: 140px;
        }

        .totals-value {
            text-align: right;
            font-size: 14px;
        }

        .qr-code {
            float: left;
            margin-top: 30px;
            margin-left: 20px;
        }

        .clear {
            clear: both;
        }

        .amount-in-words {
            text-align: right;
            font-weight: bold;
            margin-top: 15px;
            font-size: 14px;
            padding-right: 10px;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <table class="header-table">
            <!-- Row 1: Logo (left) + Quote Info (right) -->
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <img src="{{ shop_logo_path(null, $setting) }}"
                        class="logo" />
                </td>
                <td style="width: 50%; vertical-align: top; padding-left: 30px;">
                    <table class="info-table">
                        <tr>
                            <td style="width: 35%"><span class="info-label">Quote No</span></td>
                            <td style="width: 5%">:</td>
                            <td style="width: 60%" class="text-colored"><strong>{{ $quote->quote_number }}</strong></td>
                        </tr>
                        <tr>
                            <td><span class="info-label">Quote Date</span></td>
                            <td>:</td>
                            <td>{{ $quote->quote_date }}</td>
                        </tr>
                        <tr>
                            <td><span class="info-label">Created At</span></td>
                            <td>:</td>
                            <td>{{ $quote->created_at ? $quote->created_at->setTimezone($userTz)->format('Y-m-d h:i A') : '' }}</td>
                        </tr>
                        
                        @if($quote->updated_at && $quote->created_at && $quote->updated_at->setTimezone($userTz)->format('Y-m-d H:i') !== $quote->created_at->setTimezone($userTz)->format('Y-m-d H:i'))
                        <tr>
                            <td><span class="info-label">Updated At</span></td>
                            <td>:</td>
                            <td>{{ $quote->updated_at->setTimezone($userTz)->format('Y-m-d h:i A') }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td><span class="info-label">Due Date</span></td>
                            <td>:</td>
                            <td>{{ $quote->due_date }}</td>
                        </tr>
                        <tr>
                            <td><span class="info-label">Created By</span></td>
                            <td>:</td>
                            <td><span style="font-weight: bold; color: #D90D91 !important;">{{ $quote->employee->name ?? 'Admin' }}</span></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <!-- Row 2: Company Info (left) + Customer Info (right) -->
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <table class="info-table" style="margin-top: 10px;">
                        <tr>
                            <td style="width: 35%"><span class="info-label">Company</span></td>
                            <td style="width: 5%">:</td>
                            <td style="width: 60%"><strong>{{ $setting->company_name ?? 'My Company' }}</strong></td>
                        </tr>
                        <tr>
                            <td><span class="info-label">Address</span></td>
                            <td>:</td>
                            <td><strong>{{ $setting->company_address ?? 'Address not set' }}</strong></td>
                        </tr>
                        <tr>
                            <td><span class="info-label">City / Country</span></td>
                            <td>:</td>
                            <td>{{ $setting->company_city ?? '' }} - {{ $setting->company_country ?? '' }}</td>
                        </tr>
                        <tr>
                            <td><span class="info-label">Telephone</span></td>
                            <td>:</td>
                            <td>{{ $setting->company_phone ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><span class="info-label">VAT Number</span></td>
                            <td>:</td>
                            <td>{{ $setting->company_vat_no ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><span class="info-label">CR Number</span></td>
                            <td>:</td>
                            <td>{{ $setting->company_cr_no ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><span class="info-label">Salesperson</span></td>
                            <td>:</td>
                            <td>{{ Auth::user()->name }}</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 50%; vertical-align: top; padding-left: 30px;">
                    <table class="info-table" style="margin-top: 10px;">
                        <tr>
                            <td style="width: 35%"><span class="info-label">Company</span></td>
                            <td style="width: 5%">:</td>
                            <td style="width: 60%"><strong>{{ $quote->customer->company_name ?? '' }}</strong></td>
                        </tr>
                        <tr>
                            <td><span class="info-label">Customer</span></td>
                            <td>:</td>
                            <td><strong>{{ $quote->customer->name ?? '' }}</strong></td>
                        </tr>
                        <tr>
                            <td><span class="info-label">Telephone</span></td>
                            <td>:</td>
                            <td>{{ $quote->customer->phone ?? '' }}</td>
                        </tr>
                        <tr>
                            <td><span class="info-label">VAT Number</span></td>
                            <td>:</td>
                            <td>{{ $quote->customer->vat_no ?? '' }}</td>
                        </tr>
                        <tr>
                            <td><span class="info-label">CR Number</span></td>
                            <td>:</td>
                            <td>{{ $quote->customer->cr_no ?? '' }}</td>
                        </tr>
                        <tr>
                            <td><span class="info-label">Address</span></td>
                            <td>:</td>
                            <td>{{ $quote->customer->address ?? '' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">#</th>
                    <th style="width: 25%;">Description</th>
                    <th style="width: 15%; text-align: center;">Image</th>
                    <th style="width: 7%;">Size/Meter</th>
                    <th style="width: 9%; text-align: center;" class="bg-gray">Qty</th>
                    <th style="width: 9%; text-align: center;" class="bg-gray">Unit Price</th>
                                        <th style="width: 15%; text-align: center;" class="bg-gray">VAT Amount 15%</th>
                    <th style="width: 9%; text-align: right;">Total Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quote->items->sortByDesc('id')->values() as $index => $item)
                    @php
                        $itemSubtotal = $item->unit_price * $item->quantity;
                        $displayTaxPercentage = $item->tax_percentage ?? 0;
                        $itemTax = $itemSubtotal * ($displayTaxPercentage / 100);
                    @endphp
                    <tr>
                        <td class="col-blue center">{{ $index + 1 }}.</td>
                        <td>
                            <span style="white-space: pre-line;">{{ $item->description }}</span>@if($item->updated_at && $quote->created_at && $item->updated_at->setTimezone($userTz)->format('Y-m-d H:i') !== $quote->created_at->setTimezone($userTz)->format('Y-m-d H:i'))<div style="font-size: 10px; color: #666; margin-top: 5px;">Updated At: {{ $item->updated_at->setTimezone($userTz)->format('Y-m-d h:i A') }}</div>@endif
                        </td>
                        
                        <td class="center" style="vertical-align: top;">
                            @if(!empty($item->images))
                                @foreach($item->images as $image)
                                    <img src="{{ public_path($image) }}"
                                        style="max-width:90px; max-height:90px; margin: 4px; border-radius: 4px;">
                                @endforeach
                            @endif
                        </td>
                        <td>
                            <span style="white-space: pre-line;">{{ $item->item_text ?? '' }}</span>
                        </td>
                        <td class="col-gray center">{{ $item->quantity }}</td>
                        <td class="col-gray center">{{ number_format($item->unit_price, 2) }}</td>
                                                <td class="col-gray center">{{ number_format($itemTax, 2) }}</td>
                        <td class="col-blue right">{{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <tr>
                <td style="width: 50%; vertical-align: middle; text-align: center; border: none; padding-right: 20px;">
                    <img src="{{ public_path('uploads/logo/stamp_logo.jpeg') }}" style="max-width: 300px; height: auto;" alt="Stamp">
                </td>
                <td style="width: 50%; vertical-align: top; border: none;">
                    <table class="totals-table" style="margin-top: 0; width: 100%;">
                        <tr>
                            <td class="totals-label">Total (Excl. Tax)</td>
                            <td class="totals-value">{{ number_format($quote->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="totals-label">Total TAX</td>
                            <td class="totals-value">{{ number_format($quote->total_tax, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="totals-label">Discount</td>
                            <td class="totals-value">-{{ number_format($quote->total_discount ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="totals-label">Shipping</td>
                            <td class="totals-value">{{ number_format($quote->shipping ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="totals-label">Total (Incl. Tax)</td>
                            <td class="totals-value text-colored" style="font-weight:bold;">
                                {{ number_format($quote->grand_total, 2) }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div class="clear"></div>

        @php
            $words = "";
            if (class_exists('NumberFormatter')) {
                $f = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
                $whole = floor($quote->grand_total);
                $fraction = round(($quote->grand_total - $whole) * 100);
                $words = $f->format($whole);
                if ($fraction > 0) {
                    $words .= " point " . $f->format($fraction);
                }
                $words .= " S.R";
            }
        @endphp

        @if($words)
            <div class="amount-in-words">
                {{ strtolower($words) }}
            </div>
        @endif
    </div>


    </div>

    <div style="height: 120px;"></div>
    <div style="position: absolute; bottom: 0; left: 4px; right: 4px;">
        @if(!empty($quote->notes))
            @php
                $notes = nl2br(e($quote->notes));
                $parts = explode('يرجى العلم', $notes, 2);
            @endphp
            <div class="pdf-footer">
                @if(count($parts) == 2)
                    @php
                        $english = preg_replace('/(<br\s*\/?>\s*)+$/i', '', trim($parts[0]));
                    @endphp
                    <div style="font-size: 10px;">{!! $english !!}</div>
                    <div class="arabic" style="font-size: 14px; margin-top: 4px;">يرجى العلم{!! $parts[1] !!}</div>
                @else
                    <div class="arabic">{!! $notes !!}</div>
                @endif
            </div>
        @endif

        <div class="powered-by">
            Powered by <a href="https://www.crelogics.com/" style="text-decoration: none; color: #007bff;">crelogics.com</a> | <a href="https://rightfreelancer.com/" style="text-decoration: none; color: #007bff;">rightfreelancer.com</a>
        </div>
    </div>
</body>

</html>



