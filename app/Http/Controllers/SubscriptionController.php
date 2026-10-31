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
     * Show the subscription expired screen.
     */
    public function expired()
    {
        $shop = auth()->user()->shop;
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

        $shop = auth()->user()->shop;
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

        return back()->with('success', 'Your Cash By Hand renewal request has been sent to Super Admin. Once verified, your account will be activated.');
    }

    /**
     * Shop Admin view for active subscription details and packages.
     */
    public function mySubscription()
    {
        $shop = auth()->user()->shop;
        if (!$shop) {
            abort(403, 'No shop assigned.');
        }

        $packages = Package::where('is_active', true)->orderBy('sort_order')->get();
        $subscriptions = $shop->subscriptions()->with(['package', 'recordedBy'])->take(10)->get();
        $setting = \App\Models\Setting::first();

        return view('admin.subscription.index', compact('shop', 'packages', 'subscriptions', 'setting'));
    }
}
