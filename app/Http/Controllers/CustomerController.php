<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Customer;
use App\Http\Requests\CustomerRequest;

class CustomerController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = Customer::latest('id');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $digitsOnly = preg_replace('/[^\d]/', '', $search);
            $cleanDigits = ltrim($digitsOnly, '0');

            $query->where(function($q) use ($search, $cleanDigits) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");

                if (!empty($cleanDigits) && strlen($cleanDigits) >= 4) {
                    $q->orWhere('phone', 'like', "%{$cleanDigits}%");
                }
            });
        }

        $customers = $query->paginate(10)->withQueryString();
        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(CustomerRequest $request)
    {
        Customer::create($request->validated());
        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }

    public function ajaxStore(CustomerRequest $request)
    {
        $customer = Customer::create($request->validated());

        $display = $customer->name;
        if (!empty($customer->company_name)) {
            $display .= ' — ' . $customer->company_name;
        }
        $formattedPhone = $customer->formatted_phone ?: $customer->phone;
        if (!empty($formattedPhone)) {
            $display .= ' (' . $formattedPhone . ')';
        }

        return response()->json([
            'success' => true,
            'customer' => [
                'id'           => $customer->id,
                'name'         => $customer->name,
                'company_name' => $customer->company_name ?? '',
                'phone'        => $formattedPhone ?? '',
                'display'      => $display,
                'text'         => $display,
            ],
        ]);
    }

    public function show(Customer $customer)
    {
        return view('admin.customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(CustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());
        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $name = $customer->name;
        
        $customer->delete();
        
        \App\Models\SystemLog::create([
            'user_id' => auth()->id(),
            'action' => 'Deleted',
            'model_type' => 'Customer',
            'description' => "Deleted Customer: {$name}"
        ]);
        
        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }

    public function statement(Customer $customer)
    {
        $invoices = $customer->invoices()->orderBy('invoice_date', 'asc')->get();
        return view('admin.customers.statement', compact('customer', 'invoices'));
    }

    public function statementPdf(Customer $customer)
    {
        $invoices = $customer->invoices()->orderBy('invoice_date', 'asc')->get();
        $userTz = request()->cookie('user_timezone', config('app.timezone'));
        view()->share('userTz', $userTz);
        
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'amiri',
        ]);

        $setting = ($customer->shop_id ? \App\Models\Setting::withoutGlobalScopes()->where('shop_id', $customer->shop_id)->first() : null) ?: current_setting();
        $html = view('admin.customers.statement_pdf', compact('customer', 'invoices', 'setting'))->render();
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="statement-' . $customer->id . '.pdf"');
    }
}

