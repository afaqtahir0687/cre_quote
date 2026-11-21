<?php

namespace App\Http\Controllers;

use App\Models\TravelTrip;
use App\Models\TravelBooking;
use Illuminate\Http\Request;

class TravelTripController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('shop.module:travel_tourism');
    }

    /**
     * Display a listing of trips for the current shop.
     */
    public function index(Request $request)
    {
        $query = TravelTrip::forCurrentShop()->withCount('confirmedBookings')->latest('departure_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('trip_code', 'like', "%{$search}%")
                  ->orWhere('bus_name', 'like', "%{$search}%")
                  ->orWhere('bus_number', 'like', "%{$search}%")
                  ->orWhere('departure_city', 'like', "%{$search}%")
                  ->orWhere('destination_city', 'like', "%{$search}%");
            });
        }

        $trips = $query->paginate(12)->withQueryString();

        $stats = [
            'total_trips' => TravelTrip::forCurrentShop()->count(),
            'upcoming_trips' => TravelTrip::forCurrentShop()->where('status', 'upcoming')->count(),
            'total_bookings' => TravelBooking::forCurrentShop()->where('booking_status', 'confirmed')->count(),
            'total_revenue' => TravelBooking::forCurrentShop()->where('booking_status', 'confirmed')->sum('total_amount'),
        ];

        return view('travel.trips.index', compact('trips', 'stats'));
    }

    /**
     * Show the form for creating a new trip.
     */
    public function create()
    {
        $shopId = current_shop_id() ?? auth()->user()->shop_id ?? 1;
        $tripCode = TravelTrip::generateTripCode($shopId);

        return view('travel.trips.create', compact('tripCode'));
    }

    /**
     * Store a newly created trip.
     */
    public function store(Request $request)
    {
        $shopId = current_shop_id() ?? auth()->user()->shop_id;
        if (!$shopId) {
            return redirect()->back()->with('error', __('Please switch to an active shop before creating a trip.'));
        }

        $validated = $request->validate([
            'trip_code' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'bus_name' => 'required|string|max:100',
            'bus_number' => 'nullable|string|max:50',
            'driver_1_name' => 'nullable|string|max:150',
            'driver_1_nationality' => 'nullable|string|max:50',
            'driver_1_phone' => 'nullable|string|max:50',
            'driver_2_name' => 'nullable|string|max:150',
            'driver_2_nationality' => 'nullable|string|max:50',
            'driver_2_phone' => 'nullable|string|max:50',
            'total_seats' => 'required|integer|min:10|max:100',
            'departure_city' => 'required|string|max:100',
            'destination_city' => 'required|string|max:100',
            'departure_date' => 'required|date',
            'return_date' => 'nullable|date|after_or_equal:departure_date',
            'duration_days' => 'required|integer|min:1|max:90',
            'price_without_hotel' => 'required|numeric|min:0',
            'price_with_hotel' => 'required|numeric|min:0',
            'hotel_name' => 'nullable|string|max:255',
            'hotel_address' => 'nullable|string|max:255',
            'hotel_rating' => 'nullable|string|max:50',
            'status' => 'required|in:upcoming,ongoing,completed,cancelled',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['shop_id'] = $shopId;
        $validated['created_by'] = auth()->id();

        $trip = TravelTrip::create($validated);

        return redirect()->route('travel.trips.show', $trip->id)
            ->with('success', __('Trip created successfully! You can now book passenger seats.'));
    }

    /**
     * Display the specified trip with its interactive 49-bus seat layout.
     */
    public function show($id)
    {
        $trip = TravelTrip::forCurrentShop()
            ->with(['confirmedBookings.customer', 'confirmedBookings.shop', 'creator', 'shop'])
            ->findOrFail($id);

        $bookingsBySeat = $trip->confirmedBookings->keyBy('seat_number');
        $bookedSeats = $trip->bookedSeatNumbers();
        $branchBreakdown = $trip->branchBreakdown();

        return view('travel.trips.show', compact('trip', 'bookingsBySeat', 'bookedSeats', 'branchBreakdown'));
    }

    /**
     * Show the form for editing the specified trip.
     */
    public function edit($id)
    {
        $trip = TravelTrip::forCurrentShop()->findOrFail($id);
        return view('travel.trips.edit', compact('trip'));
    }

    /**
     * Update the specified trip.
     */
    public function update(Request $request, $id)
    {
        $trip = TravelTrip::forCurrentShop()->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'bus_name' => 'required|string|max:100',
            'bus_number' => 'nullable|string|max:50',
            'driver_1_name' => 'nullable|string|max:150',
            'driver_1_nationality' => 'nullable|string|max:50',
            'driver_1_phone' => 'nullable|string|max:50',
            'driver_2_name' => 'nullable|string|max:150',
            'driver_2_nationality' => 'nullable|string|max:50',
            'driver_2_phone' => 'nullable|string|max:50',
            'total_seats' => 'required|integer|min:' . max(10, $trip->bookedSeatsCount()),
            'departure_city' => 'required|string|max:100',
            'destination_city' => 'required|string|max:100',
            'departure_date' => 'required|date',
            'return_date' => 'nullable|date|after_or_equal:departure_date',
            'duration_days' => 'required|integer|min:1|max:90',
            'price_without_hotel' => 'required|numeric|min:0',
            'price_with_hotel' => 'required|numeric|min:0',
            'hotel_name' => 'nullable|string|max:255',
            'hotel_address' => 'nullable|string|max:255',
            'hotel_rating' => 'nullable|string|max:50',
            'status' => 'required|in:upcoming,ongoing,completed,cancelled',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $trip->update($validated);

        return redirect()->route('travel.trips.show', $trip->id)
            ->with('success', __('Trip details updated successfully!'));
    }

    /**
     * Remove the trip.
     */
    public function destroy($id)
    {
        $trip = TravelTrip::forCurrentShop()->findOrFail($id);

        if ($trip->bookedSeatsCount() > 0) {
            return redirect()->back()->with('error', __('Cannot delete trip because it has confirmed passenger bookings. Please cancel bookings first.'));
        }

        $trip->delete();

        return redirect()->route('travel.trips.index')
            ->with('success', __('Trip removed successfully.'));
    }

    /**
     * Return JSON data of seats for AJAX interactive visual updates.
     */
    public function seatsJson($id)
    {
        $trip = TravelTrip::forCurrentShop()->findOrFail($id);
        $bookings = $trip->confirmedBookings->keyBy('seat_number');

        $seats = [];
        $totalSeats = (int) $trip->total_seats;

        for ($i = 1; $i <= $totalSeats; $i++) {
            $booking = $bookings->get($i);
            $seats[] = [
                'seat_number' => $i,
                'is_booked' => $booking !== null,
                'passenger_name' => $booking?->passenger_name,
                'passenger_phone' => $booking?->passenger_phone,
                'package_type' => $booking?->package_type,
                'payment_status' => $booking?->payment_status,
                'booking_ref' => $booking?->booking_reference,
                'booking_id' => $booking?->id,
            ];
        }

        return response()->json([
            'trip_id' => $trip->id,
            'title' => $trip->title,
            'bus_name' => $trip->bus_name,
            'total_seats' => $totalSeats,
            'booked_count' => count($bookings),
            'available_count' => $totalSeats - count($bookings),
            'price_without_hotel' => (float)$trip->price_without_hotel,
            'price_with_hotel' => (float)$trip->price_with_hotel,
            'hotel_name' => $trip->hotel_name,
            'hotel_address' => $trip->hotel_address,
            'seats' => $seats,
        ]);
    }

    /**
     * Display the official Trip Passenger Manifest / Bus Statement sheet (HTML printable view).
     */
    public function manifest($id)
    {
        if (request()->has('lang') && in_array(request('lang'), ['ar', 'en'])) {
            app()->setLocale(request('lang'));
        }

        $trip = TravelTrip::forCurrentShop()
            ->with(['confirmedBookings.shop', 'confirmedBookings.customer', 'creator', 'shop.setting'])
            ->findOrFail($id);

        $bookings = $trip->confirmedBookings->sortBy('seat_number');

        return view('travel.trips.manifest', compact('trip', 'bookings'));
    }

    /**
     * Download or stream the official Trip Passenger Manifest / Bus Statement PDF.
     */
    public function manifestPdf($id)
    {
        if (request()->has('lang') && in_array(request('lang'), ['ar', 'en'])) {
            app()->setLocale(request('lang'));
        }

        $trip = TravelTrip::forCurrentShop()
            ->with(['confirmedBookings.shop', 'confirmedBookings.customer', 'creator', 'shop.setting'])
            ->findOrFail($id);

        $bookings = $trip->confirmedBookings->sortBy('seat_number');

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_top' => 5,
            'margin_bottom' => 5,
            'default_font' => 'amiri',
        ]);

        if (app()->getLocale() === 'ar') {
            $mpdf->SetDirectionality('rtl');
        }

        $html = view('travel.trips.manifest_pdf', compact('trip', 'bookings'))->render();
        $mpdf->WriteHTML($html);

        $filename = 'Trip-Manifest-' . $trip->trip_code . '-' . date('Ymd') . '.pdf';
        $disposition = request()->has('download') ? 'attachment' : 'inline';

        return response($mpdf->Output('', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', $disposition . '; filename="' . $filename . '"');
    }
}
