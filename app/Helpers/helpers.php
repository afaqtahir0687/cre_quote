<?php

use App\Models\Setting;

if (!function_exists('format_currency')) {
    function format_currency($amount)
    {
        $setting = Setting::first();
        $symbol = $setting ? $setting->currency_symbol : '$';
        
        return $symbol . ' ' . number_format($amount, 2);
    }
}
