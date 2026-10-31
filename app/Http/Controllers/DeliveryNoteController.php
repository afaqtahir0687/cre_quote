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
        $query = DeliveryNote::with(['customer', 'customerCompany']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('delivery_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('customerCompany', function($cq) use ($search) {
                      $cq->where('company_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('delivery_date', $request->date);
        }

        $deliveryNotes = $query->latest('id')->paginate(10)->withQueryString();
        $employees = Employee::orderBy('name')->get();
        return view('admin.delivery_notes.index', compact('deliveryNotes', 'employees'));
    }

    public function create()
    {
        $customers = Customer::where('is_active', true)->with('companies')->latest()->get();
        $employees = Employee::all();
        $invoices = Invoice::all();
        $setting = current_setting();
        return view('admin.delivery_notes.create_manual', compact('customers', 'employees', 'invoices', 'setting'));
    }

    public function createFromInvoice($invoiceId)
    {
        $invoice = Invoice::with(['customer', 'customerCompany'])->findOrFail($invoiceId);
        $setting = current_setting();
        return view('admin.delivery_notes.create', compact('invoice', 'setting'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'delivery_date' => 'required|date',
            'delivery_address' => 'nullable|string',
            'notes' => 'nullable|string',
            'customer_id' => 'required|exists:customers,id',
            'customer_company_id' => 'nullable|exists:customer_companies,id',
            'employee_id' => 'nullable|exists:employees,id',
            'invoice_id' => 'nullable|exists:invoices,id',
        ]);

        if (empty($validated['customer_company_id']) && !empty($validated['customer_id'])) {
            $cust = Customer::find($validated['customer_id']);
            $validated['customer_company_id'] = $cust?->defaultCompany()?->id;
        }

        // Generate delivery number
        $deliveryNumber = 'DN-' . date('Y') . '-' . str_pad(DeliveryNote::count() + 1, 4, '0', STR_PAD_LEFT);

        $deliveryNote = DeliveryNote::create([
            'delivery_number' => $deliveryNumber,
            'delivery_date' => $validated['delivery_date'],
            'invoice_id' => $validated['invoice_id'] ?? null,
            'customer_id' => $validated['customer_id'],
            'customer_company_id' => $validated['customer_company_id'] ?? null,
            'show_customer_name' => $request->boolean('show_customer_name', true),
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
        $deliveryNote->load(['invoice.items', 'customer', 'customerCompany', 'employee']);
        $employees = \App\Models\Employee::orderBy('name')->get();
        return view('admin.delivery_notes.show', compact('deliveryNote', 'employees'));
    }

    public function edit(DeliveryNote $deliveryNote)
    {
        $deliveryNote->load('customerCompany');
        $customers = Customer::where('is_active', true)->with('companies')->latest()->get();
        $employees = Employee::all();
        $invoices = Invoice::all();
        $setting = current_setting();
        return view('admin.delivery_notes.edit', compact('deliveryNote', 'customers', 'employees', 'invoices', 'setting'));
    }

    public function update(Request $request, DeliveryNote $deliveryNote)
    {
        $validated = $request->validate([
            'delivery_date' => 'required|date',
            'delivery_address' => 'nullable|string',
            'notes' => 'nullable|string',
            'customer_id' => 'required|exists:customers,id',
            'customer_company_id' => 'nullable|exists:customer_companies,id',
            'employee_id' => 'nullable|exists:employees,id',
            'invoice_id' => 'nullable|exists:invoices,id',
        ]);

        if (empty($validated['customer_company_id']) && !empty($validated['customer_id'])) {
            $cust = Customer::find($validated['customer_id']);
            $validated['customer_company_id'] = $cust?->defaultCompany()?->id;
        }

        $deliveryNote->update([
            'delivery_date' => $validated['delivery_date'],
            'invoice_id' => $validated['invoice_id'] ?? null,
            'customer_id' => $validated['customer_id'],
            'customer_company_id' => $validated['customer_company_id'] ?? null,
            'show_customer_name' => $request->boolean('show_customer_name', true),
            'employee_id' => $validated['employee_id'],
            'delivery_address' => $validated['delivery_address'],
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('delivery_notes.index')
            ->with('success', 'Delivery Note updated successfully.');
    }

    public function generatePDF(DeliveryNote $deliveryNote)
    {
        $deliveryNote->load(['invoice.items', 'customer', 'customerCompany', 'employee']);
        $setting = ($deliveryNote->shop_id ? \App\Models\Setting::withoutGlobalScopes()->where('shop_id', $deliveryNote->shop_id)->first() : null) ?: current_setting();
        
        if (request()->has('show_customer_name')) {
            $deliveryNote->show_customer_name = filter_var(request('show_customer_name'), FILTER_VALIDATE_BOOLEAN);
        }
        
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

        $billingComp = $invoice->billing_company;

        // Create delivery note from invoice
        $deliveryNote = DeliveryNote::create([
            'delivery_number' => $deliveryNumber,
            'delivery_date' => date('Y-m-d'),
            'invoice_id' => $invoice->id,
            'customer_id' => $invoice->customer_id,
            'customer_company_id' => $invoice->customer_company_id ?: $billingComp?->id,
            'show_customer_name' => $invoice->show_customer_name ?? true,
            'employee_id' => $invoice->employee_id,
            'delivery_address' => $billingComp?->address ?? ($invoice->customer->address ?? null),
            'notes' => 'Created from Invoice ' . $invoice->invoice_number,
            'status' => 'Pending',
        ]);

        return redirect()->route('delivery_notes.show', $deliveryNote)
            ->with('success', 'Delivery Note created successfully from Invoice.');
    }

    public function assignEmployee(Request $request, DeliveryNote $deliveryNote)
    {
        $validated = $request->validate([
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        $empId = $validated['employee_id'] ?: null;
        $deliveryNote->update(['employee_id' => $empId]);

        if ($empId) {
            $emp = \App\Models\Employee::find($empId);
            return back()->with('success', "Delivery Note #{$deliveryNote->delivery_number} assigned to {$emp->name} successfully.");
        }

        return back()->with('success', "Delivery Note #{$deliveryNote->delivery_number} assigned back to Admin successfully.");
    }
}
