<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Payment;
use App\Http\Requests\InvoiceRequest;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = Invoice::with('customer', 'items', 'employee');

        if (auth()->user()->role === 'employee') {
            $employee = \App\Models\Employee::where('user_id', auth()->id())->first();
            if ($employee) {
                $query->where('employee_id', $employee->id);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('invoice_date', $request->date);
        }

        $invoices = $query->latest('id')->paginate(10)->withQueryString();

        return view('admin.invoices.index', compact('invoices'));
    }

    public function create()
    {
        $customers = Customer::where('is_active', true)->latest()->get();
        $products = Product::all();
        $setting = current_setting();
        
        $nextNumber = 1001;
        $latestInvoice = Invoice::latest('id')->first();
        if ($latestInvoice) {
            preg_match('/\d+/', $latestInvoice->invoice_number, $matches);
            $lastNum = !empty($matches) ? (int)$matches[0] : 1000;
            $nextNumber = max(1001, $lastNum + 1);
        }
        
        $invoiceNumber = ($setting->invoice_prefix ?? 'INV-') . $nextNumber;
        while(Invoice::where('invoice_number', $invoiceNumber)->exists()) {
            $nextNumber++;
            $invoiceNumber = ($setting->invoice_prefix ?? 'INV-') . $nextNumber;
        }

        return view('admin.invoices.create', compact('customers', 'products', 'invoiceNumber'));
    }

    public function store(InvoiceRequest $request)
    {
        DB::transaction(function () use ($request) {
            $data = $request->validated();
            
            $setting = current_setting();
            $invoiceNumber = \App\Helpers\SerialGenerator::generate('invoice', now()->format('Y-m-d'));
            $data['invoice_number'] = $invoiceNumber;

            // Set employee_id from authenticated user's employee record
            $employee = \App\Models\Employee::where('user_id', auth()->id())->first();
            $data['employee_id'] = $employee ? $employee->id : null;
            
            $invoice = Invoice::create($data);
            $invoice->update(['balance_due' => $invoice->grand_total]);
            
            foreach ($request->items as $item) {
                $invoice->items()->create($item);
            }
        });

        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('customer', 'items', 'payments', 'employee');
        return view('admin.invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load('items');
        $customers = Customer::where('is_active', true)->latest()->get();
        $products = Product::all();
        return view('admin.invoices.edit', compact('invoice', 'customers', 'products'));
    }

    public function update(InvoiceRequest $request, Invoice $invoice)
    {
        DB::transaction(function () use ($request, $invoice) {
            $data = $request->validated();
            // Keep original creator's employee_id intact
            
            $invoice->update($data);$invoice->touch();
            $invoice->update(['balance_due' => $invoice->grand_total - $invoice->amount_paid]);
            $invoice->items()->delete();
            
            foreach ($request->items as $item) {
                $invoice->items()->create($item);
            }
        });

        return redirect()->route('invoices.index')->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Invoice $invoice)
    {
        $invoiceNumber = $invoice->invoice_number;
        $customerName = $invoice->customer->name ?? 'Unknown';

        $invoice->delete();

        \App\Models\SystemLog::create([
            'user_id' => auth()->id(),
            'action' => 'Deleted',
            'model_type' => 'Invoice',
            'description' => "Deleted Invoice #{$invoiceNumber} (Customer: {$customerName})"
        ]);

        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully.');
    }

    public function downloadPdf(Invoice $invoice)
    {
        $invoice->load('customer', 'items', 'employee');
        $setting = ($invoice->shop_id ? Setting::withoutGlobalScopes()->where('shop_id', $invoice->shop_id)->first() : null) ?: current_setting();
        
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'amiri',
            'autoScriptToLang' => true,
            'autoLangToFont'   => true,
        ]);
        
        $html = view('admin.invoices.pdf', compact('invoice', 'setting'))->render();
        $mpdf->WriteHTML($html);
        
        return response($mpdf->Output('', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $invoice->invoice_number . '.pdf"');
    }
}

