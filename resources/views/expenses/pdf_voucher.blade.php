<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Expense Voucher') }} - EXP-{{ str_pad($expense->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page {
            margin: 6mm 8mm;
        }
        body {
            font-family: 'amiri', 'dejavusans', sans-serif;
            font-size: 8.5pt;
            color: #1e293b;
            line-height: 1.35;
        }
        .voucher-border {
            border: 2px solid #0f172a;
            border-radius: 6px;
            padding: 8px 12px;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }
        .title-box {
            text-align: center;
        }
        .voucher-title {
            font-size: 14pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
        }
        .voucher-title-ar {
            font-size: 11pt;
            font-weight: bold;
            color: #475569;
            margin-top: 1px;
        }
        .meta-strip {
            width: 100%;
            margin-bottom: 8px;
            border-collapse: collapse;
        }
        .meta-strip td {
            padding: 3px 6px;
            font-size: 8pt;
        }
        .amount-card {
            border: 2px solid #059669;
            background-color: #ecfdf5;
            border-radius: 4px;
            padding: 6px 12px;
            text-align: center;
        }
        .amount-num {
            font-size: 14pt;
            font-weight: bold;
            color: #065f46;
            font-family: monospace;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        .info-table td {
            padding: 5px 6px;
            border: 1px solid #cbd5e1;
            font-size: 8.5pt;
        }
        .info-lbl {
            width: 22%;
            background-color: #f1f5f9;
            font-weight: bold;
            color: #334155;
        }
        .info-val {
            width: 78%;
            color: #0f172a;
        }
        .signatures {
            width: 100%;
            margin-top: 16px;
            border-collapse: collapse;
        }
        .signatures td {
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 4px;
        }
        .sig-line {
            border-top: 1px dashed #64748b;
            margin-top: 28px;
            padding-top: 3px;
            font-size: 7.5pt;
            font-weight: bold;
            color: #334155;
        }
    </style>
</head>
<body>

<div class="voucher-border">
    {{-- Header --}}
    <table class="header-table">
        <tr>
            <td style="width: 35%; vertical-align: middle;">
                @php
                    $brandName = shop_brand_name($currentShop, $setting);
                @endphp
                <div style="font-size: 12pt; font-weight: bold; color: #0f172a;">{{ $brandName }}</div>
                <div style="font-size: 7.5pt; color: #64748b;">
                    {{ $expense->shop?->name ?: __('Head Office') }}
                    @if($setting && $setting->phone) &bull; <span dir="ltr">{{ $setting->phone }}</span> @endif
                </div>
            </td>
            <td style="width: 35%; vertical-align: middle;" class="title-box">
                <div class="voucher-title">{{ __('PAYMENT VOUCHER') }}</div>
                <div class="voucher-title-ar">{{ __('سند صرف مالي') }}</div>
            </td>
            <td style="width: 30%; text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}; vertical-align: middle;">
                <div style="font-size: 8.5pt; font-weight: bold; color: #0f172a;">
                    {{ __('Voucher #') }}: <span dir="ltr" style="color: #dc2626;">EXP-{{ str_pad($expense->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div style="font-size: 8pt; color: #64748b; margin-top: 2px;">
                    {{ __('Date') }}: <span dir="ltr"><strong>{{ $expense->expense_date->format('d/m/Y') }}</strong></span>
                </div>
            </td>
        </tr>
    </table>

    {{-- Meta Strip --}}
    <table class="meta-strip">
        <tr>
            <td style="width: 65%; vertical-align: middle;">
                <span style="color: #64748b; font-weight: bold;">{{ __('Office / Branch') }}:</span>
                <strong style="color: #0f172a; font-size: 9.5pt;">{{ $expense->shop?->name ?: __('Main Office') }}</strong>
                <span style="color: #64748b; margin-left: 10px; margin-right: 10px;">&bull;</span>
                <span style="color: #64748b; font-weight: bold;">{{ __('Staff') }}:</span>
                <strong>{{ $expense->user?->name ?: __('Staff') }}</strong>
            </td>
            <td style="width: 35%; text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}; vertical-align: middle;">
                <div class="amount-card">
                    <span style="font-size: 7pt; color: #065f46; font-weight: bold; text-transform: uppercase;">{{ __('Amount Paid / المبلغ') }}:</span>
                    <div class="amount-num" dir="ltr">{{ format_currency($expense->amount) }}</div>
                </div>
            </td>
        </tr>
    </table>

    {{-- Info Table --}}
    <table class="info-table">
        <tr>
            <td class="info-lbl">{{ __('Expense Title / البند') }}:</td>
            <td class="info-val"><strong>{{ $expense->title }}</strong></td>
        </tr>
        <tr>
            <td class="info-lbl">{{ __('Category / التصنيف') }}:</td>
            <td class="info-val">{{ $expense->categoryLabel() }}</td>
        </tr>
        <tr>
            <td class="info-lbl">{{ __('Payment Method / طريقة الدفع') }}:</td>
            <td class="info-val">
                {{ $expense->paymentMethodLabel() }}
                @if($expense->reference_no)
                    &nbsp;&bull;&nbsp; <strong>{{ __('Receipt / Bill #') }}:</strong> <span dir="ltr">{{ $expense->reference_no }}</span>
                @endif
            </td>
        </tr>
        @if($expense->notes)
        <tr>
            <td class="info-lbl">{{ __('Remarks & Details / ملاحظات') }}:</td>
            <td class="info-val" style="color: #334155;">{{ $expense->notes }}</td>
        </tr>
        @endif
    </table>

    {{-- Signatures --}}
    <table class="signatures">
        <tr>
            <td>
                <div class="sig-line">
                    {{ __('Prepared By (Staff)') }}<br>
                    <span style="font-weight: normal; font-size: 7.5pt; color: #64748b;">{{ $expense->user?->name ?: __('Staff') }}</span>
                </div>
            </td>
            <td>
                <div class="sig-line">
                    {{ __('Accountant / Checked By') }}<br>
                    <span style="font-weight: normal; font-size: 7pt; color: #94a3b8;">{{ __('Signature') }}</span>
                </div>
            </td>
            <td>
                <div class="sig-line">
                    {{ __('Authorized Signatory (Manager)') }}<br>
                    <span style="font-weight: normal; font-size: 7pt; color: #94a3b8;">{{ __('Signature & Stamp') }}</span>
                </div>
            </td>
        </tr>
    </table>
</div>

</body>
</html>
