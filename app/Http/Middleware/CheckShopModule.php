<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckShopModule
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        if (!shop_has_module($module)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('This module is not enabled for your shop account. Please contact Super Admin to activate it.'),
                ], 403);
            }

            return redirect()->route('dashboard')->with('error', __('This module is not enabled for your shop account. Please contact Super Admin to activate it.'));
        }

        return $next($request);
    }
}
