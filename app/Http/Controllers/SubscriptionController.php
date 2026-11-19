<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Shop;
use App\Models\Subscription;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Resolve the active shop model safely for both Admin and Super Admin.
     */
    protected function resolveShop($user): ?Shop
    {
        // 1. Session active shop (if selected via switcher)
        $sessionShop = current_shop();
        if ($sessionShop) {
            return $sessionShop;
        }

        // 2. User's explicitly assigned shop
        if ($user->shop) {
            return $user->shop;
        }

        // 3. Fallback: First available shop on platform
        return Shop::first();
    }

    /**
     * Show the subscription expired screen.
     */
    public function expired()
    {
        $user = auth()->user();
        $shop = $this->resolveShop($user);

        if (!$shop) {
            return redirect()->route('dashboard');
        }

        // If subscription is actually active, send to dashboard
        if ($shop->hasActiveSubscription()) {
            return redirect()->route('dashboard');
        }

        $packages = Package::where('is_active', true)->orderBy('sort_order')->get();
        $setting = \App\Models\Setting::first();

        return view('subscription.expired', compact('shop', 'packages', 'setting'));
    }

    /**
     * Shop admin submits an offline cash payment / renewal request.
     */
    public function requestRenewal(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = auth()->user();
        $shop = $this->resolveShop($user);

        if (!$shop) {
            return back()->with('error', __('No active shop found to request renewal for.'));
        }

        $package = Package::findOrFail($request->package_id);

        // Record a pending subscription record for super admin approval
        Subscription::create([
            'shop_id' => $shop->id,
            'package_id' => $package->id,
            'user_id' => auth()->id(),
            'payment_method' => 'cash_by_hand',
            'payment_status' => 'pending_verification',
            'amount_paid' => $package->price,
            'starts_at' => now(),
            'expires_at' => now()->addDays($package->duration_days),
            'status' => 'pending',
            'payment_notes' => 'Cash By Hand Request from ' . auth()->user()->name . ($request->notes ? ': ' . $request->notes : ''),
        ]);

        // Log in system logs
        SystemLog::create([
            'shop_id' => $shop->id,
            'user_id' => auth()->id(),
            'action' => 'SUBSCRIPTION_RENEWAL_REQUESTED',
            'model_type' => 'Subscription',
            'description' => "Shop '{$shop->name}' requested renewal for {$package->name} via Cash By Hand.",
        ]);

        return back()->with('success', __('Your Cash By Hand renewal request has been sent to Super Admin. Once verified, your account will be activated.'));
    }

    /**
     * Shop Admin view for active subscription details and packages.
     */
    public function mySubscription()
    {
        $user = auth()->user();
        $shop = $this->resolveShop($user);

        if (!$shop) {
            if ($user->isSuperAdmin()) {
                return redirect()->route('super_admin.subscriptions.index')->with('info', __('Please create or select a shop first.'));
            }
            abort(403, __('No shop assigned.'));
        }

        $packages = Package::where('is_active', true)->orderBy('sort_order')->get();
        $subscriptions = $shop->subscriptions()->with(['package', 'recordedBy'])->take(10)->get();
        $setting = \App\Models\Setting::first();

        return view('admin.subscription.index', compact('shop', 'packages', 'subscriptions', 'setting'));
    }
}
