@php
    $useCustomImg = !empty($setting?->use_custom_bank_image) && !empty($setting?->bank_stamp_logo) && file_exists(public_path($setting->bank_stamp_logo));
    $stampPath = !empty($setting?->company_stamp) && file_exists(public_path($setting->company_stamp))
        ? public_path($setting->company_stamp)
        : (file_exists(public_path('uploads/logo/stamp.png')) ? public_path('uploads/logo/stamp.png') : null);
@endphp

@if($useCustomImg)
    <div style="text-align: center;">
        <img src="{{ public_path($setting->bank_stamp_logo) }}" style="max-width: 320px; height: auto;" alt="Bank & Stamp">
    </div>
@else
    <table style="width: 100%; max-width: 320px; border-collapse: separate; border-spacing: 0; font-family: 'amiri', sans-serif; direction: ltr; margin: 0 auto;">
        <!-- Top Row: Bank Badge on Left, Stamp on Right -->
        <tr>
            <td colspan="2" style="padding-bottom: 12px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="vertical-align: middle; text-align: left;">
                            <table style="border-collapse: collapse; margin: 0;">
                                <tr>
                                    <td style="background-color: #1e1e1e; color: #ffffff; padding: 8px 18px; border-radius: 4px; font-size: 13px; font-weight: bold; text-align: center; white-space: nowrap;">
                                        {{ $setting->bank_name ?? 'SNB' }} &nbsp;&nbsp;&nbsp;&nbsp; {{ $setting->bank_name_ar ?? 'البنك الأهلي السعودي' }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                        @if($stampPath)
                        <td style="vertical-align: middle; text-align: right;">
                            <img src="{{ $stampPath }}" style="max-height: 52px; max-width: 100px; object-fit: contain;" alt="Stamp">
                        </td>
                        @endif
                    </tr>
                </table>
            </td>
        </tr>

        <!-- Row 1: Name / الإسم -->
        <tr>
            <td colspan="2" style="padding: 5px 0;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 22%; font-size: 11px; font-weight: bold; color: #111; text-align: left; vertical-align: middle; white-space: nowrap;">Name :</td>
                        <td style="width: 56%; text-align: center; vertical-align: middle;">
                            <table style="margin: 0 auto; border-collapse: collapse;">
                                <tr>
                                    <td style="background-color: #1e1e1e; color: #ffffff; padding: 7px 16px; border-radius: 4px; font-size: 11px; font-weight: bold; text-align: center; white-space: nowrap;">
                                        {{ $setting->bank_account_name ?? 'شركة وكالة الإعلان الجديد للدعاية والإعلان' }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td style="width: 22%; font-size: 11px; font-weight: bold; color: #111; text-align: right; vertical-align: middle; white-space: nowrap; direction: rtl;">: الإسم</td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- Row 2: A.C. No. / رقم الحساب -->
        <tr>
            <td colspan="2" style="padding: 5px 0;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 22%; font-size: 11px; font-weight: bold; color: #111; text-align: left; vertical-align: middle; white-space: nowrap;">A.C. No. :</td>
                        <td style="width: 56%; text-align: center; vertical-align: middle;">
                            <table style="margin: 0 auto; border-collapse: collapse;">
                                <tr>
                                    <td style="background-color: #1e1e1e; color: #ffffff; padding: 7px 20px; border-radius: 4px; font-size: 12px; font-weight: bold; text-align: center; letter-spacing: 0.8px; white-space: nowrap;">
                                        {{ $setting->bank_account_no ?? '24400001567203' }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td style="width: 22%; font-size: 11px; font-weight: bold; color: #111; text-align: right; vertical-align: middle; white-space: nowrap; direction: rtl;">: رقم الحساب</td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- Row 3: IBAN. / رقم الآيبان -->
        <tr>
            <td colspan="2" style="padding: 5px 0;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 22%; font-size: 11px; font-weight: bold; color: #111; text-align: left; vertical-align: middle; white-space: nowrap;">IBAN. :</td>
                        <td style="width: 56%; text-align: center; vertical-align: middle;">
                            <table style="margin: 0 auto; border-collapse: collapse;">
                                <tr>
                                    <td style="background-color: #1e1e1e; color: #ffffff; padding: 7px 16px; border-radius: 4px; font-size: 10.5px; font-weight: bold; text-align: center; letter-spacing: 0.5px; white-space: nowrap;">
                                        {{ $setting->bank_iban ?? 'SA0210000024400001567203' }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td style="width: 22%; font-size: 11px; font-weight: bold; color: #111; text-align: right; vertical-align: middle; white-space: nowrap; direction: rtl;">: رقم الآيبان</td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- Row 4: SWIFT. / رمز السويفت -->
        <tr>
            <td colspan="2" style="padding: 5px 0;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 22%; font-size: 11px; font-weight: bold; color: #111; text-align: left; vertical-align: middle; white-space: nowrap;">SWIFT. :</td>
                        <td style="width: 56%; text-align: center; vertical-align: middle;">
                            <table style="margin: 0 auto; border-collapse: collapse;">
                                <tr>
                                    <td style="background-color: #1e1e1e; color: #ffffff; padding: 7px 22px; border-radius: 4px; font-size: 12px; font-weight: bold; text-align: center; letter-spacing: 1px; white-space: nowrap;">
                                        {{ $setting->bank_swift ?? 'NCBKSAJE' }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td style="width: 22%; font-size: 11px; font-weight: bold; color: #111; text-align: right; vertical-align: middle; white-space: nowrap; direction: rtl;">: رمز السويفت</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
@endif
