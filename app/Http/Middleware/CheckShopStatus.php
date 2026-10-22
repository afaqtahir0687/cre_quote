<?php

namespace App\Http\Middleware;

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
                if (!$user->shop->isActive()) {
                    auth()->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('login')->with('error', 'Your shop account is currently deactivated or suspended. Please contact the platform Super Admin.');
                }
            }
        }

        return $next($request);
    }
}
