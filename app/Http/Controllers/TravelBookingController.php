<?php

namespace App\Http\Controllers;

use App\Models\TravelTrip;
use App\Models\TravelBooking;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TravelBookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('shop.module:travel_tourism');
    }

    /**
     * Display a listing of bookings.
     */
    public function index(Request $request)
    {
        $isEmployee = auth()->user()->role === 'employee';
        $query = TravelBooking::forCurrentShop()
            ->with(['trip', 'customer', 'shop', 'employee'])
            ->latest('id');

        if ($request->filled('trip_id')) {
            $query->where('travel_trip_id', $request->trip_id);
        }

        if ($request->filled('employee_id')) {
            if ($request->employee_id === 'admin') {
                $query->whereNull('employee_id');
            } else {
                $query->where('employee_id', $request->employee_id);
            }
        }

        if ($request->filled('branch_id')) {
            $query->where('shop_id', $request->branch_id);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('package_type')) {
            $query->where('package_type', $request->package_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('passenger_name', 'like', "%{$search}%")
                  ->orWhere('booking_reference', 'like', "%{$search}%")
                  ->orWhere('passenger_phone', 'like', "%{$search}%")
                  ->orWhere('passenger_id_number', 'like', "%{$search}%");
            });
        }

        $bookings = $query->paginate(15)->withQueryString();
        $trips = TravelTrip::forCurrentShop()->latest('departure_date')->get();
        $employees = \App\Models\Employee::orderBy('name')->get();
        $currentShop = current_shop();
        $branches = ($currentShop && !$currentShop->parent_shop_id) ? $currentShop->branches : collect();

        $stats = [
            'total_bookings' => TravelBooking::forCurrentShop()->count(),
            'confirmed_bookings' => TravelBooking::forCurrentShop()->where('booking_status', 'confirmed')->count(),
            'paid_amount' => TravelBooking::forCurrentShop()->where('booking_status', 'confirmed')->sum('paid_amount'),
            'balance_due' => TravelBooking::forCurrentShop()->where('booking_status', 'confirmed')->sum('balance_due'),
        ];

        return view('travel.bookings.index', compact('bookings', 'trips', 'stats', 'branches', 'employees', 'isEmployee'));
    }

    /**
     * Show form for booking a seat.
     */
    public function create(Request $request)
    {
        $trips = TravelTrip::forCurrentShop()->whereIn('status', ['upcoming', 'ongoing'])->latest('departure_date')->get();

        $selectedTrip = null;
        if ($request->filled('trip_id')) {
            $selectedTrip = TravelTrip::forCurrentShop()->find($request->trip_id);
        }

        if (!$selectedTrip && $trips->count() > 0) {
            $selectedTrip = $trips->first();
        }

        $selectedSeat = $request->filled('seat') ? (int) $request->seat : null;

        $customers = (current_shop_id() ? Customer::where('shop_id', current_shop_id()) : Customer::query())->orderBy('name')->get();
        $employees = \App\Models\Employee::orderBy('name')->get();
        $currentEmployee = \App\Models\Employee::where('user_id', auth()->id())->first();

        return view('travel.bookings.create', compact('trips', 'selectedTrip', 'selectedSeat', 'customers', 'employees', 'currentEmployee'));
    }

    /**
     * Store a newly created passenger booking.
     */
    public function store(Request $request)
    {
        $shopId = current_shop_id() ?? auth()->user()->shop_id;
        $shopId = current_shop_id();
        if (!$shopId) {
            return redirect()->back()->with('error', __('Active shop context not found.'));
        }

        // Support both multiple seats (seat_numbers) and single seat (seat_number)
        $rawSeats = $request->input('seat_numbers') ?: $request->input('seat_number');
        $seatNumbers = [];
        if (is_array($rawSeats)) {
            $seatNumbers = array_filter(array_map('intval', $rawSeats));
        } elseif (is_string($rawSeats)) {
            $seatNumbers = array_filter(array_map('intval', explode(',', $rawSeats)));
        } elseif (is_numeric($rawSeats)) {
            $seatNumbers = [(int) $rawSeats];
        }

        $seatNumbers = array_values(array_unique($seatNumbers));
        sort($seatNumbers);

        if (empty($seatNumbers)) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('Please select at least one available seat on the bus.'));
        }

        $validated = $request->validate([
            'travel_trip_id' => 'required|exists:travel_trips,id',
            'passenger_name' => 'required|string|max:255',
            'passenger_phone' => 'required|string|max:50',
            'passenger_id_number' => 'nullable|string|max:50',
            'passenger_gender' => 'required|in:male,female',
            'passenger_nationality' => 'nullable|string|max:50',
            'pickup_location' => 'nullable|string|max:150',
            'dropoff_location' => 'nullable|string|max:150',
            'return_dropoff_location' => 'nullable|string|max:150',
            'journey_type' => 'required|in:round_trip,one_way',
            'one_way_direction' => 'nullable|in:outbound,return',
            'employee_id' => 'nullable|exists:employees,id',
            'salesman_name' => 'nullable|string|max:100',
            'salesman_phone' => 'nullable|string|max:50',
            'package_type' => 'required|in:with_hotel,without_hotel',
            'hotel_name' => 'nullable|string|max:255',
            'hotel_address' => 'nullable|string|max:255',
            'room_type' => 'nullable|string|max:50',
            'nights_count' => 'nullable|integer|min:1',
            'travel_fare' => 'required|numeric|min:0',
            'hotel_expense' => 'nullable|numeric|min:0',
            'extra_charges' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string|max:50',
            'customer_id' => 'nullable|exists:customers,id',
            'generate_invoice' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        $trip = TravelTrip::forCurrentShop()->findOrFail($validated['travel_trip_id']);

        // 1. Strict anti-collision check: prevent double booking of ANY selected seat on this trip
        $collisions = TravelBooking::where('travel_trip_id', $trip->id)
            ->whereIn('seat_number', $seatNumbers)
            ->where('booking_status', 'confirmed')
            ->get();

        if ($collisions->isNotEmpty()) {
            $taken = $collisions->map(fn($c) => "#{$c->seat_number} ({$c->passenger_name})")->join(', ');
            return redirect()->back()
                ->withInput()
                ->with('error', __("Selected seats (:seats) are already booked. Please choose other seats.", [
                    'seats' => $taken,
                ]));
        }

        // 2. Calculate Financial Totals
        $seatsCount = count($seatNumbers);
        $totalTravelFare = (float) $validated['travel_fare'];
        $totalHotelExpense = $validated['package_type'] === 'with_hotel' ? (float) ($validated['hotel_expense'] ?? 0) : 0.00;
        $totalExtraCharges = (float) ($validated['extra_charges'] ?? 0);
        $totalDiscount = (float) ($validated['discount'] ?? 0);
        $totalTaxAmount = (float) ($validated['tax_amount'] ?? 0);

        $totalAmount = max(0, ($totalTravelFare + $totalHotelExpense + $totalExtraCharges - $totalDiscount + $totalTaxAmount));
        $paidAmount = min($totalAmount, (float) ($validated['paid_amount'] ?? 0));
        $balanceDue = max(0, $totalAmount - $paidAmount);

        $paymentStatus = 'unpaid';
        if ($balanceDue == 0 && $totalAmount > 0) {
            $paymentStatus = 'paid';
        } elseif ($paidAmount > 0) {
            $paymentStatus = 'partial';
        }

        $passengersInput = $request->input('passengers', []);

        try {
            $firstBooking = DB::transaction(function () use (
                $validated, $trip, $seatNumbers, $seatsCount, $shopId,
                $totalTravelFare, $totalHotelExpense, $totalExtraCharges, $totalDiscount,
                $totalTaxAmount, $totalAmount, $paidAmount, $balanceDue, $paymentStatus, $request, $passengersInput
            ) {
                // Strict concurrency anti-collision check inside transaction with lockForUpdate to prevent race condition across counters
                $collision = TravelBooking::where('travel_trip_id', $trip->id)
                    ->whereIn('seat_number', $seatNumbers)
                    ->where('booking_status', 'confirmed')
                    ->lockForUpdate()
                    ->get();

                if ($collision->isNotEmpty()) {
                    $taken = $collision->map(fn($c) => "#{$c->seat_number} ({$c->passenger_name})")->join(', ');
                    throw new \Exception(__("Selected seat(s) (:seats) were just booked by another counter/office. Please choose available seats.", [
                        'seats' => $taken,
                    ]));
                }

                // Auto-create Customer in CRM if not found
            $customerId = $validated['customer_id'] ?? null;
            if (!$customerId) {
                $existingCust = Customer::where('shop_id', $shopId)
                    ->where(function($q) use ($validated) {
                        $q->where('phone', $validated['passenger_phone']);
                        if (!empty($validated['passenger_id_number'])) {
                            $q->orWhere('tax_id', $validated['passenger_id_number']);
                        }
                    })->first();

                if ($existingCust) {
                    $customerId = $existingCust->id;
                } else {
                    $newCust = Customer::create([
                        'shop_id' => $shopId,
                        'name' => $validated['passenger_name'],
                        'phone' => $validated['passenger_phone'],
                        'tax_id' => $validated['passenger_id_number'] ?? null,
                        'city' => $trip->departure_city,
                        'country' => 'Saudi Arabia',
                        'is_active' => true,
                    ]);
                    $customerId = $newCust->id;
                }
            }

            // Shared unique Booking Reference for this entire group reservation
            $sharedBookingRef = TravelBooking::generateReference($shopId);
            $invoiceId = null;

            // Optional: Generate ONE official consolidated invoice for all selected seats
            if ($request->boolean('generate_invoice') && shop_has_module('invoices')) {
                $shop = $trip->shop;
                $invPrefix = $shop->invoice_prefix ?: 'INV-';
                $invNumber = $invPrefix . date('Ymd') . '-' . substr(md5(uniqid()), 0, 5);

                $invoice = Invoice::create([
                    'shop_id' => $shopId,
                    'customer_id' => $customerId,
                    'invoice_number' => $invNumber,
                    'invoice_date' => now(),
                    'due_date' => $trip->departure_date,
                    'sub_total' => $totalTravelFare + $totalHotelExpense + $totalExtraCharges,
                    'discount' => $totalDiscount,
                    'tax_amount' => $totalTaxAmount,
                    'grand_total' => $totalAmount,
                    'paid_amount' => $paidAmount,
                    'balance_due' => $balanceDue,
                    'status' => $paymentStatus === 'paid' ? 'Paid' : ($paidAmount > 0 ? 'Partially Paid' : 'Unpaid'),
                    'payment_method' => $validated['payment_method'],
                    'notes' => "Umrah Bus Ticket - Seats: #" . implode(', #', $seatNumbers) . " ({$trip->title})",
                ]);

                // Item lines for each seat with specific passenger name if provided
                $perSeatFare = round($totalTravelFare / $seatsCount, 2);
                foreach ($seatNumbers as $idx => $sNum) {
                    $sPass = $passengersInput[$sNum] ?? [];
                    $sName = !empty($sPass['name']) ? trim($sPass['name']) : ($idx === 0 ? $validated['passenger_name'] : $validated['passenger_name'] . ' (' . __('Seat') . ' #' . $sNum . ')');
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'description' => "Seat #{$sNum} - {$sName} - {$trip->bus_name} ({$trip->departure_city} to {$trip->destination_city})",
                        'quantity' => 1,
                        'unit_price' => $perSeatFare,
                        'total' => $perSeatFare,
                    ]);
                }

                // Item: Hotel Accommodation (if applicable)
                if ($totalHotelExpense > 0) {
                    $roomLabel = TravelBooking::roomTypes()[$validated['room_type'] ?? 'quad_sharing']['label'] ?? ucfirst(str_replace('_', ' ', $validated['room_type'] ?? 'quad_sharing'));
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'description' => "Hotel Accommodation: " . ($validated['hotel_name'] ?: ($trip->hotel_name ?: 'Designated Hotel')) . " (" . $roomLabel . ")",
                        'quantity' => 1,
                        'unit_price' => $totalHotelExpense,
                        'total' => $totalHotelExpense,
                    ]);
                }

                if ($totalExtraCharges > 0) {
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'description' => "Extra Charges & Service Fees",
                        'quantity' => 1,
                        'unit_price' => $totalExtraCharges,
                        'total' => $totalExtraCharges,
                    ]);
                }

                $invoiceId = $invoice->id;
            }

            // Create individual confirmed TravelBooking records for each seat
            // Distribute financial totals cleanly across seats so sums equal totals exactly
            $createdBookings = [];
            $accumFare = 0; $accumHotel = 0; $accumExtra = 0; $accumDisc = 0; $accumTax = 0; $accumTotal = 0; $accumPaid = 0; $accumBal = 0;

            foreach ($seatNumbers as $idx => $sNum) {
                $isLast = ($idx === $seatsCount - 1);

                $sFare = $isLast ? ($totalTravelFare - $accumFare) : round($totalTravelFare / $seatsCount, 2);
                $sHotel = $isLast ? ($totalHotelExpense - $accumHotel) : round($totalHotelExpense / $seatsCount, 2);
                $sExtra = $isLast ? ($totalExtraCharges - $accumExtra) : round($totalExtraCharges / $seatsCount, 2);
                $sDisc = $isLast ? ($totalDiscount - $accumDisc) : round($totalDiscount / $seatsCount, 2);
                $sTax = $isLast ? ($totalTaxAmount - $accumTax) : round($totalTaxAmount / $seatsCount, 2);
                $sTotal = $isLast ? ($totalAmount - $accumTotal) : round($totalAmount / $seatsCount, 2);
                $sPaid = $isLast ? ($paidAmount - $accumPaid) : round($paidAmount / $seatsCount, 2);
                $sBal = $isLast ? ($balanceDue - $accumBal) : round($balanceDue / $seatsCount, 2);

                $accumFare += $sFare;
                $accumHotel += $sHotel;
                $accumExtra += $sExtra;
                $accumDisc += $sDisc;
                $accumTax += $sTax;
                $accumTotal += $sTotal;
                $accumPaid += $sPaid;
                $accumBal += $sBal;

                // Seat specific passenger details
                $seatPassenger = $passengersInput[$sNum] ?? [];
                $pDrop = !empty($seatPassenger['dropoff_location']) ? trim($seatPassenger['dropoff_location']) : ($validated['dropoff_location'] ?? null);
                $pRetDrop = !empty($seatPassenger['return_dropoff_location']) ? trim($seatPassenger['return_dropoff_location']) : ($validated['return_dropoff_location'] ?? null);
                if ($idx === 0) {
                    $pName = !empty($seatPassenger['name']) ? trim($seatPassenger['name']) : $validated['passenger_name'];
                    $pGender = !empty($seatPassenger['gender']) ? $seatPassenger['gender'] : $validated['passenger_gender'];
                    $pIdNum = !empty($seatPassenger['id_number']) ? trim($seatPassenger['id_number']) : ($validated['passenger_id_number'] ?? null);
                    $pNat = !empty($seatPassenger['nationality']) ? trim($seatPassenger['nationality']) : ($validated['passenger_nationality'] ?? null);
                    $pLoc = !empty($seatPassenger['pickup_location']) ? trim($seatPassenger['pickup_location']) : ($validated['pickup_location'] ?? null);
                } else {
                    $pName = !empty($seatPassenger['name']) ? trim($seatPassenger['name']) : ($validated['passenger_name'] . ' (' . __('Seat') . ' #' . $sNum . ')');
                    $pGender = !empty($seatPassenger['gender']) ? $seatPassenger['gender'] : $validated['passenger_gender'];
                    $pIdNum = !empty($seatPassenger['id_number']) ? trim($seatPassenger['id_number']) : null;
                    $pNat = !empty($seatPassenger['nationality']) ? trim($seatPassenger['nationality']) : ($validated['passenger_nationality'] ?? null);
                    $pLoc = !empty($seatPassenger['pickup_location']) ? trim($seatPassenger['pickup_location']) : ($validated['pickup_location'] ?? null);
                }

                $createdBookings[] = TravelBooking::create([
                    'shop_id' => $shopId,
                    'travel_trip_id' => $trip->id,
                    'customer_id' => $customerId,
                    'booking_reference' => $sharedBookingRef,
                    'passenger_name' => $pName,
                    'passenger_phone' => $validated['passenger_phone'],
                    'passenger_id_number' => $pIdNum,
                    'passenger_gender' => $pGender,
                    'passenger_nationality' => $pNat,
                    'pickup_location' => $pLoc,
                    'dropoff_location' => $pDrop,
                    'return_dropoff_location' => $pRetDrop,
                    'journey_type' => $validated['journey_type'] ?? 'round_trip',
                    'one_way_direction' => ($validated['journey_type'] ?? 'round_trip') === 'one_way' ? ($validated['one_way_direction'] ?? 'outbound') : null,
                    'employee_id' => $validated['employee_id'] ?? (auth()->user()?->employee?->id ?: null),
                    'salesman_name' => $validated['salesman_name'] ?? (auth()->user()?->name ?: null),
                    'salesman_phone' => $validated['salesman_phone'] ?? (auth()->user()?->employee?->phone ?: (auth()->user()?->phone ?: null)),
                    'seat_number' => $sNum,
                    'seat_class' => $trip->getSeatClass((int)$sNum),
                    'package_type' => $validated['package_type'],
                    'hotel_name' => $validated['package_type'] === 'with_hotel' ? (($validated['hotel_name'] ?? null) ?: $trip->hotel_name) : null,
                    'hotel_address' => $validated['package_type'] === 'with_hotel' ? (($validated['hotel_address'] ?? null) ?: $trip->hotel_address) : null,
                    'room_type' => $validated['package_type'] === 'with_hotel' ? (($validated['room_type'] ?? null) ?: 'quad_sharing') : null,
                    'nights_count' => $validated['package_type'] === 'with_hotel' ? (($validated['nights_count'] ?? null) ?: $trip->duration_days) : null,
                    'travel_fare' => $sFare,
                    'hotel_expense' => $sHotel,
                    'extra_charges' => $sExtra,
                    'discount' => $sDisc,
                    'tax_amount' => $sTax,
                    'total_amount' => $sTotal,
                    'paid_amount' => $sPaid,
                    'balance_due' => $sBal,
                    'payment_status' => $paymentStatus,
                    'payment_method' => $validated['payment_method'],
                    'booking_status' => 'confirmed',
                    'invoice_id' => $invoiceId,
                    'notes' => $validated['notes'] ?? null,
                    'created_by' => auth()->id(),
                ]);
            }

            return $createdBookings[0];
        });
    } catch (\Exception $e) {
        return redirect()->back()
            ->withInput()
            ->with('error', $e->getMessage());
    }

        $seatsList = implode(', #', $seatNumbers);
        return redirect()->route('travel.bookings.voucher', $firstBooking->id)
            ->with('success', __('Seats (:seats) successfully booked under reference :ref!', [
                'seats' => $seatsList,
                'ref' => $firstBooking->booking_reference,
            ]));
    }

    /**
     * Display the specified booking voucher.
     */
    public function show($id)
    {
        $booking = TravelBooking::forCurrentShop()
            ->with(['trip', 'customer', 'invoice'])
            ->findOrFail($id);

        $groupBookings = TravelBooking::where('booking_reference', $booking->booking_reference)
            ->where('travel_trip_id', $booking->travel_trip_id)
            ->where('booking_status', 'confirmed')
            ->orderBy('seat_number')
            ->get();

        return view('travel.bookings.show', compact('booking', 'groupBookings'));
    }

    /**
     * Printable Boarding Pass / Ticket Voucher.
     */
    public function printVoucher($id)
    {
        if (request()->has('lang') && in_array(request('lang'), ['ar', 'en'])) {
            app()->setLocale(request('lang'));
        }

        $booking = TravelBooking::forCurrentShop()
            ->with(['trip', 'customer', 'shop.setting', 'invoice'])
            ->findOrFail($id);

        $groupBookings = TravelBooking::where('booking_reference', $booking->booking_reference)
            ->where('travel_trip_id', $booking->travel_trip_id)
            ->where('booking_status', 'confirmed')
            ->orderBy('seat_number')
            ->get();

        return view('travel.bookings.voucher', compact('booking', 'groupBookings'));
    }

    /**
     * Download A5 PDF Boarding Pass / Ticket Invoice.
     */
    public function downloadPdf($id)
    {
        if (request()->has('lang') && in_array(request('lang'), ['ar', 'en'])) {
            app()->setLocale(request('lang'));
        }

        $booking = TravelBooking::forCurrentShop()
            ->with(['trip', 'customer', 'shop.setting', 'invoice'])
            ->findOrFail($id);

        $groupBookings = TravelBooking::where('booking_reference', $booking->booking_reference)
            ->where('travel_trip_id', $booking->travel_trip_id)
            ->where('booking_status', 'confirmed')
            ->orderBy('seat_number')
            ->get();

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A5',
            'margin_left' => 3,
            'margin_right' => 3,
            'margin_top' => 2.5,
            'margin_bottom' => 2.5,
            'default_font' => 'amiri',
            'autoScriptToLang' => true,
            'autoLangToFont'   => true,
        ]);

        if (app()->getLocale() === 'ar') {
            $mpdf->SetDirectionality('rtl');
        }

        $html = view('travel.bookings.pdf', compact('booking', 'groupBookings'))->render();
        $mpdf->WriteHTML($html);

        $filename = 'Ticket_' . $booking->booking_reference . '.pdf';
        return response($mpdf->Output('', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
    }

    /**
     * Show form for editing passenger booking.
     */
    public function edit($id)
    {
        $booking = TravelBooking::forCurrentShop()->with('trip')->findOrFail($id);
        $trips = TravelTrip::forCurrentShop()->get();

        return view('travel.bookings.edit', compact('booking', 'trips'));
    }

    /**
     * Update passenger booking.
     */
    public function update(Request $request, $id)
    {
        $booking = TravelBooking::forCurrentShop()->findOrFail($id);

        $validated = $request->validate([
            'seat_number' => 'required|integer|min:1|max:100',
            'passenger_name' => 'required|string|max:255',
            'passenger_phone' => 'required|string|max:50',
            'passenger_id_number' => 'nullable|string|max:50',
            'passenger_gender' => 'required|in:male,female',
            'passenger_nationality' => 'nullable|string|max:50',
            'pickup_location' => 'nullable|string|max:150',
            'dropoff_location' => 'nullable|string|max:150',
            'return_dropoff_location' => 'nullable|string|max:150',
            'journey_type' => 'required|in:round_trip,one_way',
            'one_way_direction' => 'nullable|in:outbound,return',
            'employee_id' => 'nullable|exists:employees,id',
            'salesman_name' => 'nullable|string|max:100',
            'salesman_phone' => 'nullable|string|max:50',
            'package_type' => 'required|in:with_hotel,without_hotel',
            'hotel_name' => 'nullable|string|max:255',
            'hotel_address' => 'nullable|string|max:255',
            'room_type' => 'nullable|string|max:50',
            'nights_count' => 'nullable|integer|min:1',
            'travel_fare' => 'required|numeric|min:0',
            'hotel_expense' => 'nullable|numeric|min:0',
            'extra_charges' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string|max:50',
            'booking_status' => 'required|in:confirmed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $newSeat = (int) $validated['seat_number'];

        // If seat changed, ensure target seat is free
        if ($newSeat !== (int) $booking->seat_number && $validated['booking_status'] === 'confirmed') {
            $conflict = TravelBooking::where('travel_trip_id', $booking->travel_trip_id)
                ->where('seat_number', $newSeat)
                ->where('booking_status', 'confirmed')
                ->where('id', '!=', $booking->id)
                ->first();

            if ($conflict) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', __("Seat #:seat is already booked by :name! Please choose another seat.", [
                        'seat' => $newSeat,
                        'name' => $conflict->passenger_name,
                    ]));
            }
        }

        $travelFare = (float) $validated['travel_fare'];
        $hotelExpense = $validated['package_type'] === 'with_hotel' ? (float) ($validated['hotel_expense'] ?? 0) : 0.00;
        $extraCharges = (float) ($validated['extra_charges'] ?? 0);
        $discount = (float) ($validated['discount'] ?? 0);
        $taxAmount = (float) ($validated['tax_amount'] ?? 0);

        $totalAmount = max(0, ($travelFare + $hotelExpense + $extraCharges - $discount + $taxAmount));
        $paidAmount = min($totalAmount, (float) ($validated['paid_amount'] ?? 0));
        $balanceDue = max(0, $totalAmount - $paidAmount);

        $paymentStatus = 'unpaid';
        if ($balanceDue == 0 && $totalAmount > 0) {
            $paymentStatus = 'paid';
        } elseif ($paidAmount > 0) {
            $paymentStatus = 'partial';
        }

        $validated['hotel_expense'] = $hotelExpense;
        $validated['total_amount'] = $totalAmount;
        $validated['paid_amount'] = $paidAmount;
        $validated['balance_due'] = $balanceDue;
        $validated['payment_status'] = $paymentStatus;

        if ($validated['package_type'] === 'with_hotel') {
            $validated['nights_count'] = $validated['nights_count'] ?? $booking->nights_count ?? $booking->trip?->duration_days;
            $validated['hotel_address'] = $validated['hotel_address'] ?? $booking->hotel_address ?? $booking->trip?->hotel_address;
        } else {
            $validated['nights_count'] = null;
            $validated['hotel_name'] = null;
            $validated['hotel_address'] = null;
            $validated['room_type'] = null;
        }

        $booking->update($validated);

        return redirect()->route('travel.bookings.show', $booking->id)
            ->with('success', __('Booking updated successfully!'));
    }

    /**
     * Cancel booking and release seat.
     */
    public function cancel($id)
    {
        $booking = TravelBooking::forCurrentShop()->findOrFail($id);
        $booking->update(['booking_status' => 'cancelled']);

        return redirect()->back()
            ->with('success', __('Booking :ref cancelled. Seat #:seat is now available for other passengers.', [
                'ref' => $booking->booking_reference,
                'seat' => $booking->seat_number,
            ]));
    }

    /**
     * Delete booking.
     */
    public function destroy($id)
    {
        $booking = TravelBooking::forCurrentShop()->findOrFail($id);
        $booking->delete();

        return redirect()->route('travel.bookings.index')
            ->with('success', __('Booking record deleted successfully.'));
    }
}
