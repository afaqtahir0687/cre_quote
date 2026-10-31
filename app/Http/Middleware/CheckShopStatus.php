<?php

namespace App\Http\Middleware;

use App\Services\SubscriptionNotificationService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckShopStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            // Super Admin always has access
            if ($user->isSuperAdmin()) {
                return $next($request);
            }

            // Check if user has an assigned shop
            if ($user->shop) {
                $shop = $user->shop;

                // 1. Permanently suspended or deactivated by Super Admin
                if (!$shop->isActive()) {
                    auth()->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    $msg = $shop->isSuspended()
                        ? "Your shop account ('{$shop->name}') is permanently suspended. Please contact Super Admin."
                        : "Your shop account is currently inactive. Please contact Super Admin.";

                    return redirect()->route('login')->with('error', $msg);
                }

                // 2. Subscription / 1-Week Test package expired
                if ($shop->isSubscriptionExpired()) {
                    // Send expiry email if not already sent for this expiry cycle
                    if (!$shop->subscription_expiry_notified_at) {
                        SubscriptionNotificationService::sendPackageExpired($shop);
                    }

                    $exemptRoutes = [
                        'subscription.expired',
                        'subscription.request_renewal',
                        'logout',
                    ];

                    if (!in_array($request->route()?->getName(), $exemptRoutes)) {
                        return redirect()->route('subscription.expired');
                    }
                }
            }
        }

        return $next($request);
    }
}
