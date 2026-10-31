<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\CustomerCompany;
use App\Http\Requests\CustomerRequest;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::with('companies')->latest('id');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $digitsOnly = preg_replace('/[^\d]/', '', $search);
            $cleanDigits = ltrim($digitsOnly, '0');

            $query->where(function($q) use ($search, $cleanDigits) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('companies', function($cq) use ($search) {
                      $cq->where('company_name', 'like', "%{$search}%")
                         ->orWhere('vat_no', 'like', "%{$search}%")
                         ->orWhere('cr_no', 'like', "%{$search}%");
                  });

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
        DB::transaction(function () use ($request) {
            $validated = $request->validated();
            $companiesData = $validated['companies'] ?? [];
            $defaultIndex = (int) ($validated['default_company_index'] ?? 0);

            // Primary Customer Info
            $customer = Customer::create([
                'name'      => $validated['name'],
                'email'     => $validated['email'] ?? null,
                'phone'     => $validated['phone'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            // Create companies
            $createdCompanies = [];
            foreach ($companiesData as $idx => $cData) {
                $isDefault = ($idx === $defaultIndex);
                $company = $customer->companies()->create([
                    'company_name' => $cData['company_name'],
                    'phone'        => $cData['phone'] ?? null,
                    'vat_no'       => $cData['vat_no'] ?? null,
                    'cr_no'        => $cData['cr_no'] ?? null,
                    'address'      => $cData['address'] ?? null,
                    'city'         => $cData['city'] ?? null,
                    'zip'          => $cData['zip'] ?? null,
                    'country'      => $cData['country'] ?? 'Saudi Arabia',
                    'is_default'   => $isDefault,
                ]);
                $createdCompanies[$idx] = $company;
            }

            // Identify the default company (or fallback to first)
            $defaultCompany = $createdCompanies[$defaultIndex] ?? ($createdCompanies[0] ?? null);
            if ($defaultCompany) {
                $defaultCompany->update(['is_default' => true]);
                // Mirror to legacy columns on customers table
                $customer->update([
                    'company_name' => $defaultCompany->company_name,
                    'vat_no'       => $defaultCompany->vat_no,
                    'cr_no'        => $defaultCompany->cr_no,
                    'address'      => $defaultCompany->address,
                    'city'         => $defaultCompany->city,
                    'zip'          => $defaultCompany->zip,
                    'country'      => $defaultCompany->country,
                ]);
            }
        });

        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }

    public function ajaxStore(CustomerRequest $request)
    {
        $customer = null;
        DB::transaction(function () use ($request, &$customer) {
            $validated = $request->validated();
            $companiesData = $validated['companies'] ?? [];
            $defaultIndex = (int) ($validated['default_company_index'] ?? 0);

            $customer = Customer::create([
                'name'      => $validated['name'],
                'email'     => $validated['email'] ?? null,
                'phone'     => $validated['phone'],
                'is_active' => true,
            ]);

            $createdCompanies = [];
            foreach ($companiesData as $idx => $cData) {
                $isDefault = ($idx === $defaultIndex);
                $company = $customer->companies()->create([
                    'company_name' => $cData['company_name'],
                    'phone'        => $cData['phone'] ?? null,
                    'vat_no'       => $cData['vat_no'] ?? null,
                    'cr_no'        => $cData['cr_no'] ?? null,
                    'address'      => $cData['address'] ?? null,
                    'city'         => $cData['city'] ?? null,
                    'zip'          => $cData['zip'] ?? null,
                    'country'      => $cData['country'] ?? 'Saudi Arabia',
                    'is_default'   => $isDefault,
                ]);
                $createdCompanies[$idx] = $company;
            }

            $defaultCompany = $createdCompanies[$defaultIndex] ?? ($createdCompanies[0] ?? null);
            if ($defaultCompany) {
                $defaultCompany->update(['is_default' => true]);
                $customer->update([
                    'company_name' => $defaultCompany->company_name,
                    'vat_no'       => $defaultCompany->vat_no,
                    'cr_no'        => $defaultCompany->cr_no,
                    'address'      => $defaultCompany->address,
                    'city'         => $defaultCompany->city,
                    'zip'          => $defaultCompany->zip,
                    'country'      => $defaultCompany->country,
                ]);
            }
        });

        $defaultCompany = $customer->defaultCompany();
        $display = $customer->name;
        if (!empty($defaultCompany?->company_name)) {
            $display .= ' — ' . $defaultCompany->company_name;
        }
        $formattedPhone = $customer->formatted_phone ?: $customer->phone;
        if (!empty($formattedPhone)) {
            $display .= ' (' . $formattedPhone . ')';
        }

        return response()->json([
            'success' => true,
            'customer' => [
                'id'           => $customer->id,
                'company_id'   => $defaultCompany?->id,
                'name'         => $customer->name,
                'company_name' => $defaultCompany?->company_name ?? '',
                'company_phone' => $defaultCompany?->phone ?? '',
                'phone'        => $formattedPhone ?? '',
                'vat_no'       => $defaultCompany?->vat_no ?? '',
                'cr_no'        => $defaultCompany?->cr_no ?? '',
                'address'      => $defaultCompany?->address ?? '',
                'display'      => $display,
                'text'         => $display,
            ],
        ]);
    }

    public function show(Customer $customer)
    {
        $customer->load('companies');
        return view('admin.customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $customer->load('companies');
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(CustomerRequest $request, Customer $customer)
    {
        DB::transaction(function () use ($request, $customer) {
            $validated = $request->validated();
            $companiesData = $validated['companies'] ?? [];
            $defaultIndex = (int) ($validated['default_company_index'] ?? 0);

            // Update primary Customer info
            $customer->update([
                'name'      => $validated['name'],
                'email'     => $validated['email'] ?? null,
                'phone'     => $validated['phone'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            $submittedIds = [];
            $processedCompanies = [];

            foreach ($companiesData as $idx => $cData) {
                $isDefault = ($idx === $defaultIndex);
                $cPayload = [
                    'company_name' => $cData['company_name'],
                    'phone'        => $cData['phone'] ?? null,
                    'vat_no'       => $cData['vat_no'] ?? null,
                    'cr_no'        => $cData['cr_no'] ?? null,
                    'address'      => $cData['address'] ?? null,
                    'city'         => $cData['city'] ?? null,
                    'zip'          => $cData['zip'] ?? null,
                    'country'      => $cData['country'] ?? 'Saudi Arabia',
                    'is_default'   => $isDefault,
                ];

                if (!empty($cData['id'])) {
                    $company = $customer->companies()->where('id', $cData['id'])->first();
                    if ($company) {
                        $company->update($cPayload);
                    } else {
                        $company = $customer->companies()->create($cPayload);
                    }
                } else {
                    $company = $customer->companies()->create($cPayload);
                }

                $submittedIds[] = $company->id;
                $processedCompanies[$idx] = $company;
            }

            // Delete any existing companies belonging to this customer whose IDs are NOT in the submitted array
            $customer->companies()->whereNotIn('id', $submittedIds)->delete();

            // Set default company
            $defaultCompany = $processedCompanies[$defaultIndex] ?? ($processedCompanies[0] ?? $customer->companies()->first());
            if ($defaultCompany) {
                $customer->companies()->where('id', '!=', $defaultCompany->id)->update(['is_default' => false]);
                $defaultCompany->update(['is_default' => true]);

                // Sync the default company's details into the customers table legacy columns
                $customer->update([
                    'company_name' => $defaultCompany->company_name,
                    'vat_no'       => $defaultCompany->vat_no,
                    'cr_no'        => $defaultCompany->cr_no,
                    'address'      => $defaultCompany->address,
                    'city'         => $defaultCompany->city,
                    'zip'          => $defaultCompany->zip,
                    'country'      => $defaultCompany->country,
                ]);
            }
        });

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
        $customer->load('companies');
        $invoices = $customer->invoices()->with('customerCompany')->orderBy('invoice_date', 'asc')->get();
        return view('admin.customers.statement', compact('customer', 'invoices'));
    }

    public function statementPdf(Customer $customer)
    {
        $customer->load('companies');
        $invoices = $customer->invoices()->with('customerCompany')->orderBy('invoice_date', 'asc')->get();
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
