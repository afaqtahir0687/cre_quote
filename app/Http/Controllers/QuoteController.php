<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Http\Requests\QuoteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class QuoteController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = Quote::with('customer', 'customerCompany', 'items', 'employee');

        if (auth()->user()->role === 'employee') {
            $employee = \App\Models\Employee::where('user_id', auth()->id())->first();
            if ($employee) {
                $query->where('employee_id', $employee->id);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('quote_number', 'like', "%{$search}%")
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
            $query->whereDate('quote_date', $request->date);
        }

        $quotes = $query->latest('id')->paginate(10)->withQueryString();
        $employees = \App\Models\Employee::orderBy('name')->get();

        return view('admin.quotes.index', compact('quotes', 'employees'));
    }

    public function create()
    {
        $customers = Customer::where('is_active', true)->with('companies')->latest()->get();
        $products = Product::all();
        $employees = \App\Models\Employee::orderBy('name')->get();
        $currentEmployee = \App\Models\Employee::where('user_id', auth()->id())->first();
        $setting = current_setting();

        $lastQuote = Quote::latest()->first();
        $nextNumber = 1001;
        if ($lastQuote && preg_match('/(\d+)$/', $lastQuote->quote_number, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        }
        $quoteNumber = ($setting->quote_prefix ?? 'QT-') . $nextNumber;

        return view('admin.quotes.create', compact('customers', 'products', 'quoteNumber', 'setting', 'employees', 'currentEmployee'));
    }

    public function store(QuoteRequest $request)
    {
        DB::transaction(function () use ($request) {
            $data = $request->validated();
            
            $setting = current_setting();
            $lastQuote = Quote::latest('id')->first();
            $nextNumber = 1001;
            if ($lastQuote && preg_match('/(\d+)$/', $lastQuote->quote_number, $matches)) {
                $nextNumber = (int)$matches[1] + 1;
            }
            $quoteNumber = ($setting->quote_prefix ?? 'QT-') . $nextNumber;
            while (Quote::where('quote_number', $quoteNumber)->exists()) {
                $nextNumber++;
                $quoteNumber = ($setting->quote_prefix ?? 'QT-') . $nextNumber;
            }
            $data['quote_number'] = $quoteNumber;

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

            // Ensure each item has a tax_percentage, default to 15%
            $totalTax = 0;
            foreach ($data['items'] as &$item) {
                if (!isset($item['tax_percentage']) || $item['tax_percentage'] === null) {
                    $item['tax_percentage'] = 15;
                }
                // Calculate tax amount for this item
                $itemTax = ($item['unit_price'] * $item['quantity'] * $item['tax_percentage']) / 100;
                $totalTax += $itemTax;
            }
            unset($item); // break reference
            $data['total_tax'] = $totalTax;

            // Handle per-item image uploads
            foreach ($data['items'] as $idx => &$item) {
                if ($request->hasFile("items.$idx.image")) {
                    $file = $request->file("items.$idx.image");
                    $filename = \App\Helpers\ImageHelper::compressAndSave($file, 'uploads/quotes');
                    $item['images'] = ['uploads/quotes/' . $filename];
                } else {
                    $item['images'] = null;
                }
            }
            unset($item);

            $data['show_customer_name'] = $request->boolean('show_customer_name', true);

            $quote = Quote::create($data);

            foreach ($data['items'] as $item) {
                $quote->items()->create($item);
            }
        });

        return redirect()->route('quotes.index')->with('success', 'Quote created successfully.');
    }

    public function show(Quote $quote)
    {
        $quote->load('customer', 'customerCompany', 'items', 'employee');
        $departments = \App\Models\Department::where('is_active', true)->get();
        $employees = \App\Models\Employee::all();
        return view('admin.quotes.show', compact('quote', 'departments', 'employees'));
    }

    public function edit(Quote $quote)
    {
        $quote->load('items', 'customerCompany', 'employee');
        $customers = Customer::where('is_active', true)->with('companies')->latest()->get();
        $products = Product::all();
        $employees = \App\Models\Employee::orderBy('name')->get();
        $setting = current_setting();
        return view('admin.quotes.edit', compact('quote', 'customers', 'products', 'setting', 'employees'));
    }

    public function update(QuoteRequest $request, Quote $quote)
    {
        DB::transaction(function () use ($request, $quote) {
            $data = $request->validated();

            // Multi-company fallback
            if (empty($data['customer_company_id']) && !empty($data['customer_id'])) {
                $cust = Customer::find($data['customer_id']);
                $data['customer_company_id'] = $cust?->defaultCompany()?->id;
            }

            // Handle per-item image uploads, merges with existing images, and process removals
            foreach ($data['items'] as $idx => &$item) {
                // Decode hidden fields
                $existingImages = json_decode($item['existing_images'] ?? '[]', true);
                $removedImages = json_decode($item['removed_images'] ?? '[]', true);

                // Remove images that user marked for deletion
                if (!empty($removedImages)) {
                    foreach ($removedImages as $old) {
                        $oldPath = public_path($old);
                        if (file_exists($oldPath)) {
                            unlink($oldPath);
                        }
                        // Also remove from existing images array
                        $existingImages = array_diff($existingImages, [$old]);
                    }
                }

                // Handle new image upload
                if ($request->hasFile("items.$idx.image")) {
                    $file = $request->file("items.$idx.image");
                    $filename = \App\Helpers\ImageHelper::compressAndSave($file, 'uploads/quotes');

                    // Append the new image to remaining existing images
                    $existingImages[] = 'uploads/quotes/' . $filename;
                    $item['images'] = array_values($existingImages);
                } else {
                    // Keep remaining existing images (if any)
                    $item['images'] = !empty($existingImages) ? array_values($existingImages) : null;
                }
            }
            unset($item);

            $data['show_customer_name'] = $request->boolean('show_customer_name', true);
            $quote->update($data);
            $quote->touch();
            $quote->items()->delete();

            foreach ($data['items'] as $item) {
                $quote->items()->create($item);
            }
        });

        return redirect()->route('quotes.index')->with('success', 'Quote updated successfully.');
    }

    public function destroy(Quote $quote)
    {
        $quoteNumber = $quote->quote_number;
        $customerName = $quote->customer->name ?? 'Unknown';

        $quote->delete();

        \App\Models\SystemLog::create([
            'user_id' => auth()->id(),
            'action' => 'Deleted',
            'model_type' => 'Quote',
            'description' => "Deleted Quote #{$quoteNumber} (Customer: {$customerName})"
        ]);

        return redirect()->route('quotes.index')->with('success', 'Quote deleted successfully.');
    }

    public function convertToInvoice(Quote $quote)
    {
        $setting = ($quote->shop_id ? Setting::withoutGlobalScopes()->where('shop_id', $quote->shop_id)->first() : null) ?: current_setting();
        $lastInvoice = Invoice::latest()->first();
        $nextNumber = 1001;
        if ($lastInvoice && preg_match('/(\d+)$/', $lastInvoice->invoice_number, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        }
        $invoiceNumber = ($setting->invoice_prefix ?? 'INV-') . $nextNumber;

        DB::transaction(function () use ($quote, $invoiceNumber) {
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'invoice_date' => now(),
                'due_date' => now()->addDays(7),
                'customer_id' => $quote->customer_id,
                'customer_company_id' => $quote->customer_company_id ?: $quote->customer?->defaultCompany()?->id,
                'subtotal' => $quote->subtotal,
                'total_tax' => $quote->total_tax,
                'total_discount' => $quote->total_discount,
                'shipping' => $quote->shipping,
                'grand_total' => $quote->grand_total,
                'balance_due' => $quote->grand_total,
                'show_customer_name' => $quote->show_customer_name ?? true,
                'status' => 'Unpaid',
                'notes' => $quote->notes,
            ]);

            foreach ($quote->items as $item) {
                $invoice->items()->create([
                    'description' => $item->description,
                    'unit_price' => $item->unit_price,
                    'quantity' => $item->quantity,
                    'tax_percentage' => $item->tax_percentage,
                    'discount' => $item->discount,
                    'total' => $item->total,
                ]);
            }

            $quote->update(['status' => 'Invoiced']);
        });

        return redirect()->route('invoices.index')->with('success', 'Quote converted to Invoice successfully.');
    }

    public function downloadPdf(Quote $quote)
    {
        $quote->load('customer', 'customerCompany', 'items', 'employee');
        $setting = ($quote->shop_id ? Setting::withoutGlobalScopes()->where('shop_id', $quote->shop_id)->first() : null) ?: current_setting();

        if (request()->has('show_customer_name')) {
            $quote->show_customer_name = filter_var(request('show_customer_name'), FILTER_VALIDATE_BOOLEAN);
        }

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'amiri',
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
        ]);

        $html = view('admin.quotes.pdf', compact('quote', 'setting'))->render();
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $quote->quote_number . '.pdf"');
    }

    public function assignEmployee(Request $request, Quote $quote)
    {
        $validated = $request->validate([
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        $empId = $validated['employee_id'] ?: null;
        $quote->update(['employee_id' => $empId]);

        if ($empId) {
            $emp = \App\Models\Employee::find($empId);
            return back()->with('success', "Quote #{$quote->quote_number} assigned to {$emp->name} successfully.");
        }

        return back()->with('success', "Quote #{$quote->quote_number} assigned back to Admin successfully.");
    }
}
