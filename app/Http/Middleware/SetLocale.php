<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = ['en', 'ar'];

        // 1. Check session
        $locale = session('locale');

        // 2. Fallback to cookie
        if (!$locale || !in_array($locale, $supportedLocales, true)) {
            $locale = $request->cookie('app_locale');
        }

        // 3. Fallback to default
        if (!$locale || !in_array($locale, $supportedLocales, true)) {
            $locale = config('app.locale', 'en');
        }

        App::setLocale($locale);
        if (class_exists(\Carbon\Carbon::class)) {
            \Carbon\Carbon::setLocale($locale);
        }

        $response = $next($request);

        // Keep cookie in sync
        if ($request->cookie('app_locale') !== $locale) {
            Cookie::queue('app_locale', $locale, 60 * 24 * 365); // 1 year
        }

        return $response;
    }
}

