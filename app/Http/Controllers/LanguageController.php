<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;

class LanguageController extends Controller
{
    public function switch(Request $request, $locale)
    {
        if (in_array($locale, ['en', 'ar'], true)) {
            session(['locale' => $locale]);
            Cookie::queue('app_locale', $locale, 60 * 24 * 365);
            App::setLocale($locale);
            if (class_exists(\Carbon\Carbon::class)) {
                \Carbon\Carbon::setLocale($locale);
            }
        }

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'locale' => $locale,
                'dir' => $locale === 'ar' ? 'rtl' : 'ltr',
                'name' => $locale === 'ar' ? 'العربية' : 'English',
                'flag' => $locale === 'ar' ? '🇸🇦' : '🇬🇧',
                'message' => $locale === 'ar' ? 'تم تحويل اللغة إلى العربية بنجاح' : 'Language switched to English successfully'
            ]);
        }

        return redirect()->back();
    }
}

