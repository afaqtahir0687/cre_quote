<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $emailSubject ?? 'Subscription Notification' }}</title>
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
                                {{ config('app.name', 'Crelogics') }}
                            </div>
                            <div style="font-size: 13px; color: #94a3b8; margin-top: 4px;">
                                Central Platform &amp; Subscription Administration
                            </div>
                        </td>
                    </tr>

                    <!-- Status Hero Banner -->
                    <tr>
                        <td style="padding: 24px 32px 10px 32px;">
                            @if($type === 'activated')
                                <div style="background-color: #ecfdf5; border-left: 5px solid #10b981; padding: 16px 20px; border-radius: 6px;">
                                    <div style="color: #047857; font-size: 17px; font-weight: 700; margin-bottom: 4px;">
                                        &#10004; Package Successfully Activated!
                                    </div>
                                    <div style="color: #065f46; font-size: 14px;">
                                        Your subscription plan is active. All system features are ready for your shop operations.
                                    </div>
                                </div>
                            @else
                                <div style="background-color: #fef2f2; border-left: 5px solid #ef4444; padding: 16px 20px; border-radius: 6px;">
                                    <div style="color: #b91c1c; font-size: 17px; font-weight: 700; margin-bottom: 4px;">
                                        &#9888; Notice: Subscription Package Expired
                                    </div>
                                    <div style="color: #991b1b; font-size: 14px;">
                                        Your subscription validity has ended. Account operations are paused until renewal.
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
                                @if($type === 'activated')
                                    We are pleased to confirm that the subscription package for <strong>{{ $shop->name }}</strong> has been successfully activated by the Super Administrator.
                                @else
                                    This is an official notification that the subscription package for <strong>{{ $shop->name }}</strong> has expired. To resume creating and managing quotes, invoices, and job orders, please contact Super Admin for renewal.
                                @endif
                            </p>

                            <!-- Key Details Card -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 12px 18px; border-bottom: 1px solid #e2e8f0; width: 38%; font-size: 13px; color: #64748b; font-weight: 600;">
                                        Shop / Business:
                                    </td>
                                    <td style="padding: 12px 18px; border-bottom: 1px solid #e2e8f0; font-size: 14px; color: #0f172a; font-weight: 600;">
                                        {{ $shop->name }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 18px; border-bottom: 1px solid #e2e8f0; font-size: 13px; color: #64748b; font-weight: 600;">
                                        Package Plan:
                                    </td>
                                    <td style="padding: 12px 18px; border-bottom: 1px solid #e2e8f0; font-size: 14px; color: #0f172a; font-weight: 700;">
                                        {{ $package->name ?? 'Standard Package' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 18px; border-bottom: 1px solid #e2e8f0; font-size: 13px; color: #64748b; font-weight: 600;">
                                        Subscription Status:
                                    </td>
                                    <td style="padding: 12px 18px; border-bottom: 1px solid #e2e8f0; font-size: 13px; font-weight: 700;">
                                        @if($type === 'activated')
                                            <span style="color: #059669; background-color: #d1fae5; padding: 3px 8px; border-radius: 4px; font-size: 12px; text-transform: uppercase;">Active</span>
                                        @else
                                            <span style="color: #dc2626; background-color: #fee2e2; padding: 3px 8px; border-radius: 4px; font-size: 12px; text-transform: uppercase;">Expired</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 18px; border-bottom: 1px solid #e2e8f0; font-size: 13px; color: #64748b; font-weight: 600;">
                                        @if($type === 'activated')
                                            Valid Until:
                                        @else
                                            Expired On:
                                        @endif
                                    </td>
                                    <td style="padding: 12px 18px; border-bottom: 1px solid #e2e8f0; font-size: 14px; color: {{ $type === 'activated' ? '#059669' : '#dc2626' }}; font-weight: 700;">
                                        {{ $shop->subscription_expires_at ? $shop->subscription_expires_at->format('d-M-Y') : 'N/A' }}
                                    </td>
                                </tr>
                                @if($type === 'activated' && isset($amountPaid))
                                <tr>
                                    <td style="padding: 12px 18px; border-bottom: 1px solid #e2e8f0; font-size: 13px; color: #64748b; font-weight: 600;">
                                        Amount Received:
                                    </td>
                                    <td style="padding: 12px 18px; border-bottom: 1px solid #e2e8f0; font-size: 14px; color: #047857; font-weight: 700;">
                                        SAR {{ number_format((float) $amountPaid, 2) }} (Cash In Hand)
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="padding: 12px 18px; font-size: 13px; color: #64748b; font-weight: 600;">
                                        Allowed Branches:
                                    </td>
                                    <td style="padding: 12px 18px; font-size: 13px; color: #334155;">
                                        {{ ($package && $package->max_shops > 1) ? 'Up to ' . $package->max_shops . ' Shops' : 'Single Shop (1 Branch)' }}
                                    </td>
                                </tr>
                            </table>

                            @if($type === 'activated')
                                <div style="background-color: #f1f5f9; border-radius: 8px; padding: 14px 18px; margin-bottom: 24px; font-size: 13px; color: #475569;">
                                    <strong>Notice:</strong> Your transaction has been recorded under Super Admin verified records. You can log in and continue your business operations seamlessly.
                                </div>

                                <div style="text-align: center; margin-bottom: 24px;">
                                    <a href="{{ route('login') }}" style="display: inline-block; background-color: #0d6efd; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-size: 14px; font-weight: 600; box-shadow: 0 2px 6px rgba(13, 110, 253, 0.3);">
                                        Go To Login Portal &rarr;
                                    </a>
                                </div>
                            @else
                                <div style="background-color: #fef3c7; border: 1px solid #fde68a; border-radius: 8px; padding: 14px 18px; margin-bottom: 24px; font-size: 13px; color: #92400e;">
                                    <strong>How to Renew:</strong> Package renewals are processed via <strong>Cash By Hand</strong>. Please contact the Super Administrator or submit a renewal notice from the login portal. Your data (quotes, invoices, customers) is completely safe.
                                </div>

                                <div style="text-align: center; margin-bottom: 24px;">
                                    <a href="{{ route('subscription.expired') }}" style="display: inline-block; background-color: #dc3545; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-size: 14px; font-weight: 600; box-shadow: 0 2px 6px rgba(220, 53, 69, 0.3);">
                                        View Renewal Options &rarr;
                                    </a>
                                </div>
                            @endif

                            <!-- Support Helpdesk Footer -->
                            <div style="border-top: 1px solid #e2e8f0; padding-top: 18px; font-size: 12px; color: #94a3b8; text-align: center;">
                                Need assistance or have questions regarding your plan?<br>
                                Contact platform administrator: 
                                <strong style="color: #64748b;">{{ config('mail.from.address', 'support@crelogics.com') }}</strong>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer Note -->
                    <tr>
                        <td align="center" style="padding: 16px 24px; background-color: #f8fafc; border-top: 1px solid #e2e8f0; font-size: 11px; color: #94a3b8;">
                            &copy; {{ date('Y') }} {{ config('app.name', 'Crelogics') }}. All rights reserved. This is an automated system email.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>

