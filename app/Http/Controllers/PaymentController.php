<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request, Invoice $invoice)
    {
        if (strtolower($invoice->status) === 'cancelled') {
            return redirect()->back()->with('error', 'Cannot record payment for a cancelled invoice.');
        }

        $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
        ]);

        $invoice->payments()->create($request->all());

        $totalPaid = $invoice->payments()->sum('amount');
        $invoice->amount_paid = $totalPaid;
        $invoice->balance_due = $invoice->grand_total - $totalPaid;

        if ($invoice->balance_due <= 0) {
            $invoice->status = 'Paid';
        } elseif ($invoice->amount_paid > 0) {
            $invoice->status = 'Partially Paid';
        }

        $invoice->save();

        return redirect()->back()->with('success', 'Payment recorded successfully.');
    }
}
