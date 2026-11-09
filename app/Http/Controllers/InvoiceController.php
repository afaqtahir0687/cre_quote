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
        $products = Product::with(['category', 'unit'])->orderBy('name')->get();
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

        $selectedProduct = request()->filled('product_id') ? Product::find(request('product_id')) : null;

        return view('admin.invoices.create', compact('customers', 'products', 'invoiceNumber', 'setting', 'employees', 'currentEmployee', 'selectedProduct'));
    }

    public function store(InvoiceRequest $request)
    {
        $invoice = null;

        DB::transaction(function () use ($request, &$invoice) {
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
            // Balance will be calculated with payments below
            
            foreach ($request->items as $item) {
                $invoice->items()->create($item);

                if (!empty($item['product_id'])) {
                    $product = Product::find($item['product_id']);
                    if ($product && $product->type === 'product') {
                        $product->decrement('stock', (int)$item['quantity']);
                    }
                }
            }

            // Immediate payment handling upon sale
            $paidAmount = (float) $request->input('amount_paid', 0);
            if ($paidAmount > 0) {
                $paidAmount = min((float)$invoice->grand_total, $paidAmount);
                $invoice->payments()->create([
                    'payment_date' => $invoice->invoice_date,
                    'amount' => $paidAmount,
                    'payment_method' => $request->input('payment_method', 'Cash'),
                    'transaction_id' => $request->input('payment_reference'),
                    'note' => $request->input('payment_note') ?: 'Payment received on sale',
                ]);
                $balanceDue = max(0, (float)$invoice->grand_total - $paidAmount);
                $invoice->update([
                    'amount_paid' => $paidAmount,
                    'balance_due' => $balanceDue,
                    'status' => ($balanceDue <= 0 && $invoice->grand_total > 0) ? 'Paid' : 'Partially Paid',
                ]);
            } else {
                $invoice->update([
                    'amount_paid' => 0,
                    'balance_due' => $invoice->grand_total,
                    'status' => 'Unpaid',
                ]);
            }
        });

        return redirect()->route('invoices.show', $invoice->id)->with('success', __('Invoice created successfully.'));
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
        $products = Product::with(['category', 'unit'])->orderBy('name')->get();
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

            // Revert previously deducted stock before recreating items
            foreach ($invoice->items as $oldItem) {
                if (!empty($oldItem->product_id)) {
                    $oldProd = Product::find($oldItem->product_id);
                    if ($oldProd && $oldProd->type === 'product') {
                        $oldProd->increment('stock', (int)$oldItem->quantity);
                    }
                }
            }
            $invoice->items()->delete();
            
            foreach ($request->items as $item) {
                $invoice->items()->create($item);

                if (!empty($item['product_id'])) {
                    $product = Product::find($item['product_id']);
                    if ($product && $product->type === 'product') {
                        $product->decrement('stock', (int)$item['quantity']);
                    }
                }
            }

            // Immediate payment handling upon sale
            $paidAmount = (float) $request->input('amount_paid', 0);
            if ($paidAmount > 0) {
                $paidAmount = min((float)$invoice->grand_total, $paidAmount);
                $invoice->payments()->create([
                    'payment_date' => $invoice->invoice_date,
                    'amount' => $paidAmount,
                    'payment_method' => $request->input('payment_method', 'Cash'),
                    'transaction_id' => $request->input('payment_reference'),
                    'note' => $request->input('payment_note') ?: 'Payment received on sale',
                ]);
                $balanceDue = max(0, (float)$invoice->grand_total - $paidAmount);
                $invoice->update([
                    'amount_paid' => $paidAmount,
                    'balance_due' => $balanceDue,
                    'status' => ($balanceDue <= 0 && $invoice->grand_total > 0) ? 'Paid' : 'Partially Paid',
                ]);
            } else {
                $invoice->update([
                    'amount_paid' => 0,
                    'balance_due' => $invoice->grand_total,
                    'status' => 'Unpaid',
                ]);
            }
        });

        return redirect()->route('invoices.index')->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Invoice $invoice)
    {
        $invoiceNumber = $invoice->invoice_number;
        $customerName = $invoice->customer->name ?? 'Unknown';

        // Revert product inventory
        foreach ($invoice->items as $item) {
            if (!empty($item->product_id)) {
                $product = Product::find($item->product_id);
                if ($product && $product->type === 'product') {
                    $product->increment('stock', (int)$item->quantity);
                }
            }
        }

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
        if (request()->filled('lang')) {
            app()->setLocale(request('lang'));
        }
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
        if (app()->getLocale() === 'ar') {
            $mpdf->SetDirectionality('rtl');
        }
        
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
