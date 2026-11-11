<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Website Inquiry</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #F4F6F9;
            margin: 0;
            padding: 24px;
            color: #17243A;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #FFFFFF;
            border-radius: 12px;
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
            opacity: 0.85;
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
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        .info-table td {
            padding: 10px 12px;
            font-size: 14px;
            border-bottom: 1px solid #EDF2F7;
        }
        .info-table td.label {
            width: 130px;
            font-weight: 600;
            color: #64748B;
        }
        .info-table td.value {
            color: #17243A;
            font-weight: 500;
        }
        .message-box {
            background: #F8FAFC;
            border-left: 4px solid #4361EE;
            padding: 16px;
            border-radius: 0 8px 8px 0;
            margin-bottom: 25px;
            font-size: 14px;
            line-height: 1.6;
            color: #334155;
            white-space: pre-wrap;
        }
        .btn {
            display: inline-block;
            background: #4361EE;
            color: #FFFFFF !important;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            text-align: center;
        }
        .footer {
            background: #F8FAFC;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #94A3B8;
            border-top: 1px solid #EDF2F7;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>CreLogics ERP</h1>
            <p>New Public Website Contact Inquiry</p>
        </div>
        <div class="content">
            <span class="badge">Direct Website Lead</span>
            <h2 style="font-size: 18px; margin: 0 0 16px; color: #17243A;">You have received a new message</h2>

            <table class="info-table">
                <tr>
                    <td class="label">Sender Name:</td>
                    <td class="value"><strong>{{ $contactMessage->name }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Email Address:</td>
                    <td class="value">
                        <a href="mailto:{{ $contactMessage->email }}" style="color: #4361EE; text-decoration: none;">
                            {{ $contactMessage->email }}
                        </a>
                    </td>
                </tr>
                <tr>
                    <td class="label">Phone Number:</td>
                    <td class="value">{{ $contactMessage->phone ?: 'Not provided' }}</td>
                </tr>
                <tr>
                    <td class="label">Subject:</td>
                    <td class="value"><strong>{{ $contactMessage->subject ?: 'General Website Inquiry' }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Received On:</td>
                    <td class="value">{{ now()->format('d M, Y - h:i A') }}</td>
                </tr>
            </table>

            <h3 style="font-size: 14px; margin: 0 0 8px; color: #64748B; text-transform: uppercase; letter-spacing: 0.05em;">Message Body:</h3>
            <div class="message-box">
                {{ $contactMessage->message }}
            </div>

            <div style="text-align: center; margin-top: 25px;">
                <a href="mailto:{{ $contactMessage->email }}?subject={{ urlencode('Re: ' . ($contactMessage->subject ?: 'Inquiry about CreLogics ERP')) }}" class="btn">
                    Reply to {{ $contactMessage->name }}
                </a>
            </div>
        </div>
        <div class="footer">
            This notification was automatically sent by CreLogics ERP Landing Page Contact Form.<br>
            You can also view this message in your Super Admin CMS portal at <a href="{{ url('/super-admin/cms/messages') }}" style="color: #4361EE;">Messages Hub</a>.
        </div>
    </div>
</body>
</html>

