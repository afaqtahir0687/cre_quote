<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Quote;
use App\Models\JobOrder;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Product;
use App\Models\Shop;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // If super admin is in Global View (no specific shop selected in session), redirect to Super Admin Portal
        if ($user->isSuperAdmin() && (!session('active_shop_id') || session('active_shop_id') === 'all')) {
            return redirect()->route('super_admin.dashboard');
        }

        $setting = current_setting();
        $currentShop = current_shop();
        
        if ($user->isSuperAdmin() || $user->isAdmin()) {
            // Discounts across models
            $invoiceDiscount = (float) Invoice::where('status', '!=', 'Cancelled')->sum('total_discount');
            $jobOrderDiscount = (float) JobOrder::whereNull('invoice_id')->where('status', '!=', 'cancelled')->sum('total_discount');
            $quoteDiscount = (float) Quote::where('status', '!=', 'Accepted')->sum('total_discount');
            $totalCustomerDiscount = $invoiceDiscount + $jobOrderDiscount + $quoteDiscount;

            // Job Orders breakdown
            $totalJobOrders = JobOrder::where('status', '!=', 'cancelled')->count();
            $pendingJobOrders = JobOrder::whereIn('status', ['pending', 'in_progress', 'draft'])->count();
            $completedJobOrders = JobOrder::whereIn('status', ['completed', 'delivered'])->count();

            // Quotations breakdown
            $totalQuotes = Quote::count();
            $pendingQuotes = Quote::whereIn('status', ['Pending', 'Sent', 'draft'])->count();
            $acceptedQuotes = Quote::where('status', 'Accepted')->count();

            // Invoices breakdown
            $totalInvoices = Invoice::where('status', '!=', 'Cancelled')->count();
            $paidInvoices = Invoice::where('status', 'Paid')->count();
            $unpaidInvoices = Invoice::whereIn('status', ['Unpaid', 'Partially Paid', 'Sent'])->count();

            $stats = [
                'customers_count' => Customer::count(),
                'invoices_count' => $totalInvoices,
                'paid_invoices_count' => $paidInvoices,
                'unpaid_invoices_count' => $unpaidInvoices,
                'quotes_count' => $totalQuotes,
                'pending_quotes_count' => $pendingQuotes,
                'accepted_quotes_count' => $acceptedQuotes,
                'job_orders_count' => $totalJobOrders,
                'pending_job_orders_count' => $pendingJobOrders,
                'completed_job_orders_count' => $completedJobOrders,
                'employees_count' => Employee::count(),
                'total_paid' => (float) Invoice::where('status', '!=', 'Cancelled')->sum('amount_paid'),
                'total_pending' => (float) Invoice::where('status', '!=', 'Cancelled')->sum('balance_due'),
                'total_discount' => $totalCustomerDiscount,
            ];

            // 6-Month Monthly Analytics for Chart.js
            $monthlyLabels = [];
            $monthlyRevenue = [];
            $monthlyInvoices = [];
            $monthlyJobOrders = [];

            for ($i = 5; $i >= 0; $i--) {
                $monthDate = Carbon::now()->subMonths($i);
                $monthLabel = $monthDate->format('M Y');
                $startOfMonth = $monthDate->copy()->startOfMonth();
                $endOfMonth = $monthDate->copy()->endOfMonth();

                $monthlyLabels[] = $monthLabel;

                // Revenue in month
                $rev = (float) Invoice::where('status', '!=', 'Cancelled')
                    ->whereBetween('invoice_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
                    ->sum('amount_paid');
                $monthlyRevenue[] = round($rev, 2);

                // Invoices count in month
                $invCount = Invoice::where('status', '!=', 'Cancelled')
                    ->whereBetween('invoice_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
                    ->count();
                $monthlyInvoices[] = $invCount;

                // Job orders count in month
                $joCount = JobOrder::where('status', '!=', 'cancelled')
                    ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                    ->count();
                $monthlyJobOrders[] = $joCount;
            }

            // Recent 5 entries per module
            $recentJobOrders = JobOrder::with('customer')->latest('id')->take(5)->get();
            $recentQuotes = Quote::with('customer')->latest('id')->take(5)->get();
            $recentInvoices = Invoice::with('customer')->latest('id')->take(5)->get();
            $recentCustomers = Customer::latest('id')->take(5)->get();
            
            return view('admin.dashboard', compact(
                'stats', 
                'recentInvoices', 
                'recentJobOrders', 
                'recentQuotes', 
                'recentCustomers', 
                'monthlyLabels', 
                'monthlyRevenue', 
                'monthlyInvoices', 
                'monthlyJobOrders', 
                'setting', 
                'currentShop'
            ));
        } else {
            // Employee dashboard - show their own data within their shop
            $employee = \App\Models\Employee::where('user_id', $user->id)->first();
            $employeeId = $employee ? $employee->id : null;
            
            $invoiceDiscount = (float) Invoice::where('status', '!=', 'Cancelled')->sum('total_discount');
            $jobOrderDiscount = (float) JobOrder::whereNull('invoice_id')->where('status', '!=', 'cancelled')->sum('total_discount');
            $quoteDiscount = (float) Quote::where('status', '!=', 'Accepted')->sum('total_discount');
            $totalCustomerDiscount = $invoiceDiscount + $jobOrderDiscount + $quoteDiscount;

            $stats = [
                'quotes_count' => $employeeId ? Quote::where('employee_id', $employeeId)->count() : 0,
                'invoices_count' => $employeeId ? Invoice::where('employee_id', $employeeId)->where('status', '!=', 'Cancelled')->count() : 0,
                'job_orders_count' => $employeeId ? JobOrder::where('employee_id', $employeeId)->where('status', '!=', 'cancelled')->count() : 0,
                'total_discount' => $totalCustomerDiscount,
                'total_quotes_amount' => $employeeId ? Quote::where('employee_id', $employeeId)->sum('grand_total') : 0,
                'total_invoices_amount' => $employeeId ? Invoice::where('employee_id', $employeeId)->where('status', '!=', 'Cancelled')->sum('grand_total') : 0,
            ];
            
            $recentQuotes = Quote::with('customer')->where('employee_id', $employeeId)->latest('id')->take(5)->get();
            $recentJobOrders = JobOrder::with('customer')->where('employee_id', $employeeId)->latest('id')->take(5)->get();
            
            return view('employee.dashboard', compact('stats', 'recentQuotes', 'recentJobOrders', 'setting', 'currentShop'));
        }
    }
}
