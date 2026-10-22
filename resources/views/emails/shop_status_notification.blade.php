<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $emailSubject ?? 'Shop Status Notification' }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f6f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #334155; line-height: 1.6;">

    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f4f6f9; padding: 30px 15px;">
        <tr>
            <td align="center">
                <!-- Main Container -->
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0;">
                    
                    <!-- Header -->
                    <tr>
                        <td align="center" style="padding: 28px 24px; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #ffffff;">
                            <div style="font-size: 20px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">
                                {{ config('app.name', 'Quote & Management System') }}
                            </div>
                            <div style="font-size: 13px; color: #94a3b8; margin-top: 4px;">
                                Central Platform Administration
                            </div>
                        </td>
                    </tr>

                    <!-- Status Hero Banner -->
                    <tr>
                        <td style="padding: 24px 32px 10px 32px;">
                            @if($status === 'inactive')
                                <div style="background-color: #fffbeb; border-left: 5px solid #f59e0b; padding: 16px 20px; border-radius: 6px;">
                                    <div style="color: #b45309; font-size: 17px; font-weight: 700; margin-bottom: 4px;">
                                        &#9888; Action Required: Branch Temporarily Deactivated
                                    </div>
                                    <div style="color: #92400e; font-size: 14px;">
                                        Your branch account access has been restricted pending resolution of the issue detailed below.
                                    </div>
                                </div>
                            @elseif($status === 'suspended')
                                <div style="background-color: #fef2f2; border-left: 5px solid #ef4444; padding: 16px 20px; border-radius: 6px;">
                                    <div style="color: #b91c1c; font-size: 17px; font-weight: 700; margin-bottom: 4px;">
                                        &#128683; Notice: Branch Account Suspended
                                    </div>
                                    <div style="color: #991b1b; font-size: 14px;">
                                        Your shop has been suspended by the platform Super Administrator.
                                    </div>
                                </div>
                            @else
                                <div style="background-color: #ecfdf5; border-left: 5px solid #10b981; padding: 16px 20px; border-radius: 6px;">
                                    <div style="color: #047857; font-size: 17px; font-weight: 700; margin-bottom: 4px;">
                                        &#10004; Account Re-Activated
                                    </div>
                                    <div style="color: #065f46; font-size: 14px;">
                                        Your branch account has been successfully re-activated. Normal operations may resume immediately.
                                    </div>
                                </div>
                            @endif
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 20px 32px 28px 32px;">
                            <p style="font-size: 15px; margin-top: 0; color: #1e293b;">
                                Hello <strong>{{ $recipientName ?? 'Shop Administrator' }}</strong>,
                            </p>
                            <p style="font-size: 14px; color: #475569; margin-bottom: 20px;">
                                This is an official notice regarding the operating status of <strong>{{ $shop->name }}</strong> on our platform.
                            </p>

                            <!-- Key Details Card -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 14px 18px; border-bottom: 1px solid #e2e8f0; width: 35%; font-size: 13px; color: #64748b; font-weight: 600;">
                                        Shop / Branch:
                                    </td>
                                    <td style="padding: 14px 18px; border-bottom: 1px solid #e2e8f0; font-size: 14px; color: #0f172a; font-weight: 600;">
                                        {{ $shop->name }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 14px 18px; border-bottom: 1px solid #e2e8f0; font-size: 13px; color: #64748b; font-weight: 600;">
                                        Current Status:
                                    </td>
                                    <td style="padding: 14px 18px; border-bottom: 1px solid #e2e8f0; font-size: 13px; font-weight: 700;">
                                        @if($status === 'inactive')
                                            <span style="color: #d97706; background-color: #fef3c7; padding: 3px 8px; border-radius: 4px; font-size: 12px; text-transform: uppercase;">Inactive (Temporary)</span>
                                        @elseif($status === 'suspended')
                                            <span style="color: #dc2626; background-color: #fee2e2; padding: 3px 8px; border-radius: 4px; font-size: 12px; text-transform: uppercase;">Suspended</span>
                                        @else
                                            <span style="color: #059669; background-color: #d1fae5; padding: 3px 8px; border-radius: 4px; font-size: 12px; text-transform: uppercase;">Active</span>
                                        @endif
                                    </td>
                                </tr>
                                @if(!empty($reason))
                                <tr>
                                    <td style="padding: 14px 18px; border-bottom: 1px solid #e2e8f0; font-size: 13px; color: #64748b; font-weight: 600;">
                                        Primary Reason:
                                    </td>
                                    <td style="padding: 14px 18px; border-bottom: 1px solid #e2e8f0; font-size: 14px; color: #0f172a;">
                                        <strong>{{ $reason }}</strong>
                                    </td>
                                </tr>
                                @endif
                                @if($status === 'inactive' && !empty($deadline))
                                <tr>
                                    <td style="padding: 14px 18px; font-size: 13px; color: #b45309; font-weight: 700;">
                                        Resolution Deadline:
                                    </td>
                                    <td style="padding: 14px 18px; font-size: 14px; color: #b45309; font-weight: 700;">
                                        {{ $deadline->format('l, d F Y') }} 
                                        @if(!empty($gracePeriodDays))
                                            <span style="font-size: 12px; font-weight: normal; color: #78350f;">({{ $gracePeriodDays }} days grace period)</span>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                            </table>

                            @if(!empty($customMessage))
                            <!-- Super Admin Notes / Custom Instructions -->
                            <div style="background-color: #ffffff; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 18px 20px; margin-bottom: 24px;">
                                <div style="font-size: 12px; text-transform: uppercase; font-weight: 700; color: #64748b; letter-spacing: 0.5px; margin-bottom: 8px;">
                                    Message from Super Administrator:
                                </div>
                                <div style="font-size: 14px; color: #334155; white-space: pre-line;">
                                    {{ $customMessage }}
                                </div>
                            </div>
                            @endif

                            @if($status === 'inactive')
                            <!-- Warning Box regarding permanent suspension -->
                            <div style="background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 16px; margin-bottom: 24px;">
                                <div style="font-size: 13px; color: #991b1b; line-height: 1.5;">
                                    <strong>Important Policy Notice:</strong> If the required payment or compliance is not completed by 
                                    <strong>{{ !empty($deadline) ? $deadline->format('d M Y') : 'the specified deadline' }}</strong>, 
                                    this shop branch will be <strong>permanently suspended</strong> and operational access will be fully revoked.
                                </div>
                            </div>
                            @endif

                            <p style="font-size: 14px; color: #475569; margin-bottom: 16px;">
                                Once the required action or payment is verified by Super Admin, your account and branch operations will be reactivated immediately.
                            </p>

                            <p style="font-size: 13px; color: #64748b; margin-bottom: 0;">
                                If you have already made the payment or believe this status was applied in error, please reply to this email or contact the system administrator directly.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="padding: 20px; background-color: #f8fafc; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8;">
                            <div>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</div>
                            <div style="margin-top: 4px;">Automated system notification dispatched on behalf of Platform Super Admin.</div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
