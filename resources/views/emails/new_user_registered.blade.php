<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New User & Shop Registration (7-Day Trial)</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #F8FAFC; color: #17243A; margin: 0; padding: 24px; }
        .email-container { max-width: 600px; margin: 0 auto; background: #FFFFFF; border-radius: 12px; overflow: hidden; border: 1px solid #E2E8F0; box-shadow: 0 4px 14px rgba(0,0,0,0.05); }
        .header { background: linear-gradient(135deg, #17243A 0%, #1e3fae 50%, #4361EE 100%); color: #FFFFFF; padding: 26px 30px; }
        .header h2 { margin: 0 0 6px 0; font-size: 20px; font-weight: 700; }
        .header p { margin: 0; font-size: 13px; color: rgba(255,255,255,0.85); }
        .content { padding: 28px 30px; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 50px; font-size: 11px; font-weight: 700; text-transform: uppercase; background: #EEF2FF; color: #4361EE; border: 1px solid rgba(67,97,238,0.2); }
        .field-group { margin-bottom: 16px; border-bottom: 1px solid #F1F5F9; padding-bottom: 12px; }
        .field-label { font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748B; margin-bottom: 4px; letter-spacing: 0.04em; }
        .field-value { font-size: 15px; color: #0F172A; font-weight: 600; }
        .highlight-box { background: #F8FAFC; border-left: 4px solid #00B894; border-radius: 6px; padding: 14px 18px; margin: 20px 0; }
        .footer { background: #F8FAFC; padding: 18px 30px; text-align: center; font-size: 12px; color: #94A3B8; border-top: 1px solid #E2E8F0; }
        .btn-portal { display: inline-block; padding: 10px 22px; background: #4361EE; color: #FFFFFF !important; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 13px; margin-top: 12px; }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <span class="badge" style="background: rgba(255,255,255,0.2); color: #FFF; border: none; margin-bottom: 8px;">New 7-Day Trial Account</span>
            <h2>New Tenant & Shop Registered</h2>
            <p>A new client has signed up for a 7-day free trial on CreLogics ERP platform.</p>
        </div>

        <div class="content">
            <div class="highlight-box">
                <div style="font-size: 12px; color: #64748B;">Registered Shop / Company:</div>
                <div style="font-size: 18px; font-weight: 800; color: #17243A; margin-top: 2px;">{{ $shop->name }}</div>
                <div style="font-size: 12px; color: #00B894; font-weight: 700; margin-top: 4px;">
                    Plan: 7-Day Free Trial ($0.00) &bull; Status: {{ ucfirst($shop->status) }}
                </div>
            </div>

            <div class="field-group" style="background: #EEF2FF; padding: 12px 14px; border-radius: 8px; border: 1px solid rgba(67,97,238,0.2);">
                <div class="field-label" style="color: #4361EE;">Service / Solution Selected</div>
                <div class="field-value" style="color: #17243A; font-size: 16px;">
                    <i class="fas fa-check-circle" style="color: #4361EE;"></i> {{ $serviceName ?? 'Crelogics ERP & Cloud Software' }}
                </div>
            </div>

            <div class="field-group">
                <div class="field-label">Owner / Contact Name</div>
                <div class="field-value">{{ $user->name }}</div>
            </div>

            <div class="field-group">
                <div class="field-label">Email Address</div>
                <div class="field-value">
                    <a href="mailto:{{ $user->email }}" style="color: #4361EE; text-decoration: none;">{{ $user->email }}</a>
                </div>
            </div>

            <div class="field-group">
                <div class="field-label">Phone / WhatsApp</div>
                <div class="field-value">{{ $shop->phone ?: 'Not provided' }}</div>
            </div>

            <div class="field-group">
                <div class="field-label">Username</div>
                <div class="field-value font-monospace">{{ $user->username ?: 'Auto-generated' }}</div>
            </div>

            <div class="field-group">
                <div class="field-label">Trial Expiry Date</div>
                <div class="field-value" style="color: #FA7436;">{{ $shop->subscription_expires_at?->format('F d, Y') ?? '7 Days' }}</div>
            </div>

            <div class="field-group">
                <div class="field-label">Registration Time</div>
                <div class="field-value">{{ now()->format('F d, Y \a\t h:i A') }} (IP: {{ request()->ip() }})</div>
            </div>

            <div style="text-align: center; margin-top: 24px;">
                <a href="{{ url('/super-admin/shops') }}" class="btn-portal">
                    View in Super Admin Portal &rarr;
                </a>
            </div>
        </div>

        <div class="footer">
            Sent automatically by {{ config('app.name', 'CreLogics ERP') }} Super Admin Notification System.
        </div>
    </div>
</body>
</html>
