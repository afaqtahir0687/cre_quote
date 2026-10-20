<?php

namespace App\Http\Controllers;

use App\Models\DeliveryNote;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Employee;
use Illuminate\Http\Request;

class DeliveryNoteController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = DeliveryNote::with(['customer']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('delivery_note_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('delivery_date', $request->date);
        }

        $deliveryNotes = $query->latest('id')->paginate(10)->withQueryString();
        return view('admin.delivery_notes.index', compact('deliveryNotes'));
    }

    public function create()
    {
        $customers = Customer::latest()->get();
        $employees = Employee::all();
        $invoices = Invoice::all();
        return view('admin.delivery_notes.create_manual', compact('customers', 'employees', 'invoices'));
    }

    public function createFromInvoice($invoiceId)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        return view('admin.delivery_notes.create', compact('invoice'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'delivery_date' => 'required|date',
            'delivery_address' => 'nullable|string',
            'notes' => 'nullable|string',
            'customer_id' => 'required|exists:customers,id',
            'employee_id' => 'nullable|exists:employees,id',
            'invoice_id' => 'nullable|exists:invoices,id',
        ]);

        // Generate delivery number
        $deliveryNumber = 'DN-' . date('Y') . '-' . str_pad(DeliveryNote::count() + 1, 4, '0', STR_PAD_LEFT);

        $deliveryNote = DeliveryNote::create([
            'delivery_number' => $deliveryNumber,
            'delivery_date' => $validated['delivery_date'],
            'invoice_id' => $validated['invoice_id'] ?? null,
            'customer_id' => $validated['customer_id'],
            'employee_id' => $validated['employee_id'],
            'delivery_address' => $validated['delivery_address'],
            'notes' => $validated['notes'],
            'status' => 'Pending',
        ]);

        return redirect()->route('delivery_notes.index')
            ->with('success', 'Delivery Note created successfully.');
    }

    public function show(DeliveryNote $deliveryNote)
    {
        $deliveryNote->load(['invoice.items', 'customer', 'employee']);
        return view('admin.delivery_notes.show', compact('deliveryNote'));
    }

    public function edit(DeliveryNote $deliveryNote)
    {
        $customers = Customer::latest()->get();
        $employees = Employee::all();
        $invoices = Invoice::all();
        return view('admin.delivery_notes.edit', compact('deliveryNote', 'customers', 'employees', 'invoices'));
    }

    public function update(Request $request, DeliveryNote $deliveryNote)
    {
        $validated = $request->validate([
            'delivery_date' => 'required|date',
            'delivery_address' => 'nullable|string',
            'notes' => 'nullable|string',
            'customer_id' => 'required|exists:customers,id',
            'employee_id' => 'nullable|exists:employees,id',
            'invoice_id' => 'nullable|exists:invoices,id',
        ]);

        $deliveryNote->update([
            'delivery_date' => $validated['delivery_date'],
            'invoice_id' => $validated['invoice_id'] ?? null,
            'customer_id' => $validated['customer_id'],
            'employee_id' => $validated['employee_id'],
            'delivery_address' => $validated['delivery_address'],
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('delivery_notes.index')
            ->with('success', 'Delivery Note updated successfully.');
    }

    public function generatePDF(DeliveryNote $deliveryNote)
    {
        $deliveryNote->load(['invoice.items', 'customer', 'employee']);
        $setting = \App\Models\Setting::first();
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.delivery_notes.pdf', compact('deliveryNote', 'setting'));
        
        return $pdf->download('delivery-note-' . $deliveryNote->delivery_number . '.pdf');
    }

    public function updateStatus(Request $request, DeliveryNote $deliveryNote)
    {
        $validated = $request->validate([
            'status' => 'required|in:Pending,Delivered,Cancelled',
        ]);

        $deliveryNote->update(['status' => $validated['status']]);

        return redirect()->back()
            ->with('success', 'Delivery Note status updated successfully.');
    }

    public function convertFromInvoice(Invoice $invoice)
    {
        // Check if delivery note already exists for this invoice
        $existingDeliveryNote = DeliveryNote::where('invoice_id', $invoice->id)->first();
        if ($existingDeliveryNote) {
            return redirect()->back()
                ->with('error', 'A delivery note already exists for this invoice.');
        }

        // Generate delivery number
        $deliveryNumber = 'DN-' . date('Y') . '-' . str_pad(DeliveryNote::count() + 1, 4, '0', STR_PAD_LEFT);

        // Create delivery note from invoice
        $deliveryNote = DeliveryNote::create([
            'delivery_number' => $deliveryNumber,
            'delivery_date' => date('Y-m-d'),
            'invoice_id' => $invoice->id,
            'customer_id' => $invoice->customer_id,
            'employee_id' => $invoice->employee_id,
            'delivery_address' => $invoice->customer->address,
            'notes' => 'Created from Invoice ' . $invoice->invoice_number,
            'status' => 'Pending',
        ]);

        return redirect()->route('delivery_notes.show', $deliveryNote)
            ->with('success', 'Delivery Note created successfully from Invoice.');
    }
}
