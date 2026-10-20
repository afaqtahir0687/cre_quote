<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Invoice;
use App\Models\Quote;
use App\Models\JobOrder;
use App\Models\Customer;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $userRole = auth()->user()->role;
        $setting = \App\Models\Setting::first();
        
        if ($userRole === 'admin') {
            // Total discount given to all customers across the system
            $invoiceDiscount = (float) Invoice::where('status', '!=', 'Cancelled')->sum('total_discount');
            $jobOrderDiscount = (float) JobOrder::whereNull('invoice_id')->where('status', '!=', 'cancelled')->sum('total_discount');
            $quoteDiscount = (float) Quote::where('status', '!=', 'Accepted')->sum('total_discount');
            $totalCustomerDiscount = $invoiceDiscount + $jobOrderDiscount + $quoteDiscount;

            $stats = [
                'customers_count' => Customer::count(),
                'invoices_count' => Invoice::where('status', '!=', 'Cancelled')->count(),
                'quotes_count' => Quote::count(),
                'job_orders_count' => JobOrder::where('status', '!=', 'cancelled')->count(),
                'total_paid' => Invoice::where('status', '!=', 'Cancelled')->sum('amount_paid'),
                'total_pending' => Invoice::where('status', '!=', 'Cancelled')->sum('balance_due'),
                'total_discount' => $totalCustomerDiscount,
            ];
            
            $recentInvoices = Invoice::with('customer')->latest('id')->take(5)->get();
            
            return view('admin.dashboard', compact('stats', 'recentInvoices', 'setting'));
        } else {
            // Employee dashboard - show their own data
            $employee = \App\Models\Employee::where('user_id', auth()->id())->first();
            $employeeId = $employee ? $employee->id : null;
            
            // Total discount given to all customers across the system
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
            
            return view('employee.dashboard', compact('stats', 'recentQuotes', 'recentJobOrders', 'setting'));
        }
    }
}
