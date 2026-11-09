<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'product_id',
        'description',
        'item_text',
        'unit_price',
        'quantity',
        'tax_percentage',
        'discount',
        'total'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getLocalizedDescriptionAttribute(): string
    {
        return tdb($this->description);
    }

    protected static function booted()
    {
        static::saved(function ($item) {
            if (!empty($item->description)) {
                \App\Services\TranslationService::warmCache($item->description);
            }
        });
    }
}
