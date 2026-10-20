@php $userTz = request()->cookie('user_timezone', config('app.timezone')); @endphp
<!DOCTYPE html>
<html dir="ltr">

<head>
    <meta charset="utf-8">
    <title>{{ $deliveryNote->delivery_number }}</title>
    <style>
        @page {
            margin: 30px;
        }

        body {
            font-family: 'amiri', sans-serif;
            color: #000;
            font-size: 12px;
            line-height: 1.4;
        }

        .delivery-note-box {
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
            vertical-align: top;
        }

        .items-table tbody td.col-blue {
            background-color: #00aeef !important;
            color: #fff;
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
            width: 300px;
            border-collapse: collapse;
            font-size: 13px;
            margin-top: 20px;
            margin-left: auto;
            margin-right: 0;
        }

        .totals-table td {
            padding: 8px 10px;
            border: none;
        }

        .totals-table tr {
            border-bottom: 1px solid #000;
        }

        .totals-table tr:last-child {
            border-bottom: none;
            border-top: 1px solid #000;
        }

        .totals-label {
            text-align: left;
            font-weight: bold;
        }

        .totals-value {
            text-align: right;
            font-size: 14px;
        }

        .text-colored {
            color: #00aeef;
        }

        .text-success {
            color: #28a745;
        }

        .section-title {
            font-weight: bold;
            font-size: 14px;
            margin-top: 20px;
            margin-bottom: 10px;
            color: #333;
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
    <div class="delivery-note-box">
        <table class="header-table">
            <!-- Row 1: Logo (left) + Delivery Note Info (right) -->
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <img src="{{ $setting && $setting->company_logo ? public_path($setting->company_logo) : public_path('uploads/logo/newsign-logo.png') }}" class="logo" />
                </td>
                <td style="width: 50%; vertical-align: top; padding-left: 30px;">
                    <table class="info-table">
                        <tr>
                            <td style="width: 35%"><span class="info-label">Delivery No</span></td>
                            <td style="width: 5%">:</td>
                            <td style="width: 60%" class="text-colored"><strong>{{ $deliveryNote->delivery_number }}</strong></td>
                        </tr>
                        <tr>
                            <td><span class="info-label">Delivery Date</span></td>
                            <td>:</td>
                            <td>{{ $deliveryNote->delivery_date }}</td>
                        </tr>
                        <tr>
                            <td><span class="info-label">Created At</span></td>
                            <td>:</td>
                            <td>{{ $deliveryNote->created_at ? $deliveryNote->created_at->setTimezone($userTz)->format('Y-m-d h:i A') : '' }}</td>
                        </tr>
                        
                        @if($deliveryNote->updated_at && $deliveryNote->created_at && $deliveryNote->updated_at->setTimezone($userTz)->format('Y-m-d H:i') !== $deliveryNote->created_at->setTimezone($userTz)->format('Y-m-d H:i'))
                        <tr>
                            <td><span class="info-label">Updated At</span></td>
                            <td>:</td>
                            <td>{{ $deliveryNote->updated_at->setTimezone($userTz)->format('Y-m-d h:i A') }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td><span class="info-label">Status</span></td>
                            <td>:</td>
                            <td class="text-colored"><strong>{{ $deliveryNote->status }}</strong></td>
                        </tr>
                        <tr>
                            <td><span class="info-label">Delivery Person</span></td>
                            <td>:</td>
                            <td>{{ $deliveryNote->employee->name ?? 'N/A' }}</td>
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
                    </table>
                </td>
                <td style="width: 50%; vertical-align: top; padding-left: 30px;">
                    <table class="info-table" style="margin-top: 10px;">
                        <tr>
                            <td style="width: 35%"><span class="info-label">Customer</span></td>
                            <td style="width: 5%">:</td>
                            <td style="width: 60%"><strong>{{ $deliveryNote->customer->name ?? '' }}</strong></td>
                        </tr>
                        <tr>
                            <td><span class="info-label">Company</span></td>
                            <td>:</td>
                            <td>{{ $deliveryNote->customer->company_name ?? '' }}</td>
                        </tr>
                        <tr>
                            <td><span class="info-label">Telephone</span></td>
                            <td>:</td>
                            <td>{{ $deliveryNote->customer->phone ?? '' }}</td>
                        </tr>
                        <tr>
                            <td><span class="info-label">VAT Number</span></td>
                            <td>:</td>
                            <td>{{ $deliveryNote->customer->vat_no ?? '' }}</td>
                        </tr>
                        <tr>
                            <td><span class="info-label">CR Number</span></td>
                            <td>:</td>
                            <td>{{ $deliveryNote->customer->cr_no ?? '' }}</td>
                        </tr>
                        <tr>
                            <td><span class="info-label">Address</span></td>
                            <td>:</td>
                            <td>{{ $deliveryNote->customer->address ?? '' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        @if($deliveryNote->delivery_address)
            <div style="margin-top: 20px; padding: 15px; background-color: #f9f9f9; border: 1px solid #ddd;">
                <div style="font-weight: bold; margin-bottom: 5px; color: #333;">Delivery Address:</div>
                <div>{{ $deliveryNote->delivery_address }}</div>
            </div>
        @endif

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">#</th>
                    <th style="width: 25%;">Description</th>
                    <th style="width: 15%; text-align: center;">Image</th>
                    <th style="width: 7%;">Size/Meter</th>
                    <th style="width: 9%; text-align: center;" class="bg-gray">Qty</th>
                    <th style="width: 9%; text-align: center;" class="bg-gray">Unit Price</th>
                    <th style="width: 9%; text-align: center;" class="bg-gray">VAT %</th>
                    <th style="width: 9%; text-align: center;" class="bg-gray">VAT Amount</th>
                    <th style="width: 9%; text-align: right;">Total Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($deliveryNote->invoice->items->sortByDesc('id')->values() as $index => $item)
                    @php
                        $itemSubtotal = $item->unit_price * $item->quantity;
                        if ($item->tax_percentage && $item->tax_percentage > 0) {
                            $itemTax = $itemSubtotal * ($item->tax_percentage / 100);
                            $displayTaxPercentage = $item->tax_percentage;
                        } else {
                            // Calculate proportional tax if tax_percentage is 0 or null
                            if ($deliveryNote->invoice->subtotal > 0 && $deliveryNote->invoice->total_tax > 0) {
                                $itemTax = ($itemSubtotal / $deliveryNote->invoice->subtotal) * $deliveryNote->invoice->total_tax;
                                $displayTaxPercentage = ($deliveryNote->invoice->total_tax / $deliveryNote->invoice->subtotal) * 100;
                            } else {
                                $itemTax = 0;
                                $displayTaxPercentage = 0;
                            }
                        }
                    @endphp
                    <tr>
                        <td class="col-blue center">{{ $index + 1 }}.</td>
                        <td>
                            <span style="white-space: pre-line;">{{ $item->description }}</span>@if($item->updated_at && $deliveryNote->created_at && $item->updated_at->setTimezone($userTz)->format('Y-m-d H:i') !== $deliveryNote->created_at->setTimezone($userTz)->format('Y-m-d H:i'))<div style="font-size: 10px; color: #666; margin-top: 5px;">Updated At: {{ $item->updated_at->setTimezone($userTz)->format('Y-m-d h:i A') }}</div>@endif
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
                        <td class="col-gray center">{{ number_format($displayTaxPercentage, 2) }}%</td>
                        <td class="col-gray center">{{ number_format($itemTax, 2) }}</td>
                        <td class="col-blue right">{{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals-table">
            <tr>
                <td class="totals-label">Total (Excl. Tax)</td>
                <td class="totals-value">{{ number_format($deliveryNote->invoice->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td class="totals-label">Total TAX</td>
                <td class="totals-value">{{ number_format($deliveryNote->invoice->total_tax, 2) }}</td>
            </tr>
            <tr>
                <td class="totals-label">Total (Incl. Tax)</td>
                <td class="totals-value text-colored" style="font-weight:bold;">
                    {{ number_format($deliveryNote->invoice->grand_total, 2) }}
                </td>
            </tr>
        </table>

        @php
            $words = "";
            if (class_exists('NumberFormatter')) {
                $f = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
                $whole = floor($deliveryNote->invoice->grand_total);
                $fraction = round(($deliveryNote->invoice->grand_total - $whole) * 100);
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

        @if($deliveryNote->notes)
            <div style="margin-top: 30px; font-size: 11px;">
                <div style="font-weight: bold; margin-bottom: 5px">Notes:</div>
                <div style="color: red; white-space: pre-line;">{{ $deliveryNote->notes }}</div>
            </div>
        @endif
    </div>
</body>

</html>

