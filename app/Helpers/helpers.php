<?php

use App\Models\Setting;
use App\Models\Shop;

if (!function_exists('platform_logo_url')) {
    /**
     * Get the Super Admin / Central Platform Logo URL.
     */
    function platform_logo_url(): ?string
    {
        $platformSetting = Setting::withoutGlobalScopes()->whereNull('shop_id')->first();
        if (!empty($platformSetting?->company_logo) && file_exists(public_path($platformSetting->company_logo))) {
            return asset($platformSetting->company_logo);
        }

        if (file_exists(public_path('uploads/logo/crelogics-logo.png'))) {
            return asset('uploads/logo/crelogics-logo.png');
        }

        return null;
    }
}

if (!function_exists('platform_logo_path')) {
    /**
     * Get the local filesystem path of the Super Admin / Platform Logo.
     */
    function platform_logo_path(): ?string
    {
        $platformSetting = Setting::withoutGlobalScopes()->whereNull('shop_id')->first();
        if (!empty($platformSetting?->company_logo) && file_exists(public_path($platformSetting->company_logo))) {
            return public_path($platformSetting->company_logo);
        }

        if (file_exists(public_path('uploads/logo/crelogics-logo.png'))) {
            return public_path('uploads/logo/crelogics-logo.png');
        }

        return null;
    }
}

if (!function_exists('platform_brand_name')) {
    /**
     * Get the Super Admin / Central Platform Brand Title.
     */
    function platform_brand_name(): string
    {
        $platformSetting = Setting::withoutGlobalScopes()->whereNull('shop_id')->first();
        if (!empty($platformSetting?->company_name)) {
            return $platformSetting->company_name;
        }

        return config('app.name', 'Crelogics Platform');
    }
}

if (!function_exists('current_shop_id')) {
    /**
     * Get the active shop ID for the current context with strict isolation.
     */
    function current_shop_id(): ?int
    {
        if (!auth()->check()) {
            return null;
        }

        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            $sessionShop = session('active_shop_id');
            // If super admin is in global view ('all' or empty), return null
            if (empty($sessionShop) || $sessionShop === 'all') {
                return null;
            }
            if (is_numeric($sessionShop)) {
                return (int) $sessionShop;
            }
            return null;
        }

        // Regular admin: check if switched to branch shop
        if ($user->isAdmin()) {
            $sessionShop = session('active_shop_id');
            if (!empty($sessionShop) && is_numeric($sessionShop)) {
                $userShop = $user->shop;
                if ($userShop) {
                    $mainShop = ($userShop->parent_shop_id && $userShop->parentShop) ? $userShop->parentShop : $userShop;
                    $allowed = $mainShop->branches->pluck('id')->push($mainShop->id)->all();
                    if (in_array((int)$sessionShop, $allowed)) {
                        return (int) $sessionShop;
                    }
                }
            }
        }

        // Default regular admin or employee: strictly confined to their assigned shop
        return $user->shop_id ? (int) $user->shop_id : null;
    }
}

if (!function_exists('current_shop')) {
    /**
     * Get the active Shop model. Strictly isolated (never leaks first shop to other contexts).
     */
    function current_shop(): ?Shop
    {
        $shopId = current_shop_id();
        if ($shopId) {
            return Shop::find($shopId);
        }

        return null;
    }
}

if (!function_exists('current_setting')) {
    /**
     * Get the current shop setting. Strictly isolated to the active shop.
     */
    function current_setting(): ?Setting
    {
        $shopId = current_shop_id();
        if ($shopId) {
            return Setting::withoutGlobalScopes()->where('shop_id', $shopId)->first();
        }

        // In global context, return the platform setting (or fallback to first available setting)
        return Setting::withoutGlobalScopes()->whereNull('shop_id')->first()
            ?: Setting::withoutGlobalScopes()->first();
    }
}

if (!function_exists('shop_logo_url')) {
    /**
     * Get the web asset URL for a shop's logo with strict shop isolation.
     * Each shop gets ONLY its own logo. No cross-shop leak.
     */
    function shop_logo_url($shop = null, $setting = null): ?string
    {
        if ($shop === null) {
            $shop = current_shop();
        }

        if ($shop) {
            // 1. Check shop's uploaded logo
            if (!empty($shop->logo) && file_exists(public_path($shop->logo))) {
                return asset($shop->logo);
            }

            // 2. Check setting's uploaded logo for THIS specific shop ONLY
            if ($setting && $setting->shop_id == $shop->id && !empty($setting->company_logo) && file_exists(public_path($setting->company_logo))) {
                return asset($setting->company_logo);
            }

            $shopSetting = Setting::withoutGlobalScopes()->where('shop_id', $shop->id)->first();
            if (!empty($shopSetting?->company_logo) && file_exists(public_path($shopSetting->company_logo))) {
                return asset($shopSetting->company_logo);
            }

            // If this shop has not uploaded any logo, return null so views display a monogram initials badge
            return null;
        }

        // Global / Super Admin platform overview context: Return Central Platform logo
        return platform_logo_url();
    }
}

