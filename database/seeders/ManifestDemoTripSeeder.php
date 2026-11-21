<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TravelTrip;
use App\Models\TravelBooking;
use App\Models\Shop;
use App\Models\User;
use Carbon\Carbon;

class ManifestDemoTripSeeder extends Seeder
{
    /**
     * Run the database seeds with brand new distinct names, drivers, and details.
     */
    public function run(): void
    {
        $user = User::first();
        $userId = $user ? $user->id : null;

        // Target both shops (Shop 5: Umrah Travel, and Shop 1: Main Shop)
        $shops = Shop::whereIn('id', [1, 5])->get();
        if ($shops->isEmpty()) {
            $shops = Shop::all();
        }

        // 49 Completely Brand New Unique Passengers
        $passengersData = [
            1  => ['name' => 'MOHAMMED IRFAN',   'nat' => 'PAK', 'iqama' => '2451098231', 'phone' => '0541129981', 'tkt' => '5101', 'loc' => 'BATHA',  'total' => 380.00, 'paid' => 380.00, 'due' => 0.00,   'htl' => 'Le Meridien',       'pkg' => 'with_hotel'],
            2  => ['name' => 'FARHAN AKHTAR',    'nat' => 'IND', 'iqama' => '2519827364', 'phone' => '0562918842', 'tkt' => '5102', 'loc' => 'BATHA',  'total' => 380.00, 'paid' => 0.00,   'due' => 380.00, 'htl' => 'Le Meridien',       'pkg' => 'with_hotel'],
            3  => ['name' => 'ABDUL KARIM',      'nat' => 'BAN', 'iqama' => '2634918273', 'phone' => '0538192734', 'tkt' => '5103', 'loc' => 'OFFICE', 'total' => 190.00, 'paid' => 190.00, 'due' => 0.00,   'htl' => null,                'pkg' => 'without_hotel'],
            4  => ['name' => 'KAMRAN SHAH',      'nat' => 'PAK', 'iqama' => '2489123847', 'phone' => '0551928374', 'tkt' => '5104', 'loc' => 'OFFICE', 'total' => 190.00, 'paid' => 95.00,  'due' => 95.00,  'htl' => null,                'pkg' => 'without_hotel'],
            5  => ['name' => 'SHAHID HUSSAIN',   'nat' => 'PAK', 'iqama' => '2501928374', 'phone' => '0501928374', 'tkt' => '5105', 'loc' => '12VLA',  'total' => 420.00, 'paid' => 420.00, 'due' => 0.00,   'htl' => 'Fairmont Clock',    'pkg' => 'with_hotel'],
            6  => ['name' => 'NOOR ALAM',        'nat' => 'BAN', 'iqama' => '2618293847', 'phone' => '0572819384', 'tkt' => '5106', 'loc' => '12VLA',  'total' => 190.00, 'paid' => 190.00, 'due' => 0.00,   'htl' => null,                'pkg' => 'without_hotel'],
            7  => ['name' => 'MUSTAFA KAMAL',    'nat' => 'EGY', 'iqama' => '2291827364', 'phone' => '0549182736', 'tkt' => '5107', 'loc' => 'NESTO',  'total' => 190.00, 'paid' => 0.00,   'due' => 190.00, 'htl' => null,                'pkg' => 'without_hotel'],
            8  => ['name' => 'RASHID KHAN',      'nat' => 'AFG', 'iqama' => '2381928374', 'phone' => '0561928374', 'tkt' => '5108', 'loc' => 'NESTO',  'total' => 280.00, 'paid' => 280.00, 'due' => 0.00,   'htl' => 'Jabal Omar Hyatt',  'pkg' => 'with_hotel'],
            9  => ['name' => 'DINESH BABU',      'nat' => 'IND', 'iqama' => '2581928374', 'phone' => '0539182736', 'tkt' => '5109', 'loc' => 'HRA',    'total' => 190.00, 'paid' => 190.00, 'due' => 0.00,   'htl' => null,                'pkg' => 'without_hotel'],
            10 => ['name' => 'FAHAD AL-HARBI',   'nat' => 'SAU', 'iqama' => '1092837465', 'phone' => '0509182736', 'tkt' => '5110', 'loc' => 'HRA',    'total' => 450.00, 'paid' => 450.00, 'due' => 0.00,   'htl' => 'Conrad Makkah',     'pkg' => 'with_hotel'],
            11 => ['name' => 'SOHAIL ANWAR',     'nat' => 'PAK', 'iqama' => '2491827364', 'phone' => '0558192736', 'tkt' => '5111', 'loc' => 'FNR',    'total' => 190.00, 'paid' => 0.00,   'due' => 190.00, 'htl' => null,                'pkg' => 'without_hotel'],
            12 => ['name' => 'AHMED MANSOOR',    'nat' => 'YEM', 'iqama' => '2182938475', 'phone' => '0571928374', 'tkt' => '5112', 'loc' => 'FNR',    'total' => 190.00, 'paid' => 190.00, 'due' => 0.00,   'htl' => null,                'pkg' => 'without_hotel'],
            13 => ['name' => 'ASIF JAVED',       'nat' => 'PAK', 'iqama' => '2471928374', 'phone' => '0548192736', 'tkt' => '5113', 'loc' => 'SANAYA', 'total' => 190.00, 'paid' => 100.00, 'due' => 90.00,  'htl' => null,                'pkg' => 'without_hotel'],
            14 => ['name' => 'BABAR AZAM',       'nat' => 'PAK', 'iqama' => '2561928374', 'phone' => '0568192736', 'tkt' => '5114', 'loc' => 'SANAYA', 'total' => 190.00, 'paid' => 190.00, 'due' => 0.00,   'htl' => null,                'pkg' => 'without_hotel'],
            15 => ['name' => 'MUBARAK ALI',      'nat' => 'IND', 'iqama' => '2591827364', 'phone' => '0531928374', 'tkt' => '5115', 'loc' => 'RAWDA',  'total' => 420.00, 'paid' => 420.00, 'due' => 0.00,   'htl' => 'Fairmont Clock',    'pkg' => 'with_hotel'],
            16 => ['name' => 'KABEER AHMED',     'nat' => 'BAN', 'iqama' => '2621928374', 'phone' => '0559182736', 'tkt' => '5116', 'loc' => 'RAWDA',  'total' => 190.00, 'paid' => 0.00,   'due' => 190.00, 'htl' => null,                'pkg' => 'without_hotel'],
            17 => ['name' => 'SHAMSUDDIN',       'nat' => 'IND', 'iqama' => '2541928374', 'phone' => '0579182736', 'tkt' => '5117', 'loc' => 'BATHA',  'total' => 190.00, 'paid' => 190.00, 'due' => 0.00,   'htl' => null,                'pkg' => 'without_hotel'],
            18 => ['name' => 'WAQAR YOUNIS',     'nat' => 'PAK', 'iqama' => '2431928374', 'phone' => '0541928374', 'tkt' => '5118', 'loc' => 'BATHA',  'total' => 380.00, 'paid' => 200.00, 'due' => 180.00, 'htl' => 'Le Meridien',       'pkg' => 'with_hotel'],
            19 => ['name' => 'MAHMOOD HASSAN',   'nat' => 'EGY', 'iqama' => '2281928374', 'phone' => '0567192837', 'tkt' => '5119', 'loc' => 'OFFICE', 'total' => 190.00, 'paid' => 190.00, 'due' => 0.00,   'htl' => null,                'pkg' => 'without_hotel'],
            20 => ['name' => 'ZAHIR UDDIN',      'nat' => 'BAN', 'iqama' => '2641928374', 'phone' => '0537192837', 'tkt' => '5120', 'loc' => 'OFFICE', 'total' => 190.00, 'paid' => 190.00, 'due' => 0.00,   'htl' => null,                'pkg' => 'without_hotel'],
            21 => ['name' => 'IMRAN NAZIR',      'nat' => 'PAK', 'iqama' => '2481928375', 'phone' => '0557192837', 'tkt' => '5121', 'loc' => '12VLA',  'total' => 280.00, 'paid' => 0.00,   'due' => 280.00, 'htl' => 'Jabal Omar Hyatt',  'pkg' => 'with_hotel'],
            22 => ['name' => 'NAEEM AKRAM',      'nat' => 'PAK', 'iqama' => '2461928374', 'phone' => '0577192837', 'tkt' => '5122', 'loc' => '12VLA',  'total' => 190.00, 'paid' => 190.00, 'due' => 0.00,   'htl' => null,                'pkg' => 'without_hotel'],
            23 => ['name' => 'SUDHEER KUMAR',    'nat' => 'IND', 'iqama' => '2571928374', 'phone' => '0547192837', 'tkt' => '5123', 'loc' => 'NESTO',  'total' => 190.00, 'paid' => 190.00, 'due' => 0.00,   'htl' => null,                'pkg' => 'without_hotel'],
            24 => ['name' => 'REHAN QURESHI',    'nat' => 'PAK', 'iqama' => '2521928374', 'phone' => '0566192837', 'tkt' => '5124', 'loc' => 'NESTO',  'total' => 450.00, 'paid' => 450.00, 'due' => 0.00,   'htl' => 'Conrad Makkah',     'pkg' => 'with_hotel'],
            25 => ['name' => 'HASAN TARIQ',      'nat' => 'BAN', 'iqama' => '2651928374', 'phone' => '0536192837', 'tkt' => '5125', 'loc' => 'HRA',    'total' => 190.00, 'paid' => 0.00,   'due' => 190.00, 'htl' => null,                'pkg' => 'without_hotel'],
            26 => ['name' => 'BILAL SIDDIQUI',   'nat' => 'PAK', 'iqama' => '2511928374', 'phone' => '0556192837', 'tkt' => '5126', 'loc' => 'HRA',    'total' => 190.00, 'paid' => 190.00, 'due' => 0.00,   'htl' => null,                'pkg' => 'without_hotel'],
            27 => ['name' => 'JASIM UDDIN',      'nat' => 'BAN', 'iqama' => '2661928374', 'phone' => '0576192837', 'tkt' => '5127', 'loc' => 'FNR',    'total' => 190.00, 'paid' => 190.00, 'due' => 0.00,   'htl' => null,                'pkg' => 'without_hotel'],
            28 => ['name' => 'SALMAN AZIZ',      'nat' => 'PAK', 'iqama' => '2491828374', 'phone' => '0546192837', 'tkt' => '5128', 'loc' => 'FNR',    'total' => 380.00, 'paid' => 380.00, 'due' => 0.00,   'htl' => 'Le Meridien',       'pkg' => 'with_hotel'],
            29 => ['name' => 'OMAR FAROOQ',      'nat' => 'SUD', 'iqama' => '2271928374', 'phone' => '0565192837', 'tkt' => '5129', 'loc' => 'SANAYA', 'total' => 190.00, 'paid' => 0.00,   'due' => 190.00, 'htl' => null,                'pkg' => 'without_hotel'],
            30 => ['name' => 'NASIR JAMIL',      'nat' => 'PAK', 'iqama' => '2441928374', 'phone' => '0535192837', 'tkt' => '5130', 'loc' => 'SANAYA', 'total' => 190.00, 'paid' => 100.00, 'due' => 90.00,  'htl' => null,                'pkg' => 'without_hotel'],
            31 => ['name' => 'ABDUL WAHAB',      'nat' => 'IND', 'iqama' => '2581827364', 'phone' => '0555192837', 'tkt' => '5131', 'loc' => 'RAWDA',  'total' => 420.00, 'paid' => 420.00, 'due' => 0.00,   'htl' => 'Fairmont Clock',    'pkg' => 'with_hotel'],
            32 => ['name' => 'HAMZA SHEIKH',     'nat' => 'PAK', 'iqama' => '2531928374', 'phone' => '0575192837', 'tkt' => '5132', 'loc' => 'RAWDA',  'total' => 190.00, 'paid' => 190.00, 'due' => 0.00,   'htl' => null,                'pkg' => 'without_hotel'],
            33 => ['name' => 'MAQBOOL AHMED',    'nat' => 'PAK', 'iqama' => '2421928374', 'phone' => '0545192837', 'tkt' => '5133', 'loc' => 'BATHA',  'total' => 190.00, 'paid' => 0.00,   'due' => 190.00, 'htl' => null,                'pkg' => 'without_hotel'],
            34 => ['name' => 'ROFIQUL ISLAM',    'nat' => 'BAN', 'iqama' => '2671928374', 'phone' => '0564192837', 'tkt' => '5134', 'loc' => 'BATHA',  'total' => 190.00, 'paid' => 190.00, 'due' => 0.00,   'htl' => null,                'pkg' => 'without_hotel'],
            35 => ['name' => 'KHALID MANSOOR',   'nat' => 'SAU', 'iqama' => '1082938475', 'phone' => '0534192837', 'tkt' => '5135', 'loc' => 'OFFICE', 'total' => 450.00, 'paid' => 450.00, 'due' => 0.00,   'htl' => 'Conrad Makkah',     'pkg' => 'with_hotel'],
            36 => ['name' => 'AZIMULLAH',        'nat' => 'AFG', 'iqama' => '2371928374', 'phone' => '0554192837', 'tkt' => '5136', 'loc' => 'OFFICE', 'total' => 190.00, 'paid' => 190.00, 'due' => 0.00,   'htl' => null,                'pkg' => 'without_hotel'],
            37 => ['name' => 'SHOAIB AKHTAR',    'nat' => 'PAK', 'iqama' => '2411928374', 'phone' => '0574192837', 'tkt' => '5137', 'loc' => '12VLA',  'total' => 280.00, 'paid' => 140.00, 'due' => 140.00, 'htl' => 'Jabal Omar Hyatt',  'pkg' => 'with_hotel'],
            38 => ['name' => 'MOHAMMAD ALI',     'nat' => 'BAN', 'iqama' => '2681928374', 'phone' => '0544192837', 'tkt' => '5138', 'loc' => '12VLA',  'total' => 190.00, 'paid' => 190.00, 'due' => 0.00,   'htl' => null,                'pkg' => 'without_hotel'],
            39 => ['name' => 'PARVEZ KHAN',      'nat' => 'IND', 'iqama' => '2561827364', 'phone' => '0563192837', 'tkt' => '5139', 'loc' => 'NESTO',  'total' => 190.00, 'paid' => 0.00,   'due' => 190.00, 'htl' => null,                'pkg' => 'without_hotel'],
            40 => ['name' => 'EJAZ AHMED',       'nat' => 'PAK', 'iqama' => '2451928374', 'phone' => '0533192837', 'tkt' => '5140', 'loc' => 'NESTO',  'total' => 190.00, 'paid' => 190.00, 'due' => 0.00,   'htl' => null,                'pkg' => 'without_hotel'],
            41 => ['name' => 'FAISAL MEHMOOD',   'nat' => 'PAK', 'iqama' => '2501827364', 'phone' => '0553192837', 'tkt' => '5141', 'loc' => 'HRA',    'total' => 380.00, 'paid' => 380.00, 'due' => 0.00,   'htl' => 'Le Meridien',       'pkg' => 'with_hotel'],
            42 => ['name' => 'SIRAJUL ISLAM',    'nat' => 'BAN', 'iqama' => '2691928374', 'phone' => '0573192837', 'tkt' => '5142', 'loc' => 'HRA',    'total' => 190.00, 'paid' => 0.00,   'due' => 190.00, 'htl' => null,                'pkg' => 'without_hotel'],
            43 => ['name' => 'AAMIR SOHAIL',     'nat' => 'PAK', 'iqama' => '2471827364', 'phone' => '0543192837', 'tkt' => '5143', 'loc' => 'FNR',    'total' => 190.00, 'paid' => 190.00, 'due' => 0.00,   'htl' => null,                'pkg' => 'without_hotel'],
            44 => ['name' => 'MANOJ KUMAR',      'nat' => 'IND', 'iqama' => '2551928374', 'phone' => '0562192837', 'tkt' => '5144', 'loc' => 'FNR',    'total' => 190.00, 'paid' => 190.00, 'due' => 0.00,   'htl' => null,                'pkg' => 'without_hotel'],
            45 => ['name' => 'HABIB UR REHMAN',  'nat' => 'PAK', 'iqama' => '2461827364', 'phone' => '0532192837', 'tkt' => '5145', 'loc' => 'SANAYA', 'total' => 420.00, 'paid' => 0.00,   'due' => 420.00, 'htl' => 'Fairmont Clock',    'pkg' => 'with_hotel'],
            46 => ['name' => 'ZUBAIR AHMED',     'nat' => 'BAN', 'iqama' => '2701928374', 'phone' => '0552192837', 'tkt' => '5146', 'loc' => 'SANAYA', 'total' => 190.00, 'paid' => 190.00, 'due' => 0.00,   'htl' => null,                'pkg' => 'without_hotel'],
            47 => ['name' => 'NADEEM ABBAS',     'nat' => 'PAK', 'iqama' => '2481827364', 'phone' => '0572192837', 'tkt' => '5147', 'loc' => 'RAWDA',  'total' => 190.00, 'paid' => 190.00, 'due' => 0.00,   'htl' => null,                'pkg' => 'without_hotel'],
            48 => ['name' => 'MOHSIN RAZA',      'nat' => 'PAK', 'iqama' => '2431827364', 'phone' => '0542192837', 'tkt' => '5148', 'loc' => 'RAWDA',  'total' => 190.00, 'paid' => 90.00,  'due' => 100.00, 'htl' => null,                'pkg' => 'without_hotel'],
            49 => ['name' => 'SAUD AL-MUTAIRI',  'nat' => 'SAU', 'iqama' => '1072938475', 'phone' => '0561192837', 'tkt' => '5149', 'loc' => 'BATHA',  'total' => 450.00, 'paid' => 450.00, 'due' => 0.00,   'htl' => 'Conrad Makkah',     'pkg' => 'with_hotel'],
        ];

        $departureDate = Carbon::create(2026, 12, 1, 20, 30, 0);
        $returnDate = Carbon::create(2026, 12, 5, 23, 0, 0);

        foreach ($shops as $shop) {
            $trip = TravelTrip::updateOrCreate(
                [
                    'shop_id' => $shop->id,
                    'trip_code' => 'UMR-2026-049',
                ],
                [
                    'title' => 'VIP Grand Royal Express Umrah Trip (Makkah & Madinah)',
                    'description' => 'Brand new 49-seater Volvo Grand Royal VIP coach with dual licensed captains, hotel accommodations, and citywide office pickups.',
                    'bus_name' => 'Volvo 9700 Grand VIP Express',
                    'bus_number' => '4819-KSA',
                    'driver_1_name' => 'TARIQ MEHMOOD',
                    'driver_1_nationality' => 'PAK',
                    'driver_1_phone' => '0541298450',
                    'driver_2_name' => 'ABDULLAH AL-OTAIBI',
                    'driver_2_nationality' => 'SAU',
                    'driver_2_phone' => '0558712390',
                    'total_seats' => 49,
                    'departure_city' => 'Riyadh',
                    'destination_city' => 'Makkah',
                    'departure_date' => $departureDate,
                    'return_date' => $returnDate,
                    'duration_days' => 4,
                    'price_without_hotel' => 190.00,
                    'price_with_hotel' => 380.00,
                    'hotel_name' => 'Le Meridien Towers Makkah',
                    'hotel_address' => 'Kudai Road, Makkah Al Mukarramah',
                    'hotel_rating' => 5,
                    'status' => 'upcoming',
                    'notes' => 'Completely new passenger roster and drivers updated as requested.',
                    'created_by' => $userId,
                ]
            );

            TravelBooking::where('travel_trip_id', $trip->id)->forceDelete();

            foreach ($passengersData as $seat => $data) {
                $paymentStatus = 'paid';
                if ($data['due'] > 0 && $data['paid'] > 0) {
                    $paymentStatus = 'partial';
                } elseif ($data['due'] > 0 && $data['paid'] == 0) {
                    $paymentStatus = 'unpaid';
                }

                TravelBooking::create([
                    'shop_id' => $shop->id,
                    'travel_trip_id' => $trip->id,
                    'customer_id' => null,
                    'booking_reference' => 'BK-202612-' . $data['tkt'],
                    'passenger_name' => $data['name'],
                    'passenger_phone' => $data['phone'],
                    'passenger_id_number' => $data['iqama'],
                    'passenger_gender' => 'male',
                    'passenger_nationality' => $data['nat'],
                    'pickup_location' => $data['loc'],
                    'salesman_name' => 'Yasir Arafat',
                    'salesman_phone' => '0543219876',
                    'seat_number' => $seat,
                    'package_type' => $data['pkg'],
                    'hotel_name' => $data['htl'],
                    'hotel_address' => $data['htl'] ? 'Ajyad St / Kudai, Makkah' : null,
                    'room_type' => $data['htl'] ? 'quad_sharing' : null,
                    'nights_count' => $data['htl'] ? 4 : null,
                    'travel_fare' => $data['pkg'] === 'with_hotel' ? 190.00 : $data['total'],
                    'hotel_expense' => $data['pkg'] === 'with_hotel' ? ($data['total'] - 190.00) : 0.00,
                    'extra_charges' => 0.00,
                    'discount' => 0.00,
                    'tax_amount' => 0.00,
                    'total_amount' => $data['total'],
                    'paid_amount' => $data['paid'],
                    'balance_due' => $data['due'],
                    'payment_status' => $paymentStatus,
                    'payment_method' => 'cash',
                    'booking_status' => 'confirmed',
                    'invoice_id' => null,
                    'notes' => 'Booking seat #' . $seat . ' - Location: ' . $data['loc'],
                    'created_by' => $userId,
                ]);
            }

            $this->command->info("Shop {$shop->id} ({$shop->name}): Updated Trip #{$trip->id} ({$trip->trip_code}) with 49 NEW distinct passengers and drivers.");
        }
    }
}
