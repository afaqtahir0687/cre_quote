<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shop;
use App\Models\User;
use App\Models\Customer;
use App\Models\TravelTrip;
use App\Models\TravelBooking;
use Carbon\Carbon;

class TravelAndUmrahSeeder extends Seeder
{
    /**
     * Run the database seeds for Travel & Umrah Module.
     */
    public function run(): void
    {
        // Find candidate shops to seed
        $shops = Shop::where('business_type', 'travel_umrah')
            ->orWhere('id', 5)
            ->get();

        // If no travel shop exists, also include main shop 1
        if ($shops->isEmpty()) {
            $shops = Shop::whereIn('id', [1, 5])->get();
        }

        // Also ensure shop 1 has travel_tourism module enabled for instant testing
        $mainShop = Shop::find(1);
        if ($mainShop) {
            $modules = $mainShop->enabled_modules ?? [];
            if (!in_array('travel_tourism', $modules, true)) {
                $modules[] = 'travel_tourism';
                $mainShop->enabled_modules = $modules;
                $mainShop->save();
            }
            if (!$shops->contains('id', 1)) {
                $shops->push($mainShop);
            }
        }

        foreach ($shops as $shop) {
            $this->seedShopTravelData($shop);
        }
    }

    private function seedShopTravelData(Shop $shop): void
    {
        $creator = User::where('shop_id', $shop->id)->first() ?? User::first();
        $creatorId = $creator ? $creator->id : null;

        // 1. Create or Find Pilgrims / Customers for this shop
        $pilgrimData = [
            ['name' => 'Sheikh Tariq Al-Ghamdi', 'phone' => '+966 50 123 4567', 'email' => 'tariq.ghamdi@example.com', 'city' => 'Riyadh'],
            ['name' => 'Haji Muhammad Usman', 'phone' => '+966 55 987 6543', 'email' => 'usman.haji@example.com', 'city' => 'Jeddah'],
            ['name' => 'Abdul Aziz Al-Shammari', 'phone' => '+966 54 222 3344', 'email' => 'shammari.aziz@example.com', 'city' => 'Dammam'],
            ['name' => 'Fatima Bint Abdullah', 'phone' => '+966 56 333 4455', 'email' => 'fatima.abdullah@example.com', 'city' => 'Riyadh'],
            ['name' => 'Dr. Omar Al-Faruq', 'phone' => '+966 53 444 5566', 'email' => 'omar.faruq@example.com', 'city' => 'Makkah'],
            ['name' => 'Haji Bilal Siddique', 'phone' => '+966 59 555 6677', 'email' => 'bilal.siddique@example.com', 'city' => 'Madinah'],
            ['name' => 'Zainab Al-Husseini', 'phone' => '+966 50 666 7788', 'email' => 'zainab.husseini@example.com', 'city' => 'Khobar'],
            ['name' => 'Khalid Mansoor', 'phone' => '+966 55 777 8899', 'email' => 'khalid.mansoor@example.com', 'city' => 'Riyadh'],
        ];

        $customerMap = [];
        foreach ($pilgrimData as $idx => $p) {
            $customer = Customer::withoutGlobalScopes()
                ->where('shop_id', $shop->id)
                ->where('email', $p['email'])
                ->first();

            if (!$customer) {
                $customer = Customer::create([
                    'shop_id' => $shop->id,
                    'name' => $p['name'],
                    'phone' => $p['phone'],
                    'email' => $p['email'],
                    'city' => $p['city'],
                    'country' => 'Saudi Arabia',
                    'is_active' => true,
                ]);
            }
            $customerMap[$idx] = $customer;
        }

        // 2. Define 4 Distinct Realistic Umrah Trips
        $tripsDefs = [
            [
                'title' => 'Riyadh to Makkah VIP Umrah Package - 10 Days',
                'description' => 'Luxury VIP 49-seat coach bus with 5-star hotel in Makkah directly opposite the Haram courtyard.',
                'bus_name' => 'Mercedes-Benz Travego 49-Seater',
                'bus_number' => 'KSA-7821',
                'total_seats' => 49,
                'departure_city' => 'Riyadh',
                'destination_city' => 'Makkah',
                'departure_date' => Carbon::now()->addDays(3)->setTime(8, 0),
                'return_date' => Carbon::now()->addDays(13)->setTime(18, 0),
                'duration_days' => 10,
                'price_without_hotel' => 350.00,
                'price_with_hotel' => 1850.00,
                'hotel_name' => 'Dar Al-Tawhid InterContinental 5-Star',
                'hotel_rating' => '5-Star',
                'status' => 'upcoming',
                'notes' => 'Complimentary Zamzam water and Ihram guide kits provided to all booked pilgrims.',
                'booked_seats' => [
                    ['seat' => 1, 'name' => 'Sheikh Tariq Al-Ghamdi', 'phone' => '+966 50 123 4567', 'gender' => 'male', 'pkg' => 'with_hotel', 'room' => 'double_sharing', 'paid' => 1850.00, 'method' => 'card', 'cid' => 0],
                    ['seat' => 2, 'name' => 'Umm Tariq Al-Ghamdi', 'phone' => '+966 50 123 4568', 'gender' => 'female', 'pkg' => 'with_hotel', 'room' => 'double_sharing', 'paid' => 1850.00, 'method' => 'card', 'cid' => 0],
                    ['seat' => 3, 'name' => 'Haji Muhammad Usman', 'phone' => '+966 55 987 6543', 'gender' => 'male', 'pkg' => 'with_hotel', 'room' => 'quad_sharing', 'paid' => 1850.00, 'method' => 'bank_transfer', 'cid' => 1],
                    ['seat' => 4, 'name' => 'Hamza Muhammad Usman', 'phone' => '+966 55 987 6544', 'gender' => 'male', 'pkg' => 'with_hotel', 'room' => 'quad_sharing', 'paid' => 1850.00, 'method' => 'bank_transfer', 'cid' => 1],
                    ['seat' => 7, 'name' => 'Abdul Aziz Al-Shammari', 'phone' => '+966 54 222 3344', 'gender' => 'male', 'pkg' => 'without_hotel', 'room' => null, 'paid' => 350.00, 'method' => 'cash', 'cid' => 2],
                    ['seat' => 8, 'name' => 'Sultan Al-Shammari', 'phone' => '+966 54 222 3345', 'gender' => 'male', 'pkg' => 'without_hotel', 'room' => null, 'paid' => 350.00, 'method' => 'cash', 'cid' => 2],
                    ['seat' => 11, 'name' => 'Fatima Bint Abdullah', 'phone' => '+966 56 333 4455', 'gender' => 'female', 'pkg' => 'with_hotel', 'room' => 'triple_sharing', 'paid' => 1000.00, 'method' => 'cash', 'cid' => 3],
                    ['seat' => 12, 'name' => 'Maryam Bint Abdullah', 'phone' => '+966 56 333 4456', 'gender' => 'female', 'pkg' => 'with_hotel', 'room' => 'triple_sharing', 'paid' => 1000.00, 'method' => 'cash', 'cid' => 3],
                    ['seat' => 15, 'name' => 'Dr. Omar Al-Faruq', 'phone' => '+966 53 444 5566', 'gender' => 'male', 'pkg' => 'with_hotel', 'room' => 'private_room', 'paid' => 2500.00, 'method' => 'card', 'cid' => 4],
                    ['seat' => 16, 'name' => 'Haji Bilal Siddique', 'phone' => '+966 59 555 6677', 'gender' => 'male', 'pkg' => 'with_hotel', 'room' => 'quad_sharing', 'paid' => 1850.00, 'method' => 'online', 'cid' => 5],
                    ['seat' => 21, 'name' => 'Zainab Al-Husseini', 'phone' => '+966 50 666 7788', 'gender' => 'female', 'pkg' => 'with_hotel', 'room' => 'double_sharing', 'paid' => 1850.00, 'method' => 'card', 'cid' => 6],
                    ['seat' => 22, 'name' => 'Khalid Mansoor', 'phone' => '+966 55 777 8899', 'gender' => 'male', 'pkg' => 'without_hotel', 'room' => null, 'paid' => 350.00, 'method' => 'cash', 'cid' => 7],
                    ['seat' => 27, 'name' => 'Ibrahim Al-Najjar', 'phone' => '+966 50 888 1122', 'gender' => 'male', 'pkg' => 'with_hotel', 'room' => 'quad_sharing', 'paid' => 1200.00, 'method' => 'cash', 'cid' => null],
                    ['seat' => 28, 'name' => 'Yusuf Al-Qarni', 'phone' => '+966 54 999 2233', 'gender' => 'male', 'pkg' => 'with_hotel', 'room' => 'quad_sharing', 'paid' => 1850.00, 'method' => 'bank_transfer', 'cid' => null],
                    ['seat' => 33, 'name' => 'Saad Bin Ziyad', 'phone' => '+966 55 111 3344', 'gender' => 'male', 'pkg' => 'without_hotel', 'room' => null, 'paid' => 0.00, 'method' => 'cash', 'cid' => null],
                    ['seat' => 34, 'name' => 'Anas Bin Malik', 'phone' => '+966 53 222 4455', 'gender' => 'male', 'pkg' => 'without_hotel', 'room' => null, 'paid' => 350.00, 'method' => 'cash', 'cid' => null],
                    ['seat' => 45, 'name' => 'Mustafa Kamal', 'phone' => '+966 56 333 5566', 'gender' => 'male', 'pkg' => 'with_hotel', 'room' => 'quad_sharing', 'paid' => 1850.00, 'method' => 'online', 'cid' => null],
                    ['seat' => 46, 'name' => 'Hassan Raza', 'phone' => '+966 59 444 6677', 'gender' => 'male', 'pkg' => 'with_hotel', 'room' => 'quad_sharing', 'paid' => 1850.00, 'method' => 'card', 'cid' => null],
                ]
            ],
            [
                'title' => 'Jeddah to Madinah Ziyarat Express - 7 Days',
                'description' => 'Fast executive transfer to Madinah Al-Munawwarah with 4-star hotel close to Masjid An-Nabawi.',
                'bus_name' => 'Volvo 9700 Luxury Coach 49-Seater',
                'bus_number' => 'KSA-4419',
                'total_seats' => 49,
                'departure_city' => 'Jeddah',
                'destination_city' => 'Madinah',
                'departure_date' => Carbon::now()->addDays(6)->setTime(14, 0),
                'return_date' => Carbon::now()->addDays(13)->setTime(20, 0),
                'duration_days' => 7,
                'price_without_hotel' => 280.00,
                'price_with_hotel' => 1450.00,
                'hotel_name' => 'Pullman Zamzam Madina 5-Star',
                'hotel_rating' => '5-Star',
                'status' => 'upcoming',
                'notes' => 'Includes organized Ziyarat visits to Mount Uhud, Masjid Quba, and Masjid Al-Qiblatayn.',
                'booked_seats' => [
                    ['seat' => 1, 'name' => 'Dr. Omar Al-Faruq', 'phone' => '+966 53 444 5566', 'gender' => 'male', 'pkg' => 'with_hotel', 'room' => 'double_sharing', 'paid' => 1450.00, 'method' => 'card', 'cid' => 4],
                    ['seat' => 2, 'name' => 'Aisha Al-Faruq', 'phone' => '+966 53 444 5567', 'gender' => 'female', 'pkg' => 'with_hotel', 'room' => 'double_sharing', 'paid' => 1450.00, 'method' => 'card', 'cid' => 4],
                    ['seat' => 5, 'name' => 'Haji Muhammad Usman', 'phone' => '+966 55 987 6543', 'gender' => 'male', 'pkg' => 'without_hotel', 'room' => null, 'paid' => 280.00, 'method' => 'cash', 'cid' => 1],
                    ['seat' => 6, 'name' => 'Zubair Usman', 'phone' => '+966 55 987 6545', 'gender' => 'male', 'pkg' => 'without_hotel', 'room' => null, 'paid' => 280.00, 'method' => 'cash', 'cid' => 1],
                    ['seat' => 9, 'name' => 'Fatima Bint Abdullah', 'phone' => '+966 56 333 4455', 'gender' => 'female', 'pkg' => 'with_hotel', 'room' => 'triple_sharing', 'paid' => 1450.00, 'method' => 'bank_transfer', 'cid' => 3],
                    ['seat' => 10, 'name' => 'Khadija Bint Abdullah', 'phone' => '+966 56 333 4457', 'gender' => 'female', 'pkg' => 'with_hotel', 'room' => 'triple_sharing', 'paid' => 1450.00, 'method' => 'bank_transfer', 'cid' => 3],
                    ['seat' => 17, 'name' => 'Rashid Al-Dosari', 'phone' => '+966 50 111 8899', 'gender' => 'male', 'pkg' => 'without_hotel', 'room' => null, 'paid' => 280.00, 'method' => 'cash', 'cid' => null],
                    ['seat' => 18, 'name' => 'Salem Al-Otaibi', 'phone' => '+966 54 222 9900', 'gender' => 'male', 'pkg' => 'with_hotel', 'room' => 'quad_sharing', 'paid' => 1000.00, 'method' => 'cash', 'cid' => null],
                    ['seat' => 25, 'name' => 'Zainab Al-Husseini', 'phone' => '+966 50 666 7788', 'gender' => 'female', 'pkg' => 'with_hotel', 'room' => 'quad_sharing', 'paid' => 1450.00, 'method' => 'card', 'cid' => 6],
                    ['seat' => 26, 'name' => 'Huda Al-Husseini', 'phone' => '+966 50 666 7789', 'gender' => 'female', 'pkg' => 'with_hotel', 'room' => 'quad_sharing', 'paid' => 1450.00, 'method' => 'card', 'cid' => 6],
                ]
            ],
            [
                'title' => 'Dammam to Makkah & Madinah Full Pilgrimage - 14 Days',
                'description' => 'Comprehensive 2-week pilgrimage starting from Eastern Province covering 7 nights in Makkah and 6 nights in Madinah.',
                'bus_name' => "MAN Lion's Coach 49-Seater",
                'bus_number' => 'KSA-9902',
                'total_seats' => 49,
                'departure_city' => 'Dammam',
                'destination_city' => 'Makkah',
                'departure_date' => Carbon::now()->addDays(11)->setTime(6, 30),
                'return_date' => Carbon::now()->addDays(25)->setTime(16, 0),
                'duration_days' => 14,
                'price_without_hotel' => 450.00,
                'price_with_hotel' => 2300.00,
                'hotel_name' => 'Swissôtel Al Maqam Makkah',
                'hotel_rating' => '5-Star',
                'status' => 'upcoming',
                'notes' => 'Long distance route with full comfort stops, WiFi onboard, and bilingual tour leader.',
                'booked_seats' => [
                    ['seat' => 3, 'name' => 'Abdul Aziz Al-Shammari', 'phone' => '+966 54 222 3344', 'gender' => 'male', 'pkg' => 'with_hotel', 'room' => 'double_sharing', 'paid' => 2300.00, 'method' => 'bank_transfer', 'cid' => 2],
                    ['seat' => 4, 'name' => 'Fahad Al-Shammari', 'phone' => '+966 54 222 3346', 'gender' => 'male', 'pkg' => 'with_hotel', 'room' => 'double_sharing', 'paid' => 2300.00, 'method' => 'bank_transfer', 'cid' => 2],
                    ['seat' => 13, 'name' => 'Haji Bilal Siddique', 'phone' => '+966 59 555 6677', 'gender' => 'male', 'pkg' => 'with_hotel', 'room' => 'quad_sharing', 'paid' => 2300.00, 'method' => 'online', 'cid' => 5],
                    ['seat' => 14, 'name' => 'Tariq Siddique', 'phone' => '+966 59 555 6678', 'gender' => 'male', 'pkg' => 'with_hotel', 'room' => 'quad_sharing', 'paid' => 1500.00, 'method' => 'cash', 'cid' => 5],
                    ['seat' => 23, 'name' => 'Khalid Mansoor', 'phone' => '+966 55 777 8899', 'gender' => 'male', 'pkg' => 'without_hotel', 'room' => null, 'paid' => 450.00, 'method' => 'cash', 'cid' => 7],
                    ['seat' => 24, 'name' => 'Adnan Mansoor', 'phone' => '+966 55 777 8890', 'gender' => 'male', 'pkg' => 'without_hotel', 'room' => null, 'paid' => 450.00, 'method' => 'cash', 'cid' => 7],
                ]
            ],
            [
                'title' => 'Riyadh to Makkah Executive Umrah Sprint - 3 Days',
                'description' => 'Fast weekend express departing Thursday evening and returning Sunday morning for working professionals.',
                'bus_name' => 'Neoplan Tourliner 49-Seater',
                'bus_number' => 'KSA-1205',
                'total_seats' => 49,
                'departure_city' => 'Riyadh',
                'destination_city' => 'Makkah',
                'departure_date' => Carbon::now()->addDays(16)->setTime(21, 0),
                'return_date' => Carbon::now()->addDays(19)->setTime(9, 0),
                'duration_days' => 3,
                'price_without_hotel' => 200.00,
                'price_with_hotel' => 950.00,
                'hotel_name' => 'Hilton Suites Makkah',
                'hotel_rating' => '5-Star',
                'status' => 'upcoming',
                'notes' => 'Weekend express trip designed for working professionals.',
                'booked_seats' => [
                    ['seat' => 1, 'name' => 'Sheikh Tariq Al-Ghamdi', 'phone' => '+966 50 123 4567', 'gender' => 'male', 'pkg' => 'with_hotel', 'room' => 'double_sharing', 'paid' => 950.00, 'method' => 'card', 'cid' => 0],
                    ['seat' => 2, 'name' => 'Haji Muhammad Usman', 'phone' => '+966 55 987 6543', 'gender' => 'male', 'pkg' => 'with_hotel', 'room' => 'double_sharing', 'paid' => 950.00, 'method' => 'card', 'cid' => 1],
                    ['seat' => 48, 'name' => 'Farooq Al-Harbi', 'phone' => '+966 50 333 7788', 'gender' => 'male', 'pkg' => 'without_hotel', 'room' => null, 'paid' => 200.00, 'method' => 'cash', 'cid' => null],
                    ['seat' => 49, 'name' => 'Nawaf Al-Mutairi', 'phone' => '+966 55 444 8899', 'gender' => 'male', 'pkg' => 'without_hotel', 'room' => null, 'paid' => 0.00, 'method' => 'cash', 'cid' => null],
                ]
            ],
        ];

        foreach ($tripsDefs as $tripIndex => $tDef) {
            $tripCode = 'UMR-' . date('Y') . '-' . str_pad($tripIndex + 1, 3, '0', STR_PAD_LEFT);

            $trip = TravelTrip::withoutGlobalScopes()
                ->where('shop_id', $shop->id)
                ->where('trip_code', $tripCode)
                ->first();

            if (!$trip) {
                $trip = TravelTrip::create([
                    'shop_id' => $shop->id,
                    'trip_code' => $tripCode,
                    'title' => $tDef['title'],
                    'description' => $tDef['description'],
                    'bus_name' => $tDef['bus_name'],
                    'bus_number' => $tDef['bus_number'],
                    'total_seats' => $tDef['total_seats'],
                    'departure_city' => $tDef['departure_city'],
                    'destination_city' => $tDef['destination_city'],
                    'departure_date' => $tDef['departure_date'],
                    'return_date' => $tDef['return_date'],
                    'duration_days' => $tDef['duration_days'],
                    'price_without_hotel' => $tDef['price_without_hotel'],
                    'price_with_hotel' => $tDef['price_with_hotel'],
                    'hotel_name' => $tDef['hotel_name'],
                    'hotel_rating' => $tDef['hotel_rating'],
                    'status' => $tDef['status'],
                    'notes' => $tDef['notes'],
                    'created_by' => $creatorId,
                ]);
            }

            // Create Bookings for this trip
            foreach ($tDef['booked_seats'] as $bIdx => $bDef) {
                $ref = 'BK-' . date('Ym') . '-' . str_pad(($tripIndex * 50) + $bIdx + 1, 4, '0', STR_PAD_LEFT);

                $existingBooking = TravelBooking::withoutGlobalScopes()
                    ->where('travel_trip_id', $trip->id)
                    ->where('seat_number', $bDef['seat'])
                    ->first();

                if ($existingBooking) {
                    continue;
                }

                $isWithHotel = $bDef['pkg'] === 'with_hotel';
                $travelFare = $tDef['price_without_hotel'];
                $hotelExpense = $isWithHotel ? ($tDef['price_with_hotel'] - $tDef['price_without_hotel']) : 0.00;
                $extraCharges = ($bDef['room'] === 'private_room') ? 650.00 : 0.00;
                $totalAmount = $travelFare + $hotelExpense + $extraCharges;
                $paidAmount = min($totalAmount, (float)$bDef['paid']);
                $balanceDue = max(0.00, $totalAmount - $paidAmount);

                $paymentStatus = 'unpaid';
                if ($paidAmount >= $totalAmount) {
                    $paymentStatus = 'paid';
                } elseif ($paidAmount > 0) {
                    $paymentStatus = 'partial';
                }

                $customer = (isset($bDef['cid']) && isset($customerMap[$bDef['cid']]))
                    ? $customerMap[$bDef['cid']]
                    : null;

                TravelBooking::create([
                    'shop_id' => $shop->id,
                    'travel_trip_id' => $trip->id,
                    'customer_id' => $customer ? $customer->id : null,
                    'booking_reference' => $ref,
                    'passenger_name' => $bDef['name'],
                    'passenger_phone' => $bDef['phone'],
                    'passenger_id_number' => '10' . rand(10000000, 99999999),
                    'passenger_gender' => $bDef['gender'],
                    'seat_number' => $bDef['seat'],
                    'package_type' => $bDef['pkg'],
                    'hotel_name' => $isWithHotel ? $tDef['hotel_name'] : null,
                    'room_type' => $bDef['room'],
                    'nights_count' => $isWithHotel ? $tDef['duration_days'] : null,
                    'travel_fare' => $travelFare,
                    'hotel_expense' => $hotelExpense,
                    'extra_charges' => $extraCharges,
                    'discount' => 0.00,
                    'tax_amount' => 0.00,
                    'total_amount' => $totalAmount,
                    'paid_amount' => $paidAmount,
                    'balance_due' => $balanceDue,
                    'payment_status' => $paymentStatus,
                    'payment_method' => $bDef['method'],
                    'booking_status' => 'confirmed',
                    'notes' => 'Seeded test passenger booking for Umrah bus fleet.',
                    'created_by' => $creatorId,
                ]);
            }
        }
    }
}
