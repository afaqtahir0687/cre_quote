<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobOrder;
use App\Models\Invoice;
use App\Models\Quote;
use App\Models\Customer;

class RecycleBinController extends Controller
{
    public function index()
    {
        $customers = Customer::onlyTrashed()->latest('deleted_at')->get();
        $jobOrders = JobOrder::onlyTrashed()->with('customer')->latest('deleted_at')->get();
        $invoices = Invoice::onlyTrashed()->with('customer')->latest('deleted_at')->get();
        $quotes = Quote::onlyTrashed()->with('customer')->latest('deleted_at')->get();

        return view('admin.recycle_bin.index', compact('customers', 'jobOrders', 'invoices', 'quotes'));
    }

    public function restore(Request $request)
    {
        $type = $request->input('type');
        $id = $request->input('id');

        $modelClass = match($type) {
            'Customer' => Customer::class,
            'JobOrder' => JobOrder::class,
            'Invoice' => Invoice::class,
            'Quote' => Quote::class,
            default => null,
        };

        if ($modelClass) {
            $record = $modelClass::onlyTrashed()->findOrFail($id);
            $record->restore();

            \App\Models\SystemLog::create([
                'user_id' => auth()->id(),
                'action' => "Restored {$type} #" . ($record->order_number ?? $record->invoice_number ?? $record->quote_number ?? $record->id),
                'model_type' => $modelClass,
                'description' => "Restored from Recycle Bin",
            ]);

            return back()->with('success', "{$type} restored successfully!");
        }

        return back()->with('error', 'Invalid type specified.');
    }
}
