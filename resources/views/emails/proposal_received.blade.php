<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Proposal Request #{{ $proposal->reference_no }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #F4F6F9;
            margin: 0;
            padding: 24px;
            color: #17243A;
        }
        .container {
            max-width: 620px;
            margin: 0 auto;
            background: #FFFFFF;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 4px 14px rgba(0,0,0,0.06);
            border: 1px solid #E2E8F0;
        }
        .header {
            background: linear-gradient(135deg, #17243A 0%, #1e3fae 50%, #4361EE 100%);
            padding: 30px;
            text-align: center;
            color: #FFFFFF;
        }
        .header h1 {
            margin: 0 0 6px;
            font-size: 22px;
            font-weight: 700;
        }
        .header p {
            margin: 0;
            font-size: 13px;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .badge {
            display: inline-block;
            background: #E8EDFF;
            color: #4361EE;
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 13px;
            font-weight: 700;
            color: #4361EE;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 20px 0 10px;
            border-bottom: 1px solid #E2E8F0;
            padding-bottom: 6px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 9px 12px;
            font-size: 14px;
            border-bottom: 1px solid #F1F5F9;
        }
        .info-table td.label {
            font-weight: 600;
            color: #64748B;
            width: 36%;
        }
        .info-table td.val {
            font-weight: 600;
            color: #0F172A;
        }
        .footer {
            background-color: #F8FAFC;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #94A3B8;
            border-top: 1px solid #E2E8F0;
        }
        .btn {
            display: inline-block;
            background-color: #4361EE;
            color: #ffffff !important;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 New Proposal Request Received</h1>
            <p>A client has requested a custom proposal through the CreLogics website.</p>
        </div>

        <div class="content">
            <div style="text-align: center;">
                <span class="badge">Reference: #{{ $proposal->reference_no }}</span>
            </div>

            <div class="section-title">Client Contact Details (Step 4)</div>
            <table class="info-table">
                <tr>
                    <td class="label">Full Name:</td>
                    <td class="val">{{ $proposal->full_name }}</td>
                </tr>
                <tr>
                    <td class="label">Email Address:</td>
                    <td class="val"><a href="mailto:{{ $proposal->email }}" style="color: #4361EE;">{{ $proposal->email }}</a></td>
                </tr>
                <tr>
                    <td class="label">Phone Number:</td>
                    <td class="val"><a href="tel:{{ $proposal->phone }}" style="color: #4361EE;">{{ $proposal->phone }}</a></td>
                </tr>
                @if($proposal->website_url)
                <tr>
                    <td class="label">Website URL:</td>
                    <td class="val"><a href="{{ $proposal->website_url }}" target="_blank" style="color: #4361EE;">{{ $proposal->website_url }}</a></td>
                </tr>
                @endif
            </table>

            <div class="section-title">Project & Strategy Details (Steps 1, 2, 3)</div>
            <table class="info-table">
                <tr>
                    <td class="label">Primary Service:</td>
                    <td class="val" style="color: #4361EE; font-weight: 700;">{{ $proposal->service }}</td>
                </tr>
                <tr>
                    <td class="label">Customer Location:</td>
                    <td class="val">{{ $proposal->customer_location }}</td>
                </tr>
                <tr>
                    <td class="label">Campaign Goal:</td>
                    <td class="val">{{ $proposal->campaign_goal }}</td>
                </tr>
                <tr>
                    <td class="label">Monthly Budget:</td>
                    <td class="val" style="color: #00B894; font-weight: 700;">{{ $proposal->monthly_budget }}</td>
                </tr>
                <tr>
                    <td class="label">Starting Timeline:</td>
                    <td class="val">{{ $proposal->starting_timeline }}</td>
                </tr>
                <tr>
                    <td class="label">Submitted At:</td>
                    <td class="val">{{ $proposal->created_at->format('M d, Y h:i A') }} (IP: {{ $proposal->ip_address }})</td>
                </tr>
            </table>

            <div style="text-align: center; margin-top: 25px;">
                <a href="{{ route('super_admin.proposals.show', $proposal->id) }}" class="btn">
                    View & Manage Proposal in Admin Panel
                </a>
            </div>
        </div>

        <div class="footer">
            <p style="margin: 0;">This email was automatically generated by Crelogics ERP Marketing Engine.</p>
        </div>
    </div>
</body>
</html>

