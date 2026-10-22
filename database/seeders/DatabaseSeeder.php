<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Super Admin User (Fazal Miran)
        User::updateOrCreate(
            ['email' => 'miran786@gmail.com'],
            [
                'name' => 'Fazal Miran',
                'username' => 'FazalMiran',
                'password' => Hash::make('Fazal@123'),
                'role' => 'super_admin',
            ]
        );

        // Shop Admin User (Afaq Tahir)
        User::updateOrCreate(
            ['email' => 'meemwork0687@gmail.com'],
            [
                'name' => 'Afaq Tahir',
                'username' => 'AfaqTahir',
                'password' => Hash::make('Afaq@123'),
                'role' => 'admin',
                'shop_id' => 1,
            ]
        );

        // Seed employees with real names
        $this->call(EmployeeSeeder::class);

        // Seed customers with real names
        $this->call(CustomerSeeder::class);

        // Seed Quotes
        $this->call(QuoteSeeder::class);

        // Seed Job Orders
        $this->call(JobOrderSeeder::class);
    }
}
