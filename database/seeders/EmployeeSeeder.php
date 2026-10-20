<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = [
            [
                'name' => 'Waqas Ahmad',
                'designation' => 'Sales Manager',
                'username' => 'waqas.ahmad',
                'password' => 'password123',
            ],
            [
                'name' => 'Sara Bilal',
                'designation' => 'Accountant',
                'username' => 'sara.bilal',
                'password' => 'password123',
            ],
        ];

        foreach ($employees as $employeeData) {
            // Create user with employee role
            $user = User::create([
                'name' => $employeeData['name'],
                'email' => $employeeData['username'] . '@crelogics.com',
                'username' => $employeeData['username'],
                'password' => Hash::make($employeeData['password']),
                'role' => 'employee',
            ]);

            // Create employee linked to user
            Employee::create([
                'name' => $employeeData['name'],
                'designation' => $employeeData['designation'],
                'user_id' => $user->id,
            ]);
        }
    }
}
