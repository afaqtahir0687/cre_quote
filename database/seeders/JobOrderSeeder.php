<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\JobOrder;
use App\Models\JobOrderItem;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class JobOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = Customer::all();
        $employees = Employee::all();

        if ($customers->isEmpty() || $employees->isEmpty()) {
            return;
        }

        $jobOrders = [
            [
                'order_number' => 'JO-' . date('Y') . '-0001',
                'customer_id' => $customers[2]->id,
                'employee_id' => $employees[0]->id,
                'order_date' => Carbon::now()->subDays(2),
                'status' => 'in_progress',
                'notes' => 'Urgent priority.',
                'subtotal' => 3000.00,
                'total_tax' => 450.00,
                'total_discount' => 0.00,
                'shipping' => 0.00,
                'grand_total' => 3450.00,
                'total_amount' => 3450.00,
                'paid_amount' => 0.00,
                'payment_status' => 'pending',
            ],
            [
                'order_number' => 'JO-' . date('Y') . '-0002',
                'customer_id' => $customers[3]->id,
                'employee_id' => $employees[1]->id,
                'order_date' => Carbon::now()->subDays(1),
                'status' => 'draft',
                'notes' => 'Waiting for design assets.',
                'subtotal' => 1500.00,
                'total_tax' => 225.00,
                'total_discount' => 25.00,
                'shipping' => 50.00,
                'grand_total' => 1750.00,
                'total_amount' => 1750.00,
                'paid_amount' => 1750.00,
                'payment_status' => 'paid',
            ],
        ];

        foreach ($jobOrders as $jobOrderData) {
            $jobOrder = JobOrder::create($jobOrderData);

            JobOrderItem::create([
                'job_order_id' => $jobOrder->id,
                'description' => 'Graphic Design',
                'item_text' => 'Logo and branding',
                'quantity' => 1,
                'unit_price' => $jobOrderData['subtotal'],
                'tax_percentage' => 15,
                'discount' => $jobOrderData['total_discount'],
                'total' => $jobOrderData['subtotal'] + $jobOrderData['total_tax'] - $jobOrderData['total_discount'],
            ]);
        }
    }
}
