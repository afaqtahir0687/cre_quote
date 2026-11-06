<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToShop;

class Product extends Model
{
    use BelongsToShop;

    protected $fillable = [
        'shop_id',
        'category_id',
        'unit_id',
        'name',
        'sku',
        'description',
        'price',
        'purchase_price',
        'sale_price',
        'tax_percentage',
        'initial_stock',
        'stock',
        'type',
        'image',
        'images',
        'low_stock_notified_at',
    ];

    protected $casts = [
        'images' => 'array',
        'price' => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'initial_stock' => 'integer',
        'stock' => 'integer',
        'low_stock_notified_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function getSoldQuantityAttribute(): int
    {
        $initial = $this->initial_stock ?? 0;
        $current = $this->stock ?? 0;
        return max(0, $initial - $current);
    }

    public function getRemainingPercentageAttribute(): float
    {
        $initial = (int) ($this->initial_stock ?? 0);
        if ($initial <= 0) {
            return ($this->stock > 0) ? 100.0 : 0.0;
        }
        return round(($this->stock / $initial) * 100, 1);
    }

    public function getSoldPercentageAttribute(): float
    {
        $initial = (int) ($this->initial_stock ?? 0);
        if ($initial <= 0) {
            return 0.0;
        }
        return round((max(0, $initial - $this->stock) / $initial) * 100, 1);
    }

    public function isLowStock(): bool
    {
        if ($this->type !== 'product') {
            return false;
        }
        $initial = (int) ($this->initial_stock ?? 0);
        if ($initial <= 0) {
            return ($this->stock <= 5);
        }
        // 70% or more sold, 30% or less remaining
        return (($this->stock / $initial) <= 0.30);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!empty($this->image) && file_exists(public_path($this->image))) {
            return asset($this->image);
        }
        return null;
    }

    public function getLocalizedNameAttribute(): string
    {
        return tdb($this->name);
    }

    public function getLocalizedDescriptionAttribute(): string
    {
        return tdb($this->description);
    }

    protected static function booted()
    {
        static::saving(function ($product) {
            // Keep price and sale_price synchronized
            if (!empty($product->sale_price) && empty($product->price)) {
                $product->price = $product->sale_price;
            } elseif (!empty($product->price) && empty($product->sale_price)) {
                $product->sale_price = $product->price;
            } elseif (!empty($product->sale_price)) {
                $product->price = $product->sale_price;
            }

            // If stock was restocked above 30%, reset low_stock_notified_at
            if ($product->initial_stock > 0 && ($product->stock / $product->initial_stock) > 0.30) {
                $product->low_stock_notified_at = null;
            }
        });

        static::saved(function ($product) {
            if (!empty($product->name)) {
                \App\Services\TranslationService::warmCache($product->name);
            }
            if (!empty($product->description)) {
                \App\Services\TranslationService::warmCache($product->description);
            }

            // Check if product hit low stock threshold and trigger alert
            if ($product->isLowStock() && empty($product->low_stock_notified_at)) {
                \App\Services\LowStockAlertService::sendAlert($product);
            }
        });
    }
}
