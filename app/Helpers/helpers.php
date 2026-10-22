<?php

use App\Models\Setting;
use App\Models\Shop;

if (!function_exists('current_shop_id')) {
    /**
     * Get the active shop ID for the current context.
     */
    function current_shop_id(): ?int
    {
        if (!auth()->check()) {
            return null;
        }

        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            $sessionShop = session('active_shop_id');
            if ($sessionShop === 'all') {
                return null;
            }
            if (!empty($sessionShop) && is_numeric($sessionShop)) {
                return (int) $sessionShop;
            }
            // Default to user's assigned shop or first active shop
            if (!empty($user->shop_id)) {
                return (int) $user->shop_id;
            }
            $firstShop = Shop::active()->first();
            return $firstShop ? (int) $firstShop->id : null;
        }

        return $user->shop_id ? (int) $user->shop_id : null;
    }
}

if (!function_exists('current_shop')) {
    /**
     * Get the active Shop model.
     */
    function current_shop(): ?Shop
    {
        $shopId = current_shop_id();
        if ($shopId) {
            return Shop::find($shopId);
        }

        if (auth()->check() && auth()->user()->isSuperAdmin()) {
            return Shop::active()->first();
        }

        return null;
    }
}

if (!function_exists('current_setting')) {
    /**
     * Get the current shop setting, with fallback.
     */
    function current_setting(): ?Setting
    {
        $shopId = current_shop_id();
        if ($shopId) {
            $setting = Setting::withoutGlobalScopes()->where('shop_id', $shopId)->first();
            if ($setting) {
                return $setting;
            }
        }

        return Setting::withoutGlobalScopes()->first();
    }
}

if (!function_exists('format_currency')) {
    /**
     * Format an amount with the current shop's currency symbol.
     */
    function format_currency($amount): string
    {
        $setting = current_setting();
        $symbol = $setting && !empty($setting->currency_symbol) ? $setting->currency_symbol : '$';
        
        return $symbol . ' ' . number_format((float) $amount, 2);
    }
}
