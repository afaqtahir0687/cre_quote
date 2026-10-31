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
        $query = Invoice::with('customer', 'customerCompany', 'items', 'employee');

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
            $query->whereDate('invoice_date', $request->date);
        }

        $invoices = $query->latest('id')->paginate(10)->withQueryString();
        $employees = \App\Models\Employee::orderBy('name')->get();

        return view('admin.invoices.index', compact('invoices', 'employees'));
    }

    public function create()
    {
        $customers = Customer::where('is_active', true)->with('companies')->latest()->get();
        $products = Product::all();
        $employees = \App\Models\Employee::orderBy('name')->get();
        $currentEmployee = \App\Models\Employee::where('user_id', auth()->id())->first();
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

        return view('admin.invoices.create', compact('customers', 'products', 'invoiceNumber', 'setting', 'employees', 'currentEmployee'));
    }

    public function store(InvoiceRequest $request)
    {
        DB::transaction(function () use ($request) {
            $data = $request->validated();
            
            $setting = current_setting();
            $invoiceNumber = \App\Helpers\SerialGenerator::generate('invoice', now()->format('Y-m-d'));
            $data['invoice_number'] = $invoiceNumber;

            // Set employee_id from input or authenticated user's employee record
            if (empty($data['employee_id'])) {
                $employee = \App\Models\Employee::where('user_id', auth()->id())->first();
                $data['employee_id'] = $employee ? $employee->id : null;
            }

            // Multi-company fallback
            if (empty($data['customer_company_id']) && !empty($data['customer_id'])) {
                $cust = Customer::find($data['customer_id']);
                $data['customer_company_id'] = $cust?->defaultCompany()?->id;
            }
            
            $data['show_customer_name'] = $request->boolean('show_customer_name', true);
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
        $invoice->load('customer', 'customerCompany', 'items', 'payments', 'employee');
        $employees = \App\Models\Employee::orderBy('name')->get();
        return view('admin.invoices.show', compact('invoice', 'employees'));
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load('items', 'customerCompany', 'employee');
        $customers = Customer::where('is_active', true)->with('companies')->latest()->get();
        $products = Product::all();
        $employees = \App\Models\Employee::orderBy('name')->get();
        $setting = current_setting();
        return view('admin.invoices.edit', compact('invoice', 'customers', 'products', 'setting', 'employees'));
    }

    public function update(InvoiceRequest $request, Invoice $invoice)
    {
        DB::transaction(function () use ($request, $invoice) {
            $data = $request->validated();
            // Multi-company fallback
            if (empty($data['customer_company_id']) && !empty($data['customer_id'])) {
                $cust = Customer::find($data['customer_id']);
                $data['customer_company_id'] = $cust?->defaultCompany()?->id;
            }
            
            $data['show_customer_name'] = $request->boolean('show_customer_name', true);
            $invoice->update($data);
            $invoice->touch();
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
        $invoice->load('customer', 'customerCompany', 'items', 'employee');
        $setting = ($invoice->shop_id ? Setting::withoutGlobalScopes()->where('shop_id', $invoice->shop_id)->first() : null) ?: current_setting();
        
        if (request()->has('show_customer_name')) {
            $invoice->show_customer_name = filter_var(request('show_customer_name'), FILTER_VALIDATE_BOOLEAN);
        }
        
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

    public function assignEmployee(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        $empId = $validated['employee_id'] ?: null;
        $invoice->update(['employee_id' => $empId]);

        if ($empId) {
            $emp = \App\Models\Employee::find($empId);
            return back()->with('success', "Invoice #{$invoice->invoice_number} assigned to {$emp->name} successfully.");
        }

        return back()->with('success', "Invoice #{$invoice->invoice_number} assigned back to Admin successfully.");
    }
}
