<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'billing_cycle',
        'duration_days',
        'max_shops',
        'max_employees',
        'features',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_days' => 'integer',
        'max_shops' => 'integer',
        'max_employees' => 'integer',
        'features' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Subscriptions for this package.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Shops currently using this package.
     */
    public function shops()
    {
        return $this->hasMany(Shop::class);
    }

    /**
     * Check if this is the trial package.
     */
    public function isTrial(): bool
    {
        return $this->slug === 'trial' || $this->duration_days <= 14;
    }

    /**
     * Check if this is the yearly package.
     */
    public function isYearly(): bool
    {
        return $this->slug === 'yearly';
    }

    /**
     * Check if this is the business package.
     */
    public function isBusiness(): bool
    {
        return $this->slug === 'business' || $this->max_shops > 1;
    }

    /**
     * Check if package allows multiple shops / branches.
     */
    public function allowsMultiShop(): bool
    {
        return $this->max_shops > 1;
    }

    /**
     * Official Saudi Riyal exchange rate (1 USD = 3.75 SAR pegged).
     */
    public const USD_TO_SAR_RATE = 3.75;

    /**
     * Get price converted to Saudi Riyal (SAR).
     */
    public function getPriceSarAttribute(): float
    {
        return round(((float) ($this->price ?? 0)) * self::USD_TO_SAR_RATE, 2);
    }

    /**
     * Formatted SAR price string.
     */
    public function formattedSarPrice(): string
    {
        return number_format($this->price_sar, 2) . ' SAR';
    }

    /**
     * Formatted USD price string.
     */
    public function formattedUsdPrice(): string
    {
        return '$' . number_format((float) ($this->price ?? 0), 2) . ' USD';
    }

    /**
     * Badge class for UI.
     */
    public function badgeClass(): string
    {
        return match ($this->slug) {
            'trial' => 'badge-soft-warning',
            'yearly' => 'badge-soft-primary',
            'business' => 'badge-soft-success',
            default => 'badge-soft-secondary',
        };
    }

    protected static function booted()
    {
        static::saved(function ($pkg) {
            if (!empty($pkg->name)) {
                \App\Services\TranslationService::warmCache($pkg->name);
            }
            if (!empty($pkg->description)) {
                \App\Services\TranslationService::warmCache($pkg->description);
            }
        });
    }
}
