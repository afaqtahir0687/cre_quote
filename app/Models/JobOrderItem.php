<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobOrderItem extends Model
{
    protected $fillable = [
        'job_order_id',
        'description',
        'item_text',
        'quantity',
        'unit_price',
        'tax_percentage',
        'discount',
        'total',
        'images',
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public function jobOrder()
    {
        return $this->belongsTo(JobOrder::class);
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
