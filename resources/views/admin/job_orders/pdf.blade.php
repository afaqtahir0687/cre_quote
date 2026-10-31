@php $userTz = request()->cookie('user_timezone', config('app.timezone')); @endphp
<!DOCTYPE html>
<html dir="ltr">

<head>
    <meta charset="utf-8">
    <title>{{ $jobOrder->order_number }}</title>
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
            color: #fff;
            font-weight: bold;
            text-align: left;
        }

        .items-table tbody td.col-blue {
            background-color: #00aeef !important;
            color: #fff;
        }

        .text-colored {
            color: #00aeef;
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
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <table style="width: 100%; border-collapse: collapse; border: none;">
                        <tr>
                            <td style="width: 130px; vertical-align: middle; border: none; padding: 0;">
                                <img src="{{ shop_logo_path(null, $setting) }}"
                                    class="logo" style="margin-bottom: 0;" />
                            </td>
                            <td style="vertical-align: middle; border: none; padding-left: 15px;">
                                <table style="width: 100%; border-collapse: collapse; border: none;">
                                    <tr><td style="border: none; padding: 0 0 25px 0;"><h2 style="margin: 0; font-size: 16px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;">Delivery Note</h2></td></tr>
                                    <tr><td style="border: none; padding: 0 0 15px 0;"><p style="margin: 0; font-size: 11px; font-weight: bold;">Date &nbsp;&nbsp;&nbsp;: _____________________</p></td></tr>
                                    <tr><td style="border: none; padding: 0;"><p style="margin: 0; font-size: 11px; font-weight: bold;">Time &nbsp;&nbsp;&nbsp;: _____________________</p></td></tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width: 50%; vertical-align: top; padding-left: 30px;">
                    <table class="info-table">
                        <tr>
                            <td style="width: 35%"><span class="info-label">Job Order No</span></td>
                            <td style="width: 5%">:</td>
                            <td style="width: 60%" class="text-colored"><strong>{{ $jobOrder->order_number }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="info-label">Order Date</span></td>
                            <td>:</td>
                            <td>{{ $jobOrder->order_date ? $jobOrder->order_date->format('Y-m-d') : now()->format('Y-m-d') }}
                            </td>
                        </tr>
                        <tr>
                            <td><span class="info-label">Created At</span></td>
                            <td>:</td>
                            <td>{{ $jobOrder->created_at ? $jobOrder->created_at->setTimezone($userTz)->format('Y-m-d h:i A') : '' }}</td>
                        </tr>
                        
                        @if($jobOrder->updated_at && $jobOrder->created_at && $jobOrder->updated_at->setTimezone($userTz)->format('Y-m-d H:i') !== $jobOrder->created_at->setTimezone($userTz)->format('Y-m-d H:i'))
                        <tr>
                            <td><span class="info-label">Updated At</span></td>
                            <td>:</td>
                            <td>{{ $jobOrder->updated_at->setTimezone($userTz)->format('Y-m-d h:i A') }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td><span class="info-label">Status</span></td>
                            <td>:</td>
                            <td>
                                @if($jobOrder->status === 'cancelled')
                                    <span style="color: #dc3545; font-weight: bold; text-transform: uppercase;">Cancelled</span>
                                @else
                                    {{ ucfirst($jobOrder->status) }}
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
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
                            <td style="font-size: 10px; white-space: nowrap;">{{ $setting->company_address ?? 'Address not set' }}</td>
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
                            <td><span class="info-label">Created By</span></td>
                            <td>:</td>
                            <td><span
                                    style="font-weight: bold; color: #D90D91 !important;">{{ $jobOrder->employee->name ?? 'Admin' }}</span>
                            </td>
                        </tr>
                        @if($jobOrder->updated_by)
                        <tr>
                            <td><span class="info-label">Updated By</span></td>
                            <td>:</td>
                            <td><span
                                    style="font-weight: bold; color: #D90D91 !important;">{{ $jobOrder->updater->name ?? 'Admin' }} ({{ ucfirst($jobOrder->updater->role ?? 'Admin') }})</span>
                            </td>
                        </tr>
                        @endif
                    </table>
                </td>
                <td style="width: 50%; vertical-align: top; padding-left: 30px;">
                    <table class="info-table" style="margin-top: 10px;">
                        <tr>
                            <td style="width: 35%"><span class="info-label">Customer Company</span></td>
                            <td style="width: 5%">:</td>
                            <td style="width: 60%"><strong>{{ $jobOrder->billing_company?->company_name ?? ($jobOrder->customer?->company_name ?? '') }}</strong></td>
                        </tr>
                        @if($jobOrder->show_customer_name ?? true)
                        <tr>
                            <td><span class="info-label">Customer Name</span></td>
                            <td>:</td>
                            <td><strong style="font-size: 16px;">{{ $jobOrder->customer->name ?? '' }}</strong></td>
                        </tr>
                        @endif
                        <tr>
                            <td><span class="info-label">Telephone</span></td>
                            <td>:</td>
                            <td style="color: red;">{{ $jobOrder->customer?->formatted_phone ?: ($jobOrder->customer?->phone ?? '') }}</td>
                        </tr>
                        <tr>
                            <td><span class="info-label">VAT Number</span></td>
                            <td>:</td>
                            <td>{{ $jobOrder->billing_company?->vat_no ?? ($jobOrder->customer?->vat_no ?? '') }}</td>
                        </tr>
                        <tr>
                            <td><span class="info-label">CR Number</span></td>
                            <td>:</td>
                            <td>{{ $jobOrder->billing_company?->cr_no ?? ($jobOrder->customer?->cr_no ?? '') }}</td>
                        </tr>
                        <tr>
                            <td><span class="info-label">Address</span></td>
                            <td>:</td>
                            <td>{{ $jobOrder->billing_company?->address ?? ($jobOrder->customer?->address ?? '') }}</td>
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
                @foreach($jobOrder->items->sortByDesc('id')->values() as $index => $item)
                    @php
                        $itemSubtotal = $item->unit_price * $item->quantity;
                        $displayTaxPercentage = $item->tax_percentage ?? 0;
                        $itemTax = $itemSubtotal * ($displayTaxPercentage / 100);
                    @endphp
                    <tr>
                        <td class="col-blue center">{{ $index + 1 }}.</td>
                        <td>
                            <span style="white-space: pre-line;">{{ $item->description }}</span>@if($item->updated_at && $item->created_at && $item->updated_at->setTimezone($userTz)->format('Y-m-d H:i') !== $item->created_at->setTimezone($userTz)->format('Y-m-d H:i'))<div style="font-size: 10px; color: #666; margin-top: 5px;">Updated At: {{ $item->updated_at->setTimezone($userTz)->format('Y-m-d h:i A') }}</div>@endif
                        </td>

                        <td class="center" style="vertical-align: top;">
                            @if(!empty($item->images))
                                <img src="{{ public_path($item->images[0]) }}"
                                    style="max-width:90px; max-height:90px; margin: 4px; border-radius: 4px;">
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
                    @include('partials.pdf_bank_details', ['setting' => $setting])
                </td>
                <td style="width: 50%; vertical-align: top; border: none;">
                    <table class="totals-table" style="margin-top: 0; width: 100%;">
                        <tr>
                            <td class="totals-label">Total (Excl. Tax)</td>
                            <td class="totals-value">{{ number_format($jobOrder->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="totals-label">Total TAX</td>
                            <td class="totals-value">{{ number_format($jobOrder->total_tax, 2) }}</td>
                        </tr>
                            <tr>
                                <td class="totals-label">Discount</td>
                                <td class="totals-value">-{{ number_format($jobOrder->total_discount ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="totals-label">Shipping</td>
                                <td class="totals-value">{{ number_format($jobOrder->shipping ?? 0, 2) }}</td>
                            </tr>
                        <tr>
                            <td class="totals-label">Total (Incl. Tax)</td>
                            <td class="totals-value text-colored" style="font-weight:bold;">
                                {{ number_format($jobOrder->grand_total, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="totals-label" style="color: #28a745;">Paid Amount</td>
                            <td class="totals-value" style="color: #28a745; font-weight:bold;">
                                {{ number_format($jobOrder->paid_amount, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="totals-label" style="color: #dc3545;">Balance Due</td>
                            <td class="totals-value" style="color: #dc3545; font-weight:bold;">
                                {{ number_format($jobOrder->remaining_balance, 2) }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div class="clear"></div>

        @php
            $words = "";
            if ($jobOrder->status === 'cancelled') {
                $words = "cancelled";
            } elseif (class_exists('NumberFormatter')) {
                $f = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
                $whole = floor($jobOrder->grand_total);
                $fraction = round(($jobOrder->grand_total - $whole) * 100);
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



    @php
        $hasExtraImages = false;
        foreach ($jobOrder->items as $item) {
            if (!empty($item->images) && count($item->images) > 1) {
                $hasExtraImages = true;
                break;
            }
        }
    @endphp

    @if($hasExtraImages)
        <div style="margin-top: 10px; font-size: 11px;">
            <div style="font-weight: bold; margin-bottom: 5px;">Extra Images:</div>
            @foreach($jobOrder->items->sortByDesc('id')->values() as $index => $item)
                @if(!empty($item->images) && count($item->images) > 1)
                    <div style="margin-bottom: 5px; page-break-inside: avoid;">
                        <span style="font-weight:bold; display:block; margin-bottom: 3px;">Item Row #{{ $index + 1 }}</span>
                        <div>
                            @foreach(array_slice($item->images, 1) as $image)
                                <img src="{{ public_path($image) }}"
                                    style="max-width:80px; max-height:80px; margin: 2px; border-radius: 4px; border: 1px solid #ccc;">
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif

    <div style="height: 120px;"></div>
    <div style="position: absolute; bottom: 0; left: 4px; right: 4px;">
        @if(!empty($jobOrder->notes))
            @php
                $notes = nl2br(e($jobOrder->notes));
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
                    <div class="arabic" style="font-size: 11px; text-align: center; margin-bottom: 10px; color: red;">{!! $notes !!}</div>
                @endif
            </div>
        @endif

        <div class="powered-by">
            Powered by <a href="https://www.crelogics.com/"
                style="text-decoration: none; color: #007bff;">crelogics.com</a> | <a
                href="https://rightfreelancer.com/"
                style="text-decoration: none; color: #007bff;">rightfreelancer.com</a>
        </div>
    </div>
</body>

</html>











