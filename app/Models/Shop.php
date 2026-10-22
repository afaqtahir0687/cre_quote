<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shop extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'currency_symbol',
        'invoice_prefix',
        'quote_prefix',
        'status',
        'status_reason',
        'status_message',
        'grace_period_days',
        'action_deadline',
        'status_updated_at',
        'logo',
        'notes',
    ];

    protected $casts = [
        'action_deadline' => 'date',
        'status_updated_at' => 'datetime',
        'grace_period_days' => 'integer',
    ];

    /**
     * Users belonging to this shop.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Shop admins.
     */
    public function admins()
    {
        return $this->hasMany(User::class)->where('role', 'admin');
    }

    /**
     * Employees belonging to this shop.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Customers belonging to this shop.
     */
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    /**
     * Products belonging to this shop.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Quotes belonging to this shop.
     */
    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

    /**
     * Job orders belonging to this shop.
     */
    public function jobOrders()
    {
        return $this->hasMany(JobOrder::class);
    }

    /**
     * Invoices belonging to this shop.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Delivery notes belonging to this shop.
     */
    public function deliveryNotes()
    {
        return $this->hasMany(DeliveryNote::class);
    }

    /**
     * Departments belonging to this shop.
     */
    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    /**
     * Shop settings.
     */
    public function setting()
    {
        return $this->hasOne(Setting::class);
    }

    /**
     * System logs belonging to this shop.
     */
    public function systemLogs()
    {
        return $this->hasMany(SystemLog::class);
    }

    /**
     * Scope active shops.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope inactive shops.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope suspended shops.
     */
    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    /**
     * Check if shop is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if shop is inactive (temporary).
     */
    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    /**
     * Check if shop is suspended (permanent / blocked).
     */
    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    /**
     * Get soft badge class for current status.
     */
    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'active' => 'badge-soft-success',
            'inactive' => 'badge-soft-warning',
            'suspended' => 'badge-soft-danger',
            default => 'badge-soft-secondary',
        };
    }

    /**
     * Get human label for status.
     */
    public function statusLabel(): string
    {
        return match ($this->status) {
            'active' => 'Active',
            'inactive' => 'Inactive (Temporary)',
            'suspended' => 'Suspended (Blocked)',
            default => ucfirst($this->status),
        };
    }

    /**
     * Formatted display string for shop phone number.
     */
    public function getFormattedPhoneAttribute(): string
    {
        $phone = $this->phone;
        if (!$phone) {
            return '';
        }

        if (preg_match('/^\+966(\d{2})(\d{3})(\d{4})$/', $phone, $m)) {
            return "+966 {$m[1]} {$m[2]} {$m[3]}";
        }
        if (preg_match('/^\+92(\d{3})(\d{7})$/', $phone, $m)) {
            return "+92 {$m[1]} {$m[2]}";
        }
        if (preg_match('/^\+971(\d{2})(\d{3})(\d{4})$/', $phone, $m)) {
            return "+971 {$m[1]} {$m[2]} {$m[3]}";
        }

        return $phone;
    }
}
