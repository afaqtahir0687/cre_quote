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
}
