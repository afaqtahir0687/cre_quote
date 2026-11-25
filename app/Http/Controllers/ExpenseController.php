<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Shop;
use App\Models\User;
use App\Models\Employee;
use App\Scopes\ShopScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    /**
     * Resolve the allowed shop IDs for the current user.
     * Admin: sees all offices/branches under their business account.
     * Employee: sees strictly their assigned shop/office.
     */
    protected function getAllowedShopIds($user): array
    {
        if ($user->isSuperAdmin()) {
            $sessionShop = session('active_shop_id');
            if (!empty($sessionShop) && is_numeric($sessionShop)) {
                return [(int) $sessionShop];
            }
            return Shop::pluck('id')->all();
        }

        $userShop = $user->shop;
        if (!$userShop) {
            return $user->shop_id ? [$user->shop_id] : [];
        }

        if ($user->isAdmin()) {
            return $userShop->allBranchAndParentShopIds();
        }

        // Employee is strictly restricted to their own assigned office/shop
        return [$user->shop_id];
    }

    /**
     * Display a listing of daily office expenses.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $allowedShopIds = $this->getAllowedShopIds($user);
        $isAdmin = $user->isSuperAdmin() || $user->isAdmin();

        $query = Expense::withoutGlobalScope(ShopScope::class)
            ->whereIn('shop_id', $allowedShopIds)
            ->with(['shop', 'user', 'employee']);

        // Filter: Office / Branch (for Admin)
        if ($isAdmin && $request->filled('shop_id')) {
            $query->where('shop_id', (int) $request->shop_id);
        }

        // Filter: Category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter: Payment Method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter: Staff / User (for Admin)
        if ($isAdmin && $request->filled('user_id')) {
            $query->where('user_id', (int) $request->user_id);
        }

        // Filter: Date Presets & Date Range
        $datePreset = $request->get('date_preset', '');
        $today = Carbon::today();

        if ($datePreset === 'today') {
            $query->whereDate('expense_date', $today);
        } elseif ($datePreset === 'yesterday') {
            $query->whereDate('expense_date', Carbon::yesterday());
        } elseif ($datePreset === 'this_week') {
            $query->whereBetween('expense_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($datePreset === 'this_month') {
            $query->whereBetween('expense_date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
        } else {
            if ($request->filled('start_date')) {
                $query->whereDate('expense_date', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('expense_date', '<=', $request->end_date);
            }
        }

        // Search text
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('reference_no', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Clone for KPI metrics
        $metricsQuery = clone $query;
        $totalAmount = (float) $metricsQuery->sum('amount');
        $totalCount = $metricsQuery->count();

        // Today's total for this scope
        $todayTotal = (float) Expense::withoutGlobalScope(ShopScope::class)
            ->whereIn('shop_id', $allowedShopIds)
            ->whereDate('expense_date', $today)
            ->sum('amount');

        // This month's total for this scope
        $monthTotal = (float) Expense::withoutGlobalScope(ShopScope::class)
            ->whereIn('shop_id', $allowedShopIds)
            ->whereBetween('expense_date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->sum('amount');

        $expenses = $query->latest('expense_date')->latest('id')->paginate(20)->withQueryString();

        // Offices / Branches list for filter dropdown (if Admin)
        $availableOffices = $isAdmin ? Shop::whereIn('id', $allowedShopIds)->get() : collect();

        // Available staff members in these offices (if Admin)
        $availableStaff = $isAdmin ? User::whereIn('shop_id', $allowedShopIds)->orderBy('name')->get() : collect();

        return view('expenses.index', compact(
            'expenses',
            'isAdmin',
            'availableOffices',
            'availableStaff',
            'totalAmount',
            'totalCount',
            'todayTotal',
            'monthTotal'
        ));
    }

    /**
     * Show the form for creating a new daily expense.
     */
    public function create()
    {
        $user = auth()->user();
        $allowedShopIds = $this->getAllowedShopIds($user);
        $isAdmin = $user->isSuperAdmin() || $user->isAdmin();

        // Available offices: for Admin they can pick any office of business; employee is locked to their own office
        $offices = Shop::whereIn('id', $allowedShopIds)->get();
        $defaultShopId = $user->shop_id ?: ($offices->first()->id ?? null);

        // Staff members for the selected office
        $staffMembers = User::whereIn('shop_id', $allowedShopIds)->orderBy('name')->get();

        return view('expenses.create', compact('isAdmin', 'offices', 'defaultShopId', 'staffMembers'));
    }

    /**
     * Store a newly created daily expense in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $allowedShopIds = $this->getAllowedShopIds($user);
        $isAdmin = $user->isSuperAdmin() || $user->isAdmin();

        $validated = $request->validate([
            'shop_id'        => $isAdmin ? 'required|exists:shops,id' : 'nullable',
            'expense_date'   => 'required|date',
            'category'       => 'required|string|max:60',
            'title'          => 'required|string|max:255',
            'amount'         => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|max:30',
            'reference_no'   => 'nullable|string|max:100',
            'attachment'     => 'nullable|file|mimes:jpeg,png,jpg,pdf,webp|max:5120',
            'notes'          => 'nullable|string|max:1000',
        ]);

        // Security: enforce allowed shop ID
        $shopId = $isAdmin && !empty($validated['shop_id']) ? (int) $validated['shop_id'] : $user->shop_id;
        if (!in_array($shopId, $allowedShopIds)) {
            abort(403, __('Unauthorized office / branch selection.'));
        }

        // Handle attachment file upload
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = 'expense_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destDir = public_path('uploads/expenses');
            if (!File::exists($destDir)) {
                File::makeDirectory($destDir, 0755, true);
            }
            $file->move($destDir, $fileName);
            $attachmentPath = 'uploads/expenses/' . $fileName;
        }

        $expense = Expense::withoutGlobalScope(ShopScope::class)->create([
            'shop_id'        => $shopId,
            'user_id'        => $user->id,
            'employee_id'    => $user->employee?->id,
            'expense_date'   => $validated['expense_date'],
            'category'       => $validated['category'],
            'title'          => $validated['title'],
            'amount'         => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'reference_no'   => $validated['reference_no'] ?? null,
            'attachment'     => $attachmentPath,
            'notes'          => $validated['notes'] ?? null,
        ]);

        return redirect()->route('expenses.index')
            ->with('success', __('Daily expense of :amount recorded successfully for :office.', [
                'amount' => format_currency($expense->amount),
                'office' => $expense->shop?->name ?: __('Office'),
            ]));
    }

    /**
     * Show the form for editing the specified daily expense.
     */
    public function edit(Expense $expense)
    {
        $user = auth()->user();
        $allowedShopIds = $this->getAllowedShopIds($user);
        $isAdmin = $user->isSuperAdmin() || $user->isAdmin();

        if (!in_array((int) $expense->shop_id, $allowedShopIds)) {
            abort(403, __('You do not have access to edit this expense.'));
        }

        $offices = Shop::whereIn('id', $allowedShopIds)->get();

        return view('expenses.edit', compact('expense', 'isAdmin', 'offices'));
    }

    /**
     * Update the specified daily expense in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        $user = auth()->user();
        $allowedShopIds = $this->getAllowedShopIds($user);
        $isAdmin = $user->isSuperAdmin() || $user->isAdmin();

        if (!in_array((int) $expense->shop_id, $allowedShopIds)) {
            abort(403, __('You do not have access to update this expense.'));
        }

        $validated = $request->validate([
            'shop_id'        => $isAdmin ? 'required|exists:shops,id' : 'nullable',
            'expense_date'   => 'required|date',
            'category'       => 'required|string|max:60',
            'title'          => 'required|string|max:255',
            'amount'         => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|max:30',
            'reference_no'   => 'nullable|string|max:100',
            'attachment'     => 'nullable|file|mimes:jpeg,png,jpg,pdf,webp|max:5120',
            'notes'          => 'nullable|string|max:1000',
        ]);

        $shopId = $isAdmin && !empty($validated['shop_id']) ? (int) $validated['shop_id'] : $expense->shop_id;
        if (!in_array($shopId, $allowedShopIds)) {
            abort(403, __('Unauthorized office selection.'));
        }

        if ($request->hasFile('attachment')) {
            // Delete old file if exists
            if ($expense->attachment && File::exists(public_path($expense->attachment))) {
                File::delete(public_path($expense->attachment));
            }

            $file = $request->file('attachment');
            $fileName = 'expense_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destDir = public_path('uploads/expenses');
            if (!File::exists($destDir)) {
                File::makeDirectory($destDir, 0755, true);
            }
            $file->move($destDir, $fileName);
            $expense->attachment = 'uploads/expenses/' . $fileName;
        }

        $expense->shop_id        = $shopId;
        $expense->expense_date   = $validated['expense_date'];
        $expense->category       = $validated['category'];
        $expense->title          = $validated['title'];
        $expense->amount         = $validated['amount'];
        $expense->payment_method = $validated['payment_method'];
        $expense->reference_no   = $validated['reference_no'] ?? null;
        $expense->notes          = $validated['notes'] ?? null;
        $expense->save();

        return redirect()->route('expenses.index')
            ->with('success', __('Expense updated successfully.'));
    }

    /**
     * Remove the specified daily expense from storage.
     */
    public function destroy(Expense $expense)
    {
        $user = auth()->user();
        $allowedShopIds = $this->getAllowedShopIds($user);

        if (!in_array((int) $expense->shop_id, $allowedShopIds)) {
            abort(403, __('You do not have access to delete this expense.'));
        }

        if ($expense->attachment && File::exists(public_path($expense->attachment))) {
            File::delete(public_path($expense->attachment));
        }

        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', __('Expense record deleted successfully.'));
    }

    /**
     * Download or view filtered Daily Expenses Report PDF.
     */
    public function exportPdf(Request $request)
    {
        $user = auth()->user();
        $allowedShopIds = $this->getAllowedShopIds($user);
        $isAdmin = $user->isSuperAdmin() || $user->isAdmin();

        $query = Expense::withoutGlobalScope(ShopScope::class)
            ->whereIn('shop_id', $allowedShopIds)
            ->with(['shop.setting', 'user', 'employee']);

        // Filter: Office / Branch (for Admin)
        $selectedOffice = null;
        if ($isAdmin && $request->filled('shop_id')) {
            $query->where('shop_id', (int) $request->shop_id);
            $selectedOffice = Shop::find($request->shop_id);
        } elseif (!$isAdmin) {
            $selectedOffice = $user->shop;
        }

        // Filter: Category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter: Payment Method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter: Staff / User
        if ($isAdmin && $request->filled('user_id')) {
            $query->where('user_id', (int) $request->user_id);
        }

        // Filter: Date Presets & Date Range
        $datePreset = $request->get('date_preset', '');
        $today = Carbon::today();
        $periodLabel = __('All Dates');

        if ($datePreset === 'today') {
            $query->whereDate('expense_date', $today);
            $periodLabel = __('Today') . ' (' . $today->format('d M Y') . ')';
        } elseif ($datePreset === 'yesterday') {
            $query->whereDate('expense_date', Carbon::yesterday());
            $periodLabel = __('Yesterday') . ' (' . Carbon::yesterday()->format('d M Y') . ')';
        } elseif ($datePreset === 'this_week') {
            $start = Carbon::now()->startOfWeek();
            $end = Carbon::now()->endOfWeek();
            $query->whereBetween('expense_date', [$start, $end]);
            $periodLabel = __('This Week') . ' (' . $start->format('d M') . ' - ' . $end->format('d M Y') . ')';
        } elseif ($datePreset === 'this_month') {
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
            $query->whereBetween('expense_date', [$start, $end]);
            $periodLabel = __('This Month') . ' (' . Carbon::now()->format('F Y') . ')';
        } else {
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereDate('expense_date', '>=', $request->start_date)
                      ->whereDate('expense_date', '<=', $request->end_date);
                $periodLabel = $request->start_date . ' ' . __('to') . ' ' . $request->end_date;
            } elseif ($request->filled('start_date')) {
                $query->whereDate('expense_date', '>=', $request->start_date);
                $periodLabel = __('From') . ' ' . $request->start_date;
            } elseif ($request->filled('end_date')) {
                $query->whereDate('expense_date', '<=', $request->end_date);
                $periodLabel = __('Until') . ' ' . $request->end_date;
            }
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('reference_no', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $expenses = $query->orderBy('expense_date', 'asc')->orderBy('id', 'asc')->get();

        $totalAmount = (float) $expenses->sum('amount');
        $totalCount = $expenses->count();

        // Group by category for summary breakdown
        $categorySummary = $expenses->groupBy('category')->map(function ($items, $cat) {
            return [
                'count' => $items->count(),
                'total' => $items->sum('amount'),
                'label' => \App\Models\Expense::categories()[$cat] ?? ucfirst($cat),
            ];
        });

        // Group by office
        $officeSummary = $expenses->groupBy('shop_id')->map(function ($items, $sId) {
            $shop = $items->first()->shop;
            return [
                'name'  => $shop ? $shop->name : __('Office'),
                'count' => $items->count(),
                'total' => $items->sum('amount'),
            ];
        });

        $currentShop = current_shop() ?: ($selectedOffice ?: Shop::first());
        $setting = $currentShop ? $currentShop->setting : null;

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 8,
            'margin_right' => 8,
            'margin_top' => 8,
            'margin_bottom' => 8,
            'default_font' => 'amiri',
            'autoScriptToLang' => true,
            'autoLangToFont'   => true,
        ]);

        if (app()->getLocale() === 'ar') {
            $mpdf->SetDirectionality('rtl');
        }

        $html = view('expenses.pdf_report', compact(
            'expenses',
            'totalAmount',
            'totalCount',
            'categorySummary',
            'officeSummary',
            'selectedOffice',
            'periodLabel',
            'currentShop',
            'setting',
            'isAdmin'
        ))->render();

        $mpdf->WriteHTML($html);

        $filename = 'Expense_Report_' . date('Ymd_His') . '.pdf';
        $disposition = $request->has('download') ? 'attachment' : 'inline';

        return response($mpdf->Output('', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', $disposition . '; filename="' . $filename . '"');
    }

    /**
     * Download / Print Single Expense Payment Voucher PDF.
     */
    public function singlePdf(Expense $expense)
    {
        $user = auth()->user();
        $allowedShopIds = $this->getAllowedShopIds($user);

        if (!in_array((int) $expense->shop_id, $allowedShopIds)) {
            abort(403, __('You do not have access to view this expense voucher.'));
        }

        $expense->load(['shop.setting', 'user', 'employee']);
        $currentShop = $expense->shop ?: current_shop();
        $setting = $currentShop ? $currentShop->setting : null;

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A5',
            'orientation' => 'L',
            'margin_left' => 6,
            'margin_right' => 6,
            'margin_top' => 6,
            'margin_bottom' => 6,
            'default_font' => 'amiri',
            'autoScriptToLang' => true,
            'autoLangToFont'   => true,
        ]);

        if (app()->getLocale() === 'ar') {
            $mpdf->SetDirectionality('rtl');
        }

        $html = view('expenses.pdf_voucher', compact('expense', 'currentShop', 'setting'))->render();
        $mpdf->WriteHTML($html);

        $filename = 'Expense_Voucher_EXP-' . $expense->id . '.pdf';
        $disposition = request()->has('download') ? 'attachment' : 'inline';

        return response($mpdf->Output('', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', $disposition . '; filename="' . $filename . '"');
    }
}
