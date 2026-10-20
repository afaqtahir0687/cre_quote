<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\Quote;
use App\Models\QuoteItem;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class QuoteSeeder extends Seeder
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

        $quotes = [
            [
                'quote_number' => 'QT-' . date('Y') . '-0001',
                'quote_date' => Carbon::now()->subDays(10),
                'due_date' => Carbon::now()->addDays(20),
                'reference' => 'REF-001',
                'customer_id' => $customers[0]->id,
                'employee_id' => $employees[0]->id,
                'subtotal' => 1000.00,
                'total_tax' => 150.00,
                'total_discount' => 50.00,
                'shipping' => 100.00,
                'grand_total' => 1200.00,
                'status' => 'draft',
                'notes' => 'Looking forward to your business.',
                'images' => [],
            ],
            [
                'quote_number' => 'QT-' . date('Y') . '-0002',
                'quote_date' => Carbon::now()->subDays(5),
                'due_date' => Carbon::now()->addDays(25),
                'reference' => 'REF-002',
                'customer_id' => $customers[1]->id,
                'employee_id' => $employees[1]->id,
                'subtotal' => 2500.00,
                'total_tax' => 375.00,
                'total_discount' => 100.00,
                'shipping' => 200.00,
                'grand_total' => 2975.00,
                'status' => 'sent',
                'notes' => 'Please confirm if you accept.',
                'images' => [],
            ],
        ];

        foreach ($quotes as $quoteData) {
            $quote = Quote::create($quoteData);

            QuoteItem::create([
                'quote_id' => $quote->id,
                'description' => 'Web Development Service',
                'item_text' => 'Full stack development',
                'quantity' => 1,
                'unit_price' => $quoteData['subtotal'],
                'tax_percentage' => 15,
                'discount' => $quoteData['total_discount'],
                'total' => $quoteData['subtotal'] + $quoteData['total_tax'] - $quoteData['total_discount'],
            ]);
        }
    }
}
