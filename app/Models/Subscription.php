<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'package_id',
        'user_id',
        'payment_method',
        'payment_status',
        'amount_paid',
        'starts_at',
        'expires_at',
        'status',
        'payment_notes',
        'recorded_by_id',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * The shop for this subscription.
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * The package plan.
     */
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * The user / admin subscriber.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The super admin user who recorded/approved this cash payment.
     */
    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by_id');
    }

    /**
     * Check if subscription is active.
     */
    public function isActive(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Check if subscription is expired.
     */
    public function isExpired(): bool
    {
        return !$this->isActive();
    }

    /**
     * Calculate days remaining.
     */
    public function daysRemaining(): int
    {
        if (!$this->expires_at) {
            return 999;
        }

        return (int) now()->diffInDays($this->expires_at, false);
    }

    /**
     * Human readable status badge.
     */
    public function statusBadgeClass(): string
    {
        if ($this->isExpired()) {
            return 'badge-soft-danger';
        }

        return match ($this->status) {
            'active' => 'badge-soft-success',
            'pending' => 'badge-soft-warning',
            'cancelled' => 'badge-soft-secondary',
            default => 'badge-soft-info',
        };
    }
}
