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
        $invoice->balance_due = max(0, (float)$invoice->grand_total - $totalPaid);

        if ($invoice->balance_due <= 0) {
            $invoice->status = 'Paid';
        } elseif ($invoice->amount_paid > 0) {
            $invoice->status = 'Partially Paid';
        }

        $invoice->save();

        // Synchronize linked travel booking(s) if any
        $travelBookings = \App\Models\TravelBooking::where('invoice_id', $invoice->id)->get();
        if ($travelBookings->isNotEmpty()) {
            $bookingCount = $travelBookings->count();
            $remainingPaid = (float) $invoice->amount_paid;
            foreach ($travelBookings as $idx => $tBooking) {
                $bTotal = (float) $tBooking->total_amount;
                if ($idx === $bookingCount - 1) {
                    $bPaid = $remainingPaid;
                } else {
                    $bPaid = min($bTotal, $remainingPaid);
                    $remainingPaid -= $bPaid;
                }
                $bBal = max(0, $bTotal - $bPaid);
                $bStatus = $bBal <= 0 && $bTotal > 0 ? 'paid' : ($bPaid > 0 ? 'partial' : 'unpaid');

                $tBooking->update([
                    'paid_amount' => $bPaid,
                    'balance_due' => $bBal,
                    'payment_status' => $bStatus,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Payment recorded successfully.');
    }
}
