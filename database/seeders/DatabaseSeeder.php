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
        if (!User::where('email', 'miran786@gmail.com')->exists()) {
            User::create([
                'name' => 'Fazal Miran',
                'email' => 'miran786@gmail.com',
                'username' => 'FazalMiran',
                'password' => Hash::make('Fazal@123'),
                'role' => 'admin',
            ]);
        }

        // Second Admin User
        if (!User::where('email', 'meemwork0687@gmail.com')->exists()) {
            User::create([
                'name' => 'Afaq Tahir',
                'email' => 'meemwork0687@gmail.com',
                'username' => 'AfaqTahir',
                'password' => Hash::make('Afaq@123'),
                'role' => 'admin',
            ]);
        }

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
