<?php

namespace App\Http\Controllers;

use App\Models\TravelBooking;
use App\Models\Expense;
use App\Models\Shop;
use App\Models\User;
use App\Models\Employee;
use App\Scopes\ShopScope;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TravelDailyReportController extends Controller
{
    /**
     * Resolve allowed shop IDs for the authenticated user.
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

        // Employee is confined to their assigned shop
        return [$user->shop_id];
    }

    /**
     * Gather and aggregate daily bookings, sales, and expenses data.
     */
    protected function buildReportData(Request $request)
    {
        $user = auth()->user();
        $allowedShopIds = $this->getAllowedShopIds($user);
        $isAdmin = $user->isSuperAdmin() || $user->isAdmin();

        // 1. Resolve Date Range
        $datePreset = $request->get('date_preset', '');
        $today = Carbon::today();

        if ($datePreset === 'today' || (empty($datePreset) && !$request->filled('start_date') && !$request->filled('date'))) {
            $startDate = $today->format('Y-m-d');
            $endDate = $startDate;
            $periodLabel = __('Today') . ' (' . $today->format('d M Y') . ')';
        } elseif ($datePreset === 'yesterday') {
            $startDate = Carbon::yesterday()->format('Y-m-d');
            $endDate = $startDate;
            $periodLabel = __('Yesterday') . ' (' . Carbon::yesterday()->format('d M Y') . ')';
        } elseif ($datePreset === 'this_week') {
            $start = Carbon::now()->startOfWeek();
            $end = Carbon::now()->endOfWeek();
            $startDate = $start->format('Y-m-d');
            $endDate = $end->format('Y-m-d');
            $periodLabel = __('This Week') . ' (' . $start->format('d M') . ' - ' . $end->format('d M Y') . ')';
        } elseif ($datePreset === 'this_month') {
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
            $startDate = $start->format('Y-m-d');
            $endDate = $end->format('Y-m-d');
            $periodLabel = __('This Month') . ' (' . Carbon::now()->format('F Y') . ')';
        } else {
            if ($request->filled('date')) {
                $startDate = $request->date;
                $endDate = $request->date;
                $periodLabel = Carbon::parse($startDate)->format('d M Y');
            } elseif ($request->filled('start_date') && $request->filled('end_date')) {
                $startDate = $request->start_date;
                $endDate = $request->end_date;
                $periodLabel = Carbon::parse($startDate)->format('d M Y') . ' ' . __('to') . ' ' . Carbon::parse($endDate)->format('d M Y');
            } elseif ($request->filled('start_date')) {
                $startDate = $request->start_date;
                $endDate = $today->format('Y-m-d');
                $periodLabel = __('From') . ' ' . Carbon::parse($startDate)->format('d M Y');
            } else {
                $startDate = $today->format('Y-m-d');
                $endDate = $startDate;
                $periodLabel = __('Today') . ' (' . $today->format('d M Y') . ')';
            }
        }

        // 2. Query Bookings
        $bookingsQuery = TravelBooking::withoutGlobalScope(ShopScope::class)
            ->whereIn('shop_id', $allowedShopIds)
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('booking_status', '!=', 'cancelled')
            ->with(['trip', 'customer', 'creator', 'employee', 'shop']);

        // 3. Query Expenses
        $expensesQuery = Expense::withoutGlobalScope(ShopScope::class)
            ->whereIn('shop_id', $allowedShopIds)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->with(['shop', 'user', 'employee']);

        // Filter: Specific Office / Branch
        $selectedOffice = null;
        if ($isAdmin && $request->filled('shop_id')) {
            $shopId = (int) $request->shop_id;
            $bookingsQuery->where('shop_id', $shopId);
            $expensesQuery->where('shop_id', $shopId);
            $selectedOffice = Shop::find($shopId);
        } elseif (!$isAdmin) {
            $selectedOffice = $user->shop;
        }

        // Filter: Specific Employee / User
        if ($isAdmin && $request->filled('user_id')) {
            $userId = (int) $request->user_id;
            $filterUser = User::find($userId);
            $filterEmp = $filterUser?->employee;
            $userName = $filterUser ? $filterUser->name : '';

            $bookingsQuery->where(function ($q) use ($userId, $filterEmp, $userName) {
                $q->where('created_by', $userId)
                  ->orWhere('salesman_name', $userName);
                if ($filterEmp) {
                    $q->orWhere('employee_id', $filterEmp->id);
                }
            });

            $expensesQuery->where('user_id', $userId);
        } elseif (!$isAdmin) {
            // Employee sees strictly their own bookings & office expenses
            $currentEmp = $user->employee;
            $currentName = trim($user->name);

            $bookingsQuery->where(function ($q) use ($user, $currentEmp, $currentName) {
                $q->where('created_by', $user->id)
                  ->orWhere('salesman_name', $currentName);
                if ($currentEmp) {
                    $q->orWhere('employee_id', $currentEmp->id);
                }
            });

            $expensesQuery->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('shop_id', $user->shop_id);
            });
        }

        $bookings = $bookingsQuery->latest('created_at')->get();
        $expenses = $expensesQuery->latest('expense_date')->latest('id')->get();

        // 4. Per-Staff Aggregation Matrix
        $staffStats = [];

        foreach ($bookings as $b) {
            $key = null;
            $name = null;
            $designation = null;
            $officeName = $b->shop ? $b->shop->name : __('Office');

            if ($b->employee) {
                $key = 'emp_' . $b->employee->id;
                $name = $b->employee->name;
                $designation = $b->employee->designation;
            } elseif ($b->creator) {
                $key = 'user_' . $b->creator->id;
                $name = $b->creator->name;
                $designation = $b->creator->role ? ucfirst($b->creator->role) : __('Staff');
            } elseif (!empty($b->salesman_name)) {
                $key = 'name_' . md5($b->salesman_name);
                $name = $b->salesman_name;
                $designation = __('Booking Agent');
            } else {
                $key = 'direct_online';
                $name = __('Direct / Front Desk');
                $designation = __('System');
            }

            if (!isset($staffStats[$key])) {
                $staffStats[$key] = [
                    'key'            => $key,
                    'name'           => $name,
                    'designation'    => $designation,
                    'office'         => $officeName,
                    'seats_count'    => 0,
                    'booking_refs'   => [],
                    'gross_sales'    => 0.0,
                    'cash_collected' => 0.0,
                    'balance_due'    => 0.0,
                    'expenses'       => 0.0,
                    'expenses_count' => 0,
                    'net_cash'       => 0.0,
                ];
            }

            $staffStats[$key]['seats_count'] += 1;
            $staffStats[$key]['booking_refs'][$b->booking_reference] = true;
            $staffStats[$key]['gross_sales'] += (float) $b->total_amount;
            $staffStats[$key]['cash_collected'] += (float) $b->paid_amount;
            $staffStats[$key]['balance_due'] += (float) $b->balance_due;
        }

        foreach ($expenses as $exp) {
            $key = null;
            $name = null;
            $designation = null;
            $officeName = $exp->shop ? $exp->shop->name : __('Office');

            if ($exp->employee) {
                $key = 'emp_' . $exp->employee->id;
                $name = $exp->employee->name;
                $designation = $exp->employee->designation;
            } elseif ($exp->user) {
                $key = 'user_' . $exp->user->id;
                $name = $exp->user->name;
                $designation = $exp->user->role ? ucfirst($exp->user->role) : __('Staff');
            } else {
                $key = 'office_' . $exp->shop_id;
                $name = $officeName . ' ' . __('Office');
                $designation = __('Branch Expense');
            }

            if (!isset($staffStats[$key])) {
                $staffStats[$key] = [
                    'key'            => $key,
                    'name'           => $name,
                    'designation'    => $designation,
                    'office'         => $officeName,
                    'seats_count'    => 0,
                    'booking_refs'   => [],
                    'gross_sales'    => 0.0,
                    'cash_collected' => 0.0,
                    'balance_due'    => 0.0,
                    'expenses'       => 0.0,
                    'expenses_count' => 0,
                    'net_cash'       => 0.0,
                ];
            }

            $staffStats[$key]['expenses'] += (float) $exp->amount;
            $staffStats[$key]['expenses_count'] += 1;
        }

        // Finalize calculations
        foreach ($staffStats as &$st) {
            $st['bookings_count'] = count($st['booking_refs']);
            $st['net_cash'] = $st['cash_collected'] - $st['expenses'];
        }
        unset($st);

        // Sort by seats booked descending
        uasort($staffStats, fn($a, $b) => $b['seats_count'] <=> $a['seats_count']);

        // 5. Grand Totals
        $grandSeats = $bookings->count();
        $grandSales = (float) $bookings->sum('total_amount');
        $grandCashIn = (float) $bookings->sum('paid_amount');
        $grandPendingDue = (float) $bookings->sum('balance_due');
        $grandExpenses = (float) $expenses->sum('amount');
        $grandNetCash = $grandCashIn - $grandExpenses;

        // Available offices and staff for filter dropdowns
        $availableOffices = $isAdmin ? Shop::whereIn('id', $allowedShopIds)->get() : collect();
        $availableStaff = $isAdmin ? User::whereIn('shop_id', $allowedShopIds)->orderBy('name')->get() : collect();

        $currentShop = current_shop() ?: ($selectedOffice ?: Shop::first());
        $setting = $currentShop ? $currentShop->setting : null;
        $reportType = $request->get('type', 'full');

        return compact(
            'bookings',
            'expenses',
            'staffStats',
            'grandSeats',
            'grandSales',
            'grandCashIn',
            'grandPendingDue',
            'grandExpenses',
            'grandNetCash',
            'startDate',
            'endDate',
            'periodLabel',
            'selectedOffice',
            'availableOffices',
            'availableStaff',
            'currentShop',
            'setting',
            'isAdmin',
            'reportType'
        );
    }

    /**
     * Display the Daily Closing & Staff Sales Web Dashboard.
     */
    public function index(Request $request)
    {
        $data = $this->buildReportData($request);
        return view('travel.reports.daily_report', $data);
    }

    /**
     * Download or view the official Daily Staff Sales & Expenses Statement PDF.
     */
    public function downloadPdf(Request $request)
    {
        $data = $this->buildReportData($request);

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_top' => 5,
            'margin_bottom' => 5,
            'default_font' => 'amiri',
            'autoScriptToLang' => true,
            'autoLangToFont'   => true,
        ]);

        if (app()->getLocale() === 'ar') {
            $mpdf->SetDirectionality('rtl');
        }

        $html = view('travel.reports.daily_report_pdf', $data)->render();
        $mpdf->WriteHTML($html);

        $prefix = ($data['reportType'] === 'staff_summary') ? 'Staff_Performance_Cash_Summary_' : 'Daily_Closing_Statement_';
        $filename = $prefix . $data['startDate'] . '_' . date('His') . '.pdf';
        $disposition = $request->has('download') ? 'attachment' : 'inline';

        return response($mpdf->Output('', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', $disposition . '; filename="' . $filename . '"');
    }
}
