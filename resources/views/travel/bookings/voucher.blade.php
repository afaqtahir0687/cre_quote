<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Boarding Pass & Ticket Voucher') }} - {{ $booking->booking_reference }}</title>
    
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    @if(app()->getLocale() === 'ar')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    @endif
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: {{ app()->getLocale() === 'ar' ? "'Cairo', sans-serif" : "'Inter', sans-serif" }};
            background: #F1F5F9;
            color: #1E293B;
            padding: 24px 0;
            margin: 0;
        }
        .voucher-container {
            max-width: 860px;
            margin: 0 auto;
            background: #FFFFFF;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            overflow: hidden;
            border: 1px solid #E2E8F0;
        }
        .voucher-header {
            background: linear-gradient(135deg, #0A6FB7 0%, #034475 100%);
            color: #FFFFFF;
            padding: 28px 36px;
            position: relative;
        }
        .seat-badge-hero {
            background: #FA7436;
            color: #FFFFFF;
            padding: 14px 28px;
            border-radius: 14px;
            font-size: 2rem;
            font-weight: 800;
            box-shadow: 0 6px 16px rgba(250, 116, 54, 0.35);
            display: inline-block;
            text-align: center;
        }
        .boarding-stub {
            border: 2px dashed #CBD5E1;
            border-radius: 14px;
            padding: 20px;
            background: #F8FAFC;
        }
        .print-actions {
            max-width: 860px;
            margin: 0 auto 16px auto;
        }
        @media print {
            body {
                background: #FFFFFF !important;
                padding: 0 !important;
            }
            .voucher-container {
                box-shadow: none !important;
                border: 1px solid #CBD5E1 !important;
                border-radius: 0 !important;
                max-width: 100% !important;
            }
            .print-actions {
                display: none !important;
            }
            .voucher-header {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .seat-badge-hero {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>

    <!-- Print / Close Actions (Hidden on Print) -->
    <div class="print-actions d-flex justify-content-between align-items-center">
        <a href="{{ route('travel.bookings.show', $booking->id) }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Details') }}
        </a>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary btn-sm px-4">
                <i class="fas fa-print me-1"></i> {{ __('Print Boarding Pass') }}
            </button>
        </div>
    </div>

    <div class="voucher-container">
        <!-- Header Banner -->
        <div class="voucher-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <span class="badge bg-white text-primary fw-bold px-3 py-1 mb-2 text-uppercase" style="letter-spacing: 0.05em;">
                    {{ __('Official Umrah Boarding Pass') }}
                </span>
                <h3 class="fw-bold mb-1 text-white">{{ $booking->shop->name ?? 'Crelogics Travel & Tourism' }}</h3>
                <p class="small mb-0 opacity-75">
                    {{ __('Booking Reference') }}: <span class="font-monospace fw-bold text-white">{{ $booking->booking_reference }}</span>
                    &bull; {{ __('Issued') }}: {{ $booking->created_at->format('d M Y') }}
                </p>
            </div>

            <div class="text-center">
                <span class="d-block extra-small text-uppercase opacity-75 fw-bold mb-1">{{ __('Seat Number') }}</span>
                <div class="seat-badge-hero">
                    #{{ $booking->seat_number }}
                </div>
            </div>
        </div>

        <div class="p-4">
            <!-- Route & Schedule Highlight -->
            @if($booking->trip)
                <div class="boarding-stub mb-4">
                    <div class="row align-items-center text-center text-md-start g-3">
                        <div class="col-md-4">
                            <span class="text-muted extra-small uppercase fw-bold d-block">{{ __('Departure Point') }}</span>
                            <h4 class="fw-bold text-dark mb-0">{{ $booking->trip->departure_city }}</h4>
                            <span class="small text-muted">{{ $booking->trip->departure_date ? $booking->trip->departure_date->format('d M Y, h:i A') : 'Scheduled' }}</span>
                        </div>
                        <div class="col-md-4 text-center my-2 my-md-0">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 mb-1">
                                <i class="fas fa-bus me-1"></i> {{ $booking->trip->bus_name }} (49 Seats)
                            </span>
                            <div class="text-muted small">
                                <i class="fas fa-arrow-right"></i> {{ $booking->trip->duration_days }} {{ __('Days Umrah Trip') }}
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <span class="text-muted extra-small uppercase fw-bold d-block">{{ __('Destination') }}</span>
                            <h4 class="fw-bold text-primary mb-0">{{ $booking->trip->destination_city }}</h4>
                            <span class="small text-muted">{{ $booking->trip->return_date ? $booking->trip->return_date->format('d M Y') : 'Round Trip' }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Passenger & Package Grid -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="p-3 border rounded-3 h-100 bg-light">
                        <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">
                            <i class="fas fa-user-circle text-primary me-2"></i>{{ __('Passenger Information') }}
                        </h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted ps-0" style="width: 40%;">{{ __('Full Name') }}:</td>
                                <td class="fw-bold text-dark">{{ $booking->passenger_name }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">{{ __('Phone') }}:</td>
                                <td class="fw-bold font-monospace">{{ $booking->passenger_phone }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">{{ __('ID / Passport') }}:</td>
                                <td class="fw-bold font-monospace">{{ $booking->passenger_id_number ?: '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">{{ __('Gender') }}:</td>
                                <td>{{ ucfirst(__($booking->passenger_gender)) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-3 border rounded-3 h-100 bg-light">
                        <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">
                            <i class="fas fa-concierge-bell text-primary me-2"></i>{{ __('Accommodation & Package') }}
                        </h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted ps-0" style="width: 40%;">{{ __('Package Type') }}:</td>
                                <td>
                                    @if($booking->package_type === 'with_hotel')
                                        <span class="badge bg-success text-white">{{ __('With Hotel Included') }}</span>
                                    @else
                                        <span class="badge bg-secondary text-white">{{ __('Fare Only (Without Hotel)') }}</span>
                                    @endif
                                </td>
                            </tr>
                            @if($booking->package_type === 'with_hotel')
                                <tr>
                                    <td class="text-muted ps-0">{{ __('Hotel Name') }}:</td>
                                    <td class="fw-bold text-dark">{{ $booking->hotel_name ?: ($booking->trip->hotel_name ?? __('Designated Hotel')) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0">{{ __('Room Type') }}:</td>
                                    <td class="fw-bold text-dark">{{ ucfirst(str_replace('_', ' ', $booking->room_type ?: 'quad_sharing')) }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td class="text-muted ps-0">{{ __('Status') }}:</td>
                                <td>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">{{ ucfirst($booking->booking_status) }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Financial Breakdown Table -->
            <div class="border rounded-3 p-3 mb-4">
                <h6 class="fw-bold text-dark mb-2 border-bottom pb-2">
                    <i class="fas fa-file-invoice-dollar text-primary me-2"></i>{{ __('Fare & Payment Summary') }}
                </h6>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr class="table-light text-muted small">
                                <th>{{ __('Description') }}</th>
                                <th class="text-end">{{ __('Amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ __('49-Seat Coach Seat Fare (Seat #:seat)', ['seat' => $booking->seat_number]) }}</td>
                                <td class="text-end font-monospace">{{ number_format($booking->travel_fare, 2) }}</td>
                            </tr>
                            @if($booking->hotel_expense > 0)
                                <tr>
                                    <td>{{ __('Hotel & Accommodations') }} ({{ $booking->hotel_name ?: 'Hotel' }})</td>
                                    <td class="text-end font-monospace">{{ number_format($booking->hotel_expense, 2) }}</td>
                                </tr>
                            @endif
                            @if($booking->extra_charges > 0)
                                <tr>
                                    <td>{{ __('Extra Charges') }}</td>
                                    <td class="text-end font-monospace">+ {{ number_format($booking->extra_charges, 2) }}</td>
                                </tr>
                            @endif
                            @if($booking->discount > 0)
                                <tr>
                                    <td>{{ __('Discount') }}</td>
                                    <td class="text-end text-danger font-monospace">- {{ number_format($booking->discount, 2) }}</td>
                                </tr>
                            @endif
                            @if($booking->tax_amount > 0)
                                <tr>
                                    <td>{{ __('Tax / VAT') }}</td>
                                    <td class="text-end font-monospace">+ {{ number_format($booking->tax_amount, 2) }}</td>
                                </tr>
                            @endif
                            <tr class="border-top table-light fw-bold">
                                <td class="fs-6">{{ __('Total Voucher Amount') }}</td>
                                <td class="text-end fs-6 text-primary font-monospace">{{ number_format($booking->total_amount, 2) }}</td>
                            </tr>
                            <tr class="fw-bold">
                                <td class="text-success">{{ __('Total Paid') }} ({{ ucfirst(str_replace('_', ' ', $booking->payment_method)) }})</td>
                                <td class="text-end text-success font-monospace">{{ number_format($booking->paid_amount, 2) }}</td>
                            </tr>
                            @if($booking->balance_due > 0)
                                <tr class="table-warning fw-bold">
                                    <td class="text-danger">{{ __('Balance Due to Driver / Counter') }}</td>
                                    <td class="text-end text-danger font-monospace">{{ number_format($booking->balance_due, 2) }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Passenger Instructions / Terms -->
            <div class="p-3 bg-light rounded-3 border">
                <h6 class="fw-bold text-dark extra-small uppercase mb-2">
                    <i class="fas fa-info-circle text-muted me-1"></i> {{ __('Important Passenger Guidelines') }}
                </h6>
                <ul class="extra-small text-muted mb-0 ps-3">
                    <li>{{ __('Please arrive at the bus terminal at least 60 minutes prior to scheduled coach departure.') }}</li>
                    <li>{{ __('Original national ID, Iqama or valid Passport with Umrah visa is strictly mandatory.') }}</li>
                    <li>{{ __('Each passenger is entitled to 1 standard check-in baggage and 1 hand luggage carry-on.') }}</li>
                    <li>{{ __('Keep this boarding pass voucher handy for conductor inspection upon boarding.') }}</li>
                </ul>
            </div>
        </div>

        <!-- Footer Strip -->
        <div class="p-3 bg-light border-top text-center text-muted extra-small">
            <span>{{ __('Thank you for traveling with us. May your Umrah be accepted!') }}</span>
            <div class="mt-1 font-monospace">{{ $booking->shop->name ?? 'Crelogics ERP' }} &bull; {{ $booking->shop->phone ?? '' }}</div>
        </div>
    </div>

</body>
</html>
