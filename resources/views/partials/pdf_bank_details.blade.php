@php
    $useCustomImg = !empty($setting?->use_custom_bank_image) && !empty($setting?->bank_stamp_logo) && file_exists(public_path($setting->bank_stamp_logo));
@endphp

@if($useCustomImg)
    <div style="text-align: center;">
        <img src="{{ public_path($setting->bank_stamp_logo) }}" style="max-width: 320px; height: auto;" alt="Bank & Stamp">
    </div>
@else
    <table style="width: 100%; max-width: 330px; border-collapse: separate; border-spacing: 0; font-family: 'amiri', sans-serif; direction: ltr; margin: 0 auto;">
        <!-- Top Row: Bank Badge on Left, Stamp on Right -->
        <tr>
            <td style="width: 55%; vertical-align: middle; text-align: left; padding-bottom: 8px;">
                <div style="background-color: #2b2e35; color: #ffffff; padding: 6px 12px; border-radius: 6px; display: inline-block;">
                    <div style="font-size: 15px; font-weight: bold; letter-spacing: 1px; line-height: 1.1;">{{ $setting->bank_name ?? 'SNB' }}</div>
                    <div style="font-size: 9px; color: #d0d4dc; margin-top: 2px;">{{ $setting->bank_name_ar ?? 'البنك الأهلي السعودي' }}</div>
                </div>
            </td>
            <td style="width: 45%; vertical-align: middle; text-align: right; padding-bottom: 8px;">
                @php
                    $stampPath = !empty($setting?->company_stamp) && file_exists(public_path($setting->company_stamp))
                        ? public_path($setting->company_stamp)
                        : (file_exists(public_path('uploads/logo/stamp.png')) ? public_path('uploads/logo/stamp.png') : null);
                @endphp
                @if($stampPath)
                    <img src="{{ $stampPath }}" style="max-height: 52px; max-width: 100px; object-fit: contain;" alt="Stamp">
                @endif
            </td>
        </tr>

        <!-- Row 1: Name / الإسم -->
        <tr>
            <td colspan="2" style="padding: 2px 0;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 20%; font-size: 11px; font-weight: bold; color: #222; text-align: left; white-space: nowrap;">Name :</td>
                        <td style="width: 60%; text-align: center; vertical-align: middle;">
                            <div style="background-color: #2b2e35; color: #ffffff; font-size: 10px; font-weight: bold; padding: 4px 6px; border-radius: 5px; line-height: 1.2;">
                                {{ $setting->bank_account_name ?? 'شركة وكالة الإعلان الجديد للدعاية والإعلان' }}
                            </div>
                        </td>
                        <td style="width: 20%; font-size: 11px; font-weight: bold; color: #222; text-align: right; white-space: nowrap; direction: rtl;">: الإسم</td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- Row 2: A.C. No. / رقم الحساب -->
        <tr>
            <td colspan="2" style="padding: 2px 0;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 20%; font-size: 11px; font-weight: bold; color: #222; text-align: left; white-space: nowrap;">A.C. No. :</td>
                        <td style="width: 60%; text-align: center; vertical-align: middle;">
                            <div style="background-color: #2b2e35; color: #ffffff; font-size: 11px; font-weight: bold; padding: 4px 6px; border-radius: 5px; letter-spacing: 0.5px;">
                                {{ $setting->bank_account_no ?? '24400001567203' }}
                            </div>
                        </td>
                        <td style="width: 20%; font-size: 11px; font-weight: bold; color: #222; text-align: right; white-space: nowrap; direction: rtl;">: رقم الحساب</td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- Row 3: IBAN. / رقم الآيبان -->
        <tr>
            <td colspan="2" style="padding: 2px 0;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 20%; font-size: 11px; font-weight: bold; color: #222; text-align: left; white-space: nowrap;">IBAN. :</td>
                        <td style="width: 60%; text-align: center; vertical-align: middle;">
                            <div style="background-color: #2b2e35; color: #ffffff; font-size: 9.5px; font-weight: bold; padding: 4px 6px; border-radius: 5px; letter-spacing: 0.3px;">
                                {{ $setting->bank_iban ?? 'SA0210000024400001567203' }}
                            </div>
                        </td>
                        <td style="width: 20%; font-size: 11px; font-weight: bold; color: #222; text-align: right; white-space: nowrap; direction: rtl;">: رقم الآيبان</td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- Row 4: SWIFT. / رمز السويفت -->
        <tr>
            <td colspan="2" style="padding: 2px 0;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 20%; font-size: 11px; font-weight: bold; color: #222; text-align: left; white-space: nowrap;">SWIFT. :</td>
                        <td style="width: 60%; text-align: center; vertical-align: middle;">
                            <div style="background-color: #2b2e35; color: #ffffff; font-size: 11px; font-weight: bold; padding: 4px 6px; border-radius: 5px; letter-spacing: 1px;">
                                {{ $setting->bank_swift ?? 'NCBKSAJE' }}
                            </div>
                        </td>
                        <td style="width: 20%; font-size: 11px; font-weight: bold; color: #222; text-align: right; white-space: nowrap; direction: rtl;">: رمز السويفت</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
@endif

