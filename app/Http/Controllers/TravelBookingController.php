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
        $query = TravelBooking::forCurrentShop()
            ->with(['trip', 'customer'])
            ->latest('id');

        if ($request->filled('trip_id')) {
            $query->where('travel_trip_id', $request->trip_id);
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

        $stats = [
            'total_bookings' => TravelBooking::forCurrentShop()->count(),
            'confirmed_bookings' => TravelBooking::forCurrentShop()->where('booking_status', 'confirmed')->count(),
            'paid_amount' => TravelBooking::forCurrentShop()->where('booking_status', 'confirmed')->sum('paid_amount'),
            'balance_due' => TravelBooking::forCurrentShop()->where('booking_status', 'confirmed')->sum('balance_due'),
        ];

        return view('travel.bookings.index', compact('bookings', 'trips', 'stats'));
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

        return view('travel.bookings.create', compact('trips', 'selectedTrip', 'selectedSeat', 'customers'));
    }

    /**
     * Store a newly created passenger booking.
     */
    public function store(Request $request)
    {
        $shopId = current_shop_id() ?? auth()->user()->shop_id;
        if (!$shopId) {
            return redirect()->back()->with('error', __('Active shop context not found.'));
        }

        $validated = $request->validate([
            'travel_trip_id' => 'required|exists:travel_trips,id',
            'seat_number' => 'required|integer|min:1|max:100',
            'passenger_name' => 'required|string|max:255',
            'passenger_phone' => 'required|string|max:50',
            'passenger_id_number' => 'nullable|string|max:50',
            'passenger_gender' => 'required|in:male,female',
            'package_type' => 'required|in:with_hotel,without_hotel',
            'hotel_name' => 'nullable|string|max:255',
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
        $seatNumber = (int) $validated['seat_number'];

        // 1. Strict anti-collision check: prevent double booking of seat on the same trip
        $collision = TravelBooking::where('travel_trip_id', $trip->id)
            ->where('seat_number', $seatNumber)
            ->where('booking_status', 'confirmed')
            ->first();

        if ($collision) {
            return redirect()->back()
                ->withInput()
                ->with('error', __("Seat #:seat is already booked for this trip by :name (:ref). Please choose another seat.", [
                    'seat' => $seatNumber,
                    'name' => $collision->passenger_name,
                    'ref' => $collision->booking_reference,
                ]));
        }

        // 2. Calculate Financial Breakdown
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

        $booking = DB::transaction(function () use ($validated, $trip, $seatNumber, $shopId, $travelFare, $hotelExpense, $extraCharges, $discount, $taxAmount, $totalAmount, $paidAmount, $balanceDue, $paymentStatus, $request) {
            // Auto-create Customer in CRM if phone not found
            $customerId = $validated['customer_id'] ?? null;
            if (!$customerId) {
                $existingCust = Customer::where('shop_id', $shopId)
                    ->where(function($q) use ($validated) {
                        $q->where('phone', $validated['passenger_phone'])
                          ->orWhere('id_no', $validated['passenger_id_number']);
                    })->first();

                if ($existingCust) {
                    $customerId = $existingCust->id;
                } else {
                    $newCust = Customer::create([
                        'shop_id' => $shopId,
                        'name' => $validated['passenger_name'],
                        'phone' => $validated['passenger_phone'],
                        'id_no' => $validated['passenger_id_number'],
                        'city' => $trip->departure_city,
                    ]);
                    $customerId = $newCust->id;
                }
            }

            // Create Booking
            $booking = TravelBooking::create([
                'shop_id' => $shopId,
                'travel_trip_id' => $trip->id,
                'customer_id' => $customerId,
                'booking_reference' => TravelBooking::generateReference($shopId),
                'passenger_name' => $validated['passenger_name'],
                'passenger_phone' => $validated['passenger_phone'],
                'passenger_id_number' => $validated['passenger_id_number'] ?? null,
                'passenger_gender' => $validated['passenger_gender'],
                'seat_number' => $seatNumber,
                'package_type' => $validated['package_type'],
                'hotel_name' => $validated['package_type'] === 'with_hotel' ? ($validated['hotel_name'] ?: $trip->hotel_name) : null,
                'room_type' => $validated['package_type'] === 'with_hotel' ? ($validated['room_type'] ?: 'quad_sharing') : null,
                'nights_count' => $validated['package_type'] === 'with_hotel' ? ($validated['nights_count'] ?: $trip->duration_days) : null,
                'travel_fare' => $travelFare,
                'hotel_expense' => $hotelExpense,
                'extra_charges' => $extraCharges,
                'discount' => $discount,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'balance_due' => $balanceDue,
                'payment_status' => $paymentStatus,
                'payment_method' => $validated['payment_method'],
                'booking_status' => 'confirmed',
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // Optional: Generate official invoice if requested or if invoices module enabled
            if ($request->boolean('generate_invoice') && shop_has_module('invoices')) {
                $shop = $trip->shop;
                $invPrefix = $shop->invoice_prefix ?: 'INV-';
                $invNumber = $invPrefix . date('Ymd') . '-' . str_pad($booking->id, 3, '0', STR_PAD_LEFT);

                $invoice = Invoice::create([
                    'shop_id' => $shopId,
                    'customer_id' => $customerId,
                    'invoice_number' => $invNumber,
                    'invoice_date' => now(),
                    'due_date' => $trip->departure_date,
                    'sub_total' => $travelFare + $hotelExpense + $extraCharges,
                    'discount' => $discount,
                    'tax_amount' => $taxAmount,
                    'grand_total' => $totalAmount,
                    'paid_amount' => $paidAmount,
                    'balance_due' => $balanceDue,
                    'status' => $paymentStatus === 'paid' ? 'Paid' : ($paidAmount > 0 ? 'Partially Paid' : 'Unpaid'),
                    'payment_method' => $validated['payment_method'],
                    'notes' => "Umrah Bus Ticket - Seat #{$seatNumber} ({$trip->title})",
                ]);

                // Item 1: Seat & Bus Transportation
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => "Seat #{$seatNumber} - {$trip->bus_name} ({$trip->departure_city} to {$trip->destination_city})",
                    'quantity' => 1,
                    'unit_price' => $travelFare,
                    'total_price' => $travelFare,
                ]);

                // Item 2: Hotel & Accommodations (if with_hotel)
                if ($hotelExpense > 0) {
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'description' => "Hotel Accommodation: " . ($booking->hotel_name ?: 'Standard Hotel') . " (" . ucfirst(str_replace('_', ' ', $booking->room_type)) . ")",
                        'quantity' => 1,
                        'unit_price' => $hotelExpense,
                        'total_price' => $hotelExpense,
                    ]);
                }

                $booking->update(['invoice_id' => $invoice->id]);
            }

            return $booking;
        });

        return redirect()->route('travel.bookings.show', $booking->id)
            ->with('success', __('Seat #:seat has been successfully booked for :name!', [
                'seat' => $booking->seat_number,
                'name' => $booking->passenger_name,
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

        return view('travel.bookings.show', compact('booking'));
    }

    /**
     * Printable Boarding Pass / Ticket Voucher.
     */
    public function printVoucher($id)
    {
        $booking = TravelBooking::forCurrentShop()
            ->with(['trip', 'customer', 'shop.setting'])
            ->findOrFail($id);

        return view('travel.bookings.voucher', compact('booking'));
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
            'package_type' => 'required|in:with_hotel,without_hotel',
            'hotel_name' => 'nullable|string|max:255',
            'room_type' => 'nullable|string|max:50',
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