if (!function_exists('shop_logo_path')) {
    /**
     * Get the local filesystem path of the logo for mPDF / DomPDF rendering with strict isolation.
     */
    function shop_logo_path($shop = null, $setting = null): ?string
    {
        if ($shop === null) {
            $shop = current_shop();
        }

        if ($shop) {
            if (!empty($shop->logo) && file_exists(public_path($shop->logo))) {
                return public_path($shop->logo);
            }

            if ($setting && $setting->shop_id == $shop->id && !empty($setting->company_logo) && file_exists(public_path($setting->company_logo))) {
                return public_path($setting->company_logo);
            }

            $shopSetting = Setting::withoutGlobalScopes()->where('shop_id', $shop->id)->first();
            if (!empty($shopSetting?->company_logo) && file_exists(public_path($shopSetting->company_logo))) {
                return public_path($shopSetting->company_logo);
            }

            return null;
        }

        return platform_logo_path();
    }
}

if (!function_exists('shop_bank_stamp_url')) {
    /**
     * Get the web asset URL for the shop's bank information & stamp image.
     */
    function shop_bank_stamp_url($shop = null, $setting = null): ?string
    {
        if ($shop === null) {
            $shop = current_shop();
        }

        if ($shop) {
            if (!empty($shop->bank_stamp_logo) && file_exists(public_path($shop->bank_stamp_logo))) {
                return asset($shop->bank_stamp_logo);
            }

            if ($setting && $setting->shop_id == $shop->id && !empty($setting->bank_stamp_logo) && file_exists(public_path($setting->bank_stamp_logo))) {
                return asset($setting->bank_stamp_logo);
            }

            $shopSetting = Setting::withoutGlobalScopes()->where('shop_id', $shop->id)->first();
            if (!empty($shopSetting?->bank_stamp_logo) && file_exists(public_path($shopSetting->bank_stamp_logo))) {
                return asset($shopSetting->bank_stamp_logo);
            }
        } elseif ($setting && !empty($setting->bank_stamp_logo) && file_exists(public_path($setting->bank_stamp_logo))) {
            return asset($setting->bank_stamp_logo);
        }

        if (file_exists(public_path('uploads/logo/stamp_logo.jpeg'))) {
            return asset('uploads/logo/stamp_logo.jpeg');
        }

        return null;
    }
}

if (!function_exists('shop_bank_stamp_path')) {
    /**
     * Get the local filesystem path of the bank information & stamp image for PDF generation.
     */
    function shop_bank_stamp_path($shop = null, $setting = null): ?string
    {
        if ($shop === null) {
            $shop = current_shop();
        }

        if ($shop) {
            if (!empty($shop->bank_stamp_logo) && file_exists(public_path($shop->bank_stamp_logo))) {
                return public_path($shop->bank_stamp_logo);
            }

            if ($setting && $setting->shop_id == $shop->id && !empty($setting->bank_stamp_logo) && file_exists(public_path($setting->bank_stamp_logo))) {
                return public_path($setting->bank_stamp_logo);
            }

            $shopSetting = Setting::withoutGlobalScopes()->where('shop_id', $shop->id)->first();
            if (!empty($shopSetting?->bank_stamp_logo) && file_exists(public_path($shopSetting->bank_stamp_logo))) {
                return public_path($shopSetting->bank_stamp_logo);
            }
        } elseif ($setting && !empty($setting->bank_stamp_logo) && file_exists(public_path($setting->bank_stamp_logo))) {
            return public_path($setting->bank_stamp_logo);
        }

        if (file_exists(public_path('uploads/logo/stamp_logo.jpeg'))) {
            return public_path('uploads/logo/stamp_logo.jpeg');
        }

        return null;
    }
}

if (!function_exists('shop_brand_name')) {
    /**
     * Get the active company/shop name for the current context with strict isolation.
     */
    function shop_brand_name($shop = null, $setting = null): string
    {
        if ($shop === null) {
            $shop = current_shop();
        }

        if ($shop) {
            return $shop->name;
        }

        return platform_brand_name();
    }
}

if (!function_exists('format_currency')) {
    /**
     * Format an amount with the current shop's currency symbol.
     */
    function format_currency($amount): string
    {
        $shop = current_shop();
        $setting = current_setting();
        $symbol = $shop?->currency_symbol ?? $setting?->currency_symbol ?? 'SAR';
        
        return $symbol . ' ' . number_format((float) $amount, 2);
    }
}

if (!function_exists('format_subscription_currency')) {
    /**
     * Format subscription and package pricing in SAR.
     */
    function format_subscription_currency($amount): string
    {
        return 'SAR ' . number_format((float) $amount, 2);
    }
}
