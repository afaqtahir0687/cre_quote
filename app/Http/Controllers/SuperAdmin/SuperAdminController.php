<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\User;
use App\Models\Quote;
use App\Models\JobOrder;
use App\Models\Invoice;
use App\Models\Customer;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    /**
     * Super Admin Overview / Platform Dashboard.
     */
    public function index()
    {
        // Platform wide counts without tenant scope
        $totalShops = Shop::count();
        $activeShops = Shop::where('status', 'active')->count();
        $inactiveShops = Shop::where('status', 'inactive')->count();
        $suspendedShops = Shop::where('status', 'suspended')->count();
        $totalUsers = User::count();
        $totalCustomers = Customer::withoutGlobalScopes()->count();
        $totalQuotes = Quote::withoutGlobalScopes()->count();
        $totalJobOrders = JobOrder::withoutGlobalScopes()->count();
        $totalInvoices = Invoice::withoutGlobalScopes()->count();
        $totalRevenue = Invoice::withoutGlobalScopes()->where('status', '!=', 'Cancelled')->sum('amount_paid');
        $totalReceivable = Invoice::withoutGlobalScopes()->where('status', '!=', 'Cancelled')->sum('balance_due');

        // Detailed shops list with statistics
        $shops = Shop::withCount([
            'users',
            'customers' => fn($q) => $q->withoutGlobalScopes(),
            'quotes' => fn($q) => $q->withoutGlobalScopes(),
            'jobOrders' => fn($q) => $q->withoutGlobalScopes(),
            'invoices' => fn($q) => $q->withoutGlobalScopes(),
        ])
        ->with(['setting', 'admins'])
        ->latest('id')
        ->paginate(15);

        // Calculate revenue per shop
        foreach ($shops as $shop) {
            $shop->total_revenue = Invoice::withoutGlobalScopes()
                ->where('shop_id', $shop->id)
                ->where('status', '!=', 'Cancelled')
                ->sum('amount_paid');

            $shop->total_balance_due = Invoice::withoutGlobalScopes()
                ->where('shop_id', $shop->id)
                ->where('status', '!=', 'Cancelled')
                ->sum('balance_due');
        }

        return view('super_admin.dashboard', compact(
            'totalShops',
            'activeShops',
            'inactiveShops',
            'suspendedShops',
            'totalUsers',
            'totalCustomers',
            'totalQuotes',
            'totalJobOrders',
            'totalInvoices',
            'totalRevenue',
            'totalReceivable',
            'shops'
        ));
    }

    /**
     * Send test verification email to Super Admin.
     */
    public function sendTestEmail(Request $request)
    {
        $targetEmail = $request->input('email', auth()->user()->email ?? config('mail.from.address'));

        try {
            $shop = Shop::first();
            \Illuminate\Support\Facades\Mail::send(
                'emails.shop_status_notification',
                [
                    'shop' => $shop ?? new Shop(['name' => 'Demo Sign Advertising']),
                    'status' => 'inactive',
                    'reason' => 'Test Notification - SMTP Verification',
                    'customMessage' => 'Hello! This is a test notification confirming that your Gmail SMTP configuration is fully active and delivering emails properly.',
                    'gracePeriodDays' => 7,
                    'deadline' => now()->addDays(7),
                    'recipientName' => auth()->user()->name ?? 'Administrator',
                    'emailSubject' => 'Quote System - SMTP Test Email',
                ],
                function ($message) use ($targetEmail) {
                    $message->to($targetEmail)
                            ->subject('Quote System - SMTP Test Email');
                }
            );

            return back()->with('success', "Test email successfully delivered to {$targetEmail}! Please check your inbox.");
        } catch (\Throwable $e) {
            return back()->with('error', "Failed to send test email: " . $e->getMessage());
        }
    }

    /**
     * Show Super Admin / Global Platform Branding Settings.
     */
    public function platformSettings()
    {
        $platformSetting = \App\Models\Setting::withoutGlobalScopes()->whereNull('shop_id')->first();
        if (!$platformSetting) {
            $platformSetting = \App\Models\Setting::withoutGlobalScopes()->create([
                'shop_id' => null,
                'company_name' => 'Crelogics Platform',
                'company_email' => 'creelogics@gmail.com',
                'company_phone' => '+966 50 000 0000',
                'company_country' => 'Saudi Arabia',
                'currency_symbol' => '$',
                'invoice_prefix' => 'INV-',
                'quote_prefix' => 'QT-',
                'company_logo' => 'uploads/logo/crelogics-logo.png'
            ]);
        }

        return view('super_admin.platform_settings', compact('platformSetting'));
    }

    /**
     * Update Super Admin / Global Platform Branding Settings.
     */
    public function updatePlatformSettings(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|email|max:255',
            'company_phone' => 'nullable|string|max:50',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
        ]);

        $platformSetting = \App\Models\Setting::withoutGlobalScopes()->whereNull('shop_id')->first();
        if (!$platformSetting) {
            $platformSetting = new \App\Models\Setting();
            $platformSetting->shop_id = null;
        }

        $data = $request->except(['company_logo']);

        if ($request->hasFile('company_logo')) {
            $imageName = \App\Helpers\ImageHelper::compressAndSave($request->company_logo, 'uploads/logo', 80, 500);
            $logoPath = 'uploads/logo/' . $imageName;
            $data['company_logo'] = $logoPath;
        }

        $data['shop_id'] = null;
        $platformSetting->fill($data);
        $platformSetting->save();

        return redirect()->back()->with('success', 'Super Admin platform branding and central logo updated successfully!');
    }
}
