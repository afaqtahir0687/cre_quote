@php $userTz = request()->cookie('user_timezone', config('app.timezone')); @endphp
<!DOCTYPE html>
<html dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

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
            font-family: 'amiri', 'Helvetica', 'Arial', sans-serif;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
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
            background-color: #ffffff;
            vertical-align: middle;
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

        @if(app()->getLocale() === 'ar')
        .totals-label {
            text-align: right !important;
            font-weight: bold;
            background-color: #fdfdfd;
            border-left: 1px solid #e0e0e0 !important;
            border-right: none !important;
            width: 150px;
            padding-right: 12px !important;
        }
        .totals-value {
            text-align: left !important;
            font-size: 14px;
            padding-left: 12px !important;
        }
        .amount-in-words {
            text-align: left !important;
            font-weight: bold;
            margin-top: 15px;
            font-size: 14px;
            padding-left: 10px !important;
        }
        .items-table th {
            text-align: right;
        }
    @else
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
        .amount-in-words {
            text-align: right;
            font-weight: bold;
            margin-top: 15px;
            font-size: 14px;
            padding-right: 10px;
        }
    @endif

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
        <!-- Top Section: Logo + Company Details + Quotation Header -->
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
                    <h1 style="margin: 0 0 15px 0; font-size: 20px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; color: #000; text-align: right;">
                        {{ __('QUOTATION') }}
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

        <!-- Row 2: Quote Details + Customer Details -->
        <table class="header-table" style="margin-bottom: 15px;">
            <tr>
                <!-- Left: Quote Details -->
                <td style="width: 50%; vertical-align: top; padding-right: 15px;">
                    <div style="color: #00aeef; font-weight: bold; font-size: 13px; text-transform: uppercase; margin-bottom: 5px; letter-spacing: 0.5px;">
                        {{ __('QUOTE DETAILS') }}
                    </div>
                    <table class="info-table" style="font-size: 11px;">
                        <tr>
                            <td style="width: 35%; font-weight: bold;"><span class="info-label">{{ __('Quote No') }}</span></td>
                            <td style="width: 5%">:</td>
                            <td style="width: 60%; color: #00aeef; font-weight: bold;">{{ $quote->quote_number }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;"><span class="info-label">{{ __('Quote Date') }}</span></td>
                            <td>:</td>
                            <td>{{ $quote->quote_date }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;"><span class="info-label">{{ __('Created At') }}</span></td>
                            <td>:</td>
                            <td>{{ $quote->created_at ? $quote->created_at->setTimezone($userTz)->format('Y-m-d h:i A') : '' }}</td>
                        </tr>
                        @if($quote->updated_at && $quote->created_at && $quote->updated_at->setTimezone($userTz)->format('Y-m-d H:i') !== $quote->created_at->setTimezone($userTz)->format('Y-m-d H:i'))
                        <tr>
                            <td style="font-weight: bold;"><span class="info-label">{{ __('Updated At') }}</span></td>
                            <td>:</td>
                            <td>{{ $quote->updated_at->setTimezone($userTz)->format('Y-m-d h:i A') }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td style="font-weight: bold;"><span class="info-label">{{ __('Due Date') }}</span></td>
                            <td>:</td>
                            <td>{{ $quote->due_date }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;"><span class="info-label">{{ __('Created By') }}</span></td>
                            <td>:</td>
                            <td><span style="font-weight: bold; color: #D90D91 !important;">{{ $quote->employee ? tdb($quote->employee->name) : (Auth::user()?->name ? tdb(Auth::user()->name) : __('Admin')) }}</span></td>
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
                            <td style="width: 60%"><strong>{{ tdb($quote->billing_company?->company_name ?? ($quote->customer?->company_name ?? '')) }}</strong></td>
                        </tr>
                        @if($quote->show_customer_name ?? true)
                        <tr>
                            <td style="font-weight: bold;"><span class="info-label">{{ __('Customer Name') }}</span></td>
                            <td>:</td>
                            <td><strong style="font-size: 13px;">{{ tdb($quote->customer->name ?? '') }}</strong></td>
                        </tr>
                        @endif
                        <tr>
                            <td style="font-weight: bold;"><span class="info-label">{{ __('Telephone') }}</span></td>
                            <td>:</td>
                            <td style="color: #e11d48; font-weight: bold;">{{ $quote->customer?->formatted_phone ?: ($quote->customer?->phone ?? '') }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;"><span class="info-label">{{ __('VAT Number') }}</span></td>
                            <td>:</td>
                            <td>{{ $quote->billing_company?->vat_no ?? ($quote->customer?->vat_no ?? '') }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;"><span class="info-label">{{ __('CR Number') }}</span></td>
                            <td>:</td>
                            <td>{{ $quote->billing_company?->cr_no ?? ($quote->customer?->cr_no ?? '') }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;"><span class="info-label">{{ __('Address') }}</span></td>
                            <td>:</td>
                            <td>{{ tdb($quote->billing_company?->address ?? ($quote->customer?->address ?? '')) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">#</th>
                    <th style="width: 25%;">{{ __('Description') }}</th>
                    <th style="width: 15%; text-align: center;">{{ __('Image') }}</th>
                    <th style="width: 7%;">{{ __('Size/Meter') }}</th>
                    <th style="width: 9%; text-align: center;" class="bg-gray">{{ __('Qty') }}</th>
                    <th style="width: 9%; text-align: center;" class="bg-gray">{{ __('Unit Price') }}</th>
                                        <th style="width: 15%; text-align: center;" class="bg-gray">{{ __('VAT Amount 15%') }}</th>
                    <th style="width: 9%; text-align: right;">{{ __('Total Price') }}</th>
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
                            <span style="white-space: pre-line;">{{ tdb($item->description) }}</span>@if($item->updated_at && $quote->created_at && $item->updated_at->setTimezone($userTz)->format('Y-m-d H:i') !== $quote->created_at->setTimezone($userTz)->format('Y-m-d H:i'))<div style="font-size: 10px; color: #666; margin-top: 5px;">Updated At: {{ $item->updated_at->setTimezone($userTz)->format('Y-m-d h:i A') }}</div>@endif
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
                <td style="width: 50%; vertical-align: middle; text-align: center; border: none; padding: 0 10px;">
                    @include('partials.pdf_bank_details', ['setting' => $setting])
                </td>
                <td style="width: 50%; vertical-align: top; border: none;">
                    <table class="totals-table" style="margin-top: 0; width: 100%;">
                        <tr>
                            <td class="totals-label">{{ __('Total (Excl. Tax)') }}</td>
                            <td class="totals-value">{{ number_format($quote->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="totals-label">{{ __('Total TAX)') }}</td>
                            <td class="totals-value">{{ number_format($quote->total_tax, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="totals-label">{{ __('Discount') }}</td>
                            <td class="totals-value">-{{ number_format($quote->total_discount ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="totals-label">{{ __('Shipping') }}</td>
                            <td class="totals-value">{{ number_format($quote->shipping ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="totals-label">{{ __('Total (Incl. Tax)') }}</td>
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
                $isAr = app()->getLocale() === 'ar'; $f = new \NumberFormatter($isAr ? 'ar' : 'en', \NumberFormatter::SPELLOUT);
                $whole = floor($quote->grand_total);
                $fraction = round(($quote->grand_total - $whole) * 100);
                $words = $f->format($whole);
                if ($fraction > 0) {
                    $words .= ($isAr ? ' فاصلة ' : ' point ') . $f->format($fraction);
                }
                $words .= ($isAr ? ' ريال سعودي' : ' S.R');
            }
        @endphp

        @if($words)
            <div class="amount-in-words">
                {{ strtolower($words) }}
            </div>
        @endif

    </div>

    <div style="height: 140px;"></div>
    <div style="position: absolute; bottom: 0; left: 4px; right: 4px;">
        {{-- Individual Note (directly above Note / .pdf-footer) --}}
        @if(!empty($quote->terms))
            @php $isAr = app()->getLocale() === 'ar'; @endphp
            <div style="margin: 0 4px 8px 4px; padding: 7px 14px; background-color: #f0f8fd; border-radius: 6px; border-{{ $isAr ? 'right' : 'left' }}: 3.5px solid #00aeef; border-top: 1px solid #d4ecf9; border-bottom: 1px solid #d4ecf9; border-{{ $isAr ? 'left' : 'right' }}: 1px solid #d4ecf9; text-align: {{ $isAr ? 'right' : 'left' }};">
                <div style="color: #0098d9; font-weight: bold; font-size: 10.5px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 3px;">
                    {{ __('Individual Note:') }}
                </div>
                <div style="color: #2c3e50; font-size: 10px; line-height: 1.4; white-space: pre-line;">
                    {{ tdb($quote->terms) }}
                </div>
            </div>
        @endif

        @if(!empty($quote->notes))
            @php
                $notes = nl2br(e(tdb($quote->notes)));
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
                    <div class="arabic" style="font-size: 10px; text-align: center; color: red; line-height: 1.4;">{!! $notes !!}</div>
                @endif
            </div>
        @endif

        <div class="powered-by">
            <div style="font-size: 8.5px; color: #777; margin-bottom: 2px;">{{ __('Vat not available on this software in KSA just only use for shop inside use for account.') }}</div>
            Powered by <a href="https://www.crelogics.com/" style="text-decoration: none; color: #007bff;">crelogics.com</a> | <a href="https://rightfreelancer.com/" style="text-decoration: none; color: #007bff;">rightfreelancer.com</a>
        </div>
    </div>
</body>

</html>



