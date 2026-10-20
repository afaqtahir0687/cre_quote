<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'name' => 'Ali Imran',
                'company_name' => 'Imran Traders',
                'email' => 'ali@imrantraders.com',
                'phone' => '+92-300-1111111',
                'address' => '10 Gulberg, Lahore',
                'city' => 'Lahore',
                'zip' => '54000',
                'country' => 'Pakistan',
                'tax_id' => '1111111111111',
                'vat_no' => 'PK111111111',
                'is_active' => true,
            ],
            [
                'name' => 'Nida Farooq',
                'company_name' => 'Farooq Builders',
                'email' => 'nida@farooqbuilders.com',
                'phone' => '+92-321-2222222',
                'address' => '45 F-8, Islamabad',
                'city' => 'Islamabad',
                'zip' => '44000',
                'country' => 'Pakistan',
                'tax_id' => '2222222222222',
                'vat_no' => 'PK222222222',
                'is_active' => true,
            ],
            [
                'name' => 'Salman Tariq',
                'company_name' => 'Salman Agencies',
                'email' => 'salman@salmanagencies.com',
                'phone' => '+92-333-3333333',
                'address' => '78 Clifton, Karachi',
                'city' => 'Karachi',
                'zip' => '74400',
                'country' => 'Pakistan',
                'tax_id' => '3333333333333',
                'vat_no' => 'PK333333333',
                'is_active' => true,
            ],
            [
                'name' => 'Hira Majeed',
                'company_name' => 'Majeed Logistics',
                'email' => 'hira@majeedlogistics.com',
                'phone' => '+92-300-4444444',
                'address' => '32 Cantt, Multan',
                'city' => 'Multan',
                'zip' => '60000',
                'country' => 'Pakistan',
                'tax_id' => '4444444444444',
                'vat_no' => 'PK444444444',
                'is_active' => true,
            ],
            [
                'name' => 'Bilal Riaz',
                'company_name' => 'Riaz Fabrics',
                'email' => 'bilal@riazfabrics.com',
                'phone' => '+92-322-5555555',
                'address' => '65 Peoples Colony, Faisalabad',
                'city' => 'Faisalabad',
                'zip' => '38000',
                'country' => 'Pakistan',
                'tax_id' => '5555555555555',
                'vat_no' => 'PK555555555',
                'is_active' => true,
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
