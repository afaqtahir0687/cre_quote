<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use App\Models\Setting;

class SerialGenerator {
    public static function generate($type, $dateString) {
        $date = \Carbon\Carbon::parse($dateString);
        $dateStr = $date->format('Ymd');
        $setting = current_setting();
        $shopId = current_shop_id();
        
        if ($type === 'quote') {
            $prefix = ($setting->quote_prefix ?? 'QT-') . $dateStr . '-';
            $table = 'quotes';
            $column = 'quote_number';
        } elseif ($type === 'job_order') {
            $prefix = 'JO-' . $dateStr . '-';
            $table = 'job_orders';
            $column = 'order_number';
        } elseif ($type === 'delivery_note') {
            $prefix = 'DN-' . $dateStr . '-';
            $table = 'delivery_notes';
            $column = 'delivery_number';
        } else {
            $prefix = ($setting->invoice_prefix ?? 'INV-') . $dateStr . '-';
            $table = 'invoices';
            $column = 'invoice_number';
        }
        
        // Find latest matching this prefix for the current shop
        $query = DB::table($table)->where($column, 'like', $prefix . '%');
        if ($shopId) {
            $query->where('shop_id', $shopId);
        }
        $latest = $query->orderBy($column, 'desc')->first();
            
        $nextNum = 1;
        if ($latest) {
            $lastNumStr = str_replace($prefix, '', $latest->{$column});
            $nextNum = (int)$lastNumStr + 1;
        }
        
        $serial = $prefix . str_pad($nextNum, 2, '0', STR_PAD_LEFT);
        
        // Ensure strictly unique for this shop
        $existsQuery = function($s) use ($table, $column, $shopId) {
            $q = DB::table($table)->where($column, $s);
            if ($shopId) {
                $q->where('shop_id', $shopId);
            }
            return $q->exists();
        };

        while($existsQuery($serial)) {
            $nextNum++;
            $serial = $prefix . str_pad($nextNum, 2, '0', STR_PAD_LEFT);
        }
        
        return $serial;
    }
}
