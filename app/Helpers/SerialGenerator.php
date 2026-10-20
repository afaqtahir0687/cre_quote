<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use App\Models\Setting;

class SerialGenerator {
    public static function generate($type, $dateString) {
        $date = \Carbon\Carbon::parse($dateString);
        $dateStr = $date->format('Ymd');
        $setting = Setting::first();
        
        if ($type === 'quote') {
            $prefix = ($setting->quote_prefix ?? 'QT-') . $dateStr . '-';
            $table = 'quotes';
            $column = 'quote_number';
        } elseif ($type === 'job_order') {
            $prefix = 'JO-' . $dateStr . '-';
            $table = 'job_orders';
            $column = 'order_number';
        } else {
            $prefix = ($setting->invoice_prefix ?? 'INV-') . $dateStr . '-';
            $table = 'invoices';
            $column = 'invoice_number';
        }
        
        // Find latest matching this prefix
        $latest = DB::table($table)
            ->where($column, 'like', $prefix . '%')
            ->orderBy($column, 'desc')
            ->first();
            
        $nextNum = 1;
        if ($latest) {
            $lastNumStr = str_replace($prefix, '', $latest->{$column});
            $nextNum = (int)$lastNumStr + 1;
        }
        
        $serial = $prefix . str_pad($nextNum, 2, '0', STR_PAD_LEFT);
        
        // Ensure strictly unique
        while(DB::table($table)->where($column, $serial)->exists()) {
            $nextNum++;
            $serial = $prefix . str_pad($nextNum, 2, '0', STR_PAD_LEFT);
        }
        
        return $serial;
    }
}
