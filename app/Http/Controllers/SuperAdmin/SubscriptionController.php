<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Shop;
use App\Models\Subscription;
use App\Models\SystemLog;
use App\Services\SubscriptionNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    /**
     * Display all subscriptions, expiring shops, and cash logs.
     */
    public function index(Request $request)
    {
        $packages = Package::orderBy('sort_order')->get();

        $query = Shop::with(['package', 'activeSubscription.package', 'activeSubscription.recordedBy', 'admins']);

        // Filter by package
        if ($request->filled('package_id')) {
            $query->where('package_id', $request->package_id);
        }

        // Filter by subscription status
        if ($request->filled('filter')) {
            $filter = $request->filter;
            if ($filter === 'expired') {
                $query->where(function ($q) {
                    $q->where('subscription_status', 'expired')
                      ->orWhere('subscription_expires_at', '<', now());
                });
            } elseif ($filter === 'trial') {
                $query->whereHas('package', fn($q) => $q->where('slug', 'trial'));
            } elseif ($filter === 'active') {
                $query->where('subscription_status', 'active')
                      ->where('subscription_expires_at', '>=', now());
            } elseif ($filter === 'business') {
                $query->whereHas('package', fn($q) => $q->where('slug', 'business'));
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $shops = $query->latest('id')->paginate(15)->withQueryString();

        // Calculate stats
        $totalShops = Shop::count();
        $activeCount = Shop::where('subscription_status', 'active')->where('subscription_expires_at', '>=', now())->count();
        $expiredCount = Shop::where('subscription_expires_at', '<', now())->orWhere('subscription_status', 'expired')->count();
        $trialCount = Shop::whereHas('package', fn($q) => $q->where('slug', 'trial'))->count();
        $totalCashCollected = Subscription::where('payment_status', 'paid')->sum('amount_paid');

        // Pending approval cash requests
        $pendingRequests = Subscription::with(['shop', 'package', 'user'])
            ->where('status', 'pending')
            ->latest('id')
            ->get();

        // Recent cash transactions
        $recentTransactions = Subscription::with(['shop', 'package', 'recordedBy'])
            ->where('payment_status', 'paid')
            ->latest('id')
            ->take(10)
            ->get();

        return view('super_admin.subscriptions.index', compact(
            'shops',
            'packages',
            'totalShops',
            'activeCount',
            'expiredCount',
            'trialCount',
            'totalCashCollected',
            'pendingRequests',
            'recentTransactions'
        ));
    }

    /**
     * Assign or renew package for a shop via Cash By Hand.
     */
    public function assignPackage(Request $request, Shop $shop)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'duration_days' => 'nullable|integer|min:1|max:3650',
            'amount_paid' => 'required|numeric|min:0',
            'payment_notes' => 'nullable|string|max:500',
            'extend_from' => 'nullable|in:today,current_expiry',
            'shop_status' => 'nullable|in:active,inactive,suspended',
        ]);

        $package = Package::findOrFail($request->package_id);
        $durationDays = $request->filled('duration_days') && $request->duration_days > 0
            ? (int) $request->duration_days
            : $package->duration_days;

        // Determine start and expiry date
        $now = now();
        $startsAt = $now;

        if ($request->extend_from === 'current_expiry' && $shop->subscription_expires_at && $shop->subscription_expires_at->isFuture()) {
            $startsAt = $shop->subscription_expires_at;
            $expiresAt = $shop->subscription_expires_at->copy()->addDays($durationDays);
        } else {
            $expiresAt = $now->copy()->addDays($durationDays);
        }

        $targetStatus = $request->input('shop_status', 'active');

        DB::transaction(function () use ($shop, $package, $request, $startsAt, $expiresAt, $durationDays, $targetStatus) {
            // 1. Mark existing active subscriptions as renewed
            Subscription::where('shop_id', $shop->id)
                ->where('status', 'active')
                ->update(['status' => 'renewed']);

            // 2. Create new subscription record
            Subscription::create([
                'shop_id' => $shop->id,
                'package_id' => $package->id,
                'user_id' => $shop->admins->first()?->id ?? auth()->id(),
                'payment_method' => 'cash_by_hand',
                'payment_status' => 'paid',
                'amount_paid' => $request->amount_paid,
                'starts_at' => $startsAt,
                'expires_at' => $expiresAt,
                'status' => 'active',
                'payment_notes' => $request->payment_notes ?: "Cash received by Super Admin (" . auth()->user()->name . ")",
                'recorded_by_id' => auth()->id(),
            ]);

            // 3. Update shop
            $shop->update([
                'package_id' => $package->id,
                'subscription_starts_at' => ($request->extend_from === 'current_expiry' && $shop->subscription_starts_at) ? $shop->subscription_starts_at : $startsAt,
                'subscription_expires_at' => $expiresAt,
                'subscription_status' => 'active',
                'status' => $targetStatus,
                'status_updated_at' => now(),
            ]);

            // 4. Log
            SystemLog::create([
                'shop_id' => $shop->id,
                'user_id' => auth()->id(),
                'action' => 'PACKAGE_ASSIGNED_CASH',
                'model_type' => 'Subscription',
                'description' => "Super Admin assigned '{$package->name}' for {$durationDays} days to shop '{$shop->name}'. Status: {$targetStatus}. Cash collected: {$request->amount_paid}.",
            ]);
        });

        // Send activation email notification to shop admin
        SubscriptionNotificationService::sendPackageActivated($shop, $package, $request->amount_paid);

        $statusMsg = $targetStatus === 'active' ? "Shop is Active" : "Shop status set to " . ucfirst($targetStatus);
        return back()->with('success', "Saved package '{$package->name}' for shop '{$shop->name}' ({$statusMsg}). Valid until " . $expiresAt->format('d M, Y') . ". Activation email dispatched.");
    }

    /**
     * Change shop status (active, inactive, suspended) directly from subscriptions module.
     */
    public function toggleShopStatus(Request $request, Shop $shop)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,suspended',
            'reason' => 'nullable|string|max:255',
        ]);

        $status = $request->status;
        $reason = $request->reason ?: match($status) {
            'inactive' => 'Temporarily deactivated by Super Admin',
            'suspended' => 'Suspended by Super Admin',
            default => null,
        };

        $shop->update([
            'status' => $status,
            'status_reason' => $reason,
            'status_updated_at' => now(),
        ]);

        SystemLog::create([
            'shop_id' => $shop->id,
            'user_id' => auth()->id(),
            'action' => 'SHOP_STATUS_CHANGED',
            'model_type' => 'Shop',
            'description' => "Super Admin updated shop '{$shop->name}' status to " . strtoupper($status) . ($reason ? " ({$reason})" : ""),
        ]);

        $statusText = match($status) {
            'active' => 'Active',
            'inactive' => 'Inactive (Deactivated)',
            'suspended' => 'Suspended (Blocked)',
        };

        return back()->with('success', "Shop '{$shop->name}' status has been set to {$statusText}.");
    }

    /**
     * Approve a pending cash-by-hand subscription request.
     */
    public function approveRequest(Request $request, Subscription $subscription)
    {
        $package = $subscription->package;
        $shop = $subscription->shop;
        $now = now();
        $expiresAt = $now->copy()->addDays($package->duration_days);

        DB::transaction(function () use ($subscription, $shop, $package, $now, $expiresAt) {
            $subscription->update([
                'status' => 'active',
                'payment_status' => 'paid',
                'starts_at' => $now,
                'expires_at' => $expiresAt,
                'recorded_by_id' => auth()->id(),
                'payment_notes' => ($subscription->payment_notes ? $subscription->payment_notes . ' | ' : '') . 'Approved by Super Admin (' . auth()->user()->name . ')',
            ]);

            $shop->update([
                'package_id' => $package->id,
                'subscription_starts_at' => $now,
                'subscription_expires_at' => $expiresAt,
                'subscription_status' => 'active',
                'status' => 'active',
            ]);

            SystemLog::create([
                'shop_id' => $shop->id,
                'user_id' => auth()->id(),
                'action' => 'SUBSCRIPTION_APPROVED',
                'model_type' => 'Subscription',
                'description' => "Super Admin approved Cash payment of {$subscription->amount_paid} for shop '{$shop->name}'. Package '{$package->name}' activated.",
            ]);
        });

        // Send activation email notification to shop admin
        SubscriptionNotificationService::sendPackageActivated($shop, $package, $subscription->amount_paid);

        return back()->with('success', "Approved Cash By Hand payment for '{$shop->name}'. '{$package->name}' is now active. Activation email dispatched.");
    }

    /**
     * Manage packages page (view/edit price and details).
     */
    public function packages()
    {
        $packages = Package::withCount('shops')->orderBy('sort_order')->get();
        return view('super_admin.packages.index', compact('packages'));
    }

    /**
     * Update package pricing or details.
     */
    public function updatePackage(Request $request, Package $package)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'max_shops' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'features' => 'nullable|string',
        ]);

        $features = null;
        if ($request->filled('features')) {
            $features = array_filter(array_map('trim', explode("\n", str_replace("\r", "", $request->features))));
        }

        $package->update([
            'name' => $request->name,
            'price' => $request->price,
            'duration_days' => $request->duration_days,
            'max_shops' => $request->max_shops,
            'description' => $request->description,
            'features' => $features,
        ]);

        return back()->with('success', "Package '{$package->name}' updated successfully.");
    }
}
