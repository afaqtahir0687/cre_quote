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
        'business_type',
        'enabled_modules',
        'status_reason',
        'status_message',
        'grace_period_days',
        'action_deadline',
        'status_updated_at',
        'logo',
        'bank_stamp_logo',
        'notes',
        'package_id',
        'subscription_starts_at',
        'subscription_expires_at',
        'subscription_expiry_notified_at',
        'subscription_status',
        'parent_shop_id',
        'owner_id',
    ];

    protected $casts = [
        'enabled_modules' => 'array',
        'action_deadline' => 'date',
        'status_updated_at' => 'datetime',
        'grace_period_days' => 'integer',
        'subscription_starts_at' => 'datetime',
        'subscription_expires_at' => 'datetime',
        'subscription_expiry_notified_at' => 'datetime',
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

    /**
     * The package assigned to this shop.
     */
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * All subscriptions/payment logs for this shop.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class)->latest('id');
    }

    /**
     * Current active subscription log.
     */
    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)->where('status', 'active')->latestOfMany();
    }

    /**
     * Sub-shops / Branches under this main shop.
     */
    public function branches()
    {
        return $this->hasMany(Shop::class, 'parent_shop_id');
    }

    /**
     * Main / parent shop if this is a branch.
     */
    public function parentShop()
    {
        return $this->belongsTo(Shop::class, 'parent_shop_id');
    }

    /**
     * The business owner / admin user.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Check if shop has an active, unexpired subscription.
     */
    public function hasActiveSubscription(): bool
    {
        if ($this->parent_shop_id && $this->parentShop) {
            return $this->parentShop->hasActiveSubscription();
        }

        if ($this->subscription_status !== 'active') {
            return false;
        }

        if ($this->subscription_expires_at && $this->subscription_expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Check if subscription has expired.
     */
    public function isSubscriptionExpired(): bool
    {
        return !$this->hasActiveSubscription();
    }

    /**
     * Days remaining until subscription expires.
     */
    public function subscriptionDaysRemaining(): int
    {
        $target = ($this->parent_shop_id && $this->parentShop) ? $this->parentShop : $this;

        if (!$target->subscription_expires_at) {
            return 999;
        }

        return (int) now()->startOfDay()->diffInDays($target->subscription_expires_at->startOfDay(), false);
    }

    /**
     * Check if shop is on Business (multi-shop) plan.
     */
    public function isBusinessAccount(): bool
    {
        $target = ($this->parent_shop_id && $this->parentShop) ? $this->parentShop : $this;
        return $target->package && $target->package->allowsMultiShop();
    }

    /**
     * Check if shop is on Test Trial plan.
     */
    public function isTrialAccount(): bool
    {
        $target = ($this->parent_shop_id && $this->parentShop) ? $this->parentShop : $this;
        return $target->package && $target->package->isTrial();
    }

    /**
     * Check if this shop can add more branches.
     */
    public function canAddBranch(): bool
    {
        if (!$this->isBusinessAccount()) {
            return false;
        }

        $target = ($this->parent_shop_id && $this->parentShop) ? $this->parentShop : $this;
        $maxShops = $target->package->max_shops ?? 5;
        $currentBranchesCount = $target->branches()->count() + 1; // including main shop

        return $currentBranchesCount < $maxShops;
    }

    /**
     * Travel trips belonging to this shop.
     */
    public function travelTrips()
    {
        return $this->hasMany(TravelTrip::class);
    }

    /**
     * Travel bookings belonging to this shop.
     */
    public function travelBookings()
    {
        return $this->hasMany(TravelBooking::class);
    }

    /**
     * All available business categories / industries.
     */
    public static function availableBusinessTypes(): array
    {
        return [
            'printing_advertising' => [
                'name' => 'Printing, Signage & Advertising',
                'name_ar' => 'الطباعة واللوحات الإعلانية',
                'icon' => 'fas fa-print',
                'description' => 'Flex printing, 3D channel signage, vehicle graphics, job order production.',
            ],
            'travel_umrah' => [
                'name' => 'Umrah, Travel & Tourism Agency',
                'name_ar' => 'وكالة العمرة والسياحة والسفر',
                'icon' => 'fas fa-kaaba',
                'description' => 'Bus seat booking, Umrah trips, passenger boarding passes, hotel & travel expense packages.',
            ],
            'general_trading' => [
                'name' => 'General Trading & Retail Store',
                'name_ar' => 'التجارة العامة ونقاط البيع',
                'icon' => 'fas fa-shopping-cart',
                'description' => 'Inventory, products, sales invoices, customer ledger balances.',
            ],
            'services_agency' => [
                'name' => 'Digital & Professional Services',
                'name_ar' => 'الخدمات الرقمية والاستشارية',
                'icon' => 'fas fa-laptop-code',
                'description' => 'Client proposals, project milestones, hourly/fixed services billing.',
            ],
            'custom' => [
                'name' => 'Custom / Full Enterprise Suite',
                'name_ar' => 'أعمال مخصصة شاملة لجميع الوحدات',
                'icon' => 'fas fa-cubes',
                'description' => 'Tailor-fit permissions with all ERP modules selectable.',
            ],
        ];
    }

    /**
     * All available modular features.
     */
    public static function availableModules(): array
    {
        return [
            'job_orders' => [
                'name' => 'Job Orders & Workshop Production',
                'name_ar' => 'أوامر التشغيل والإنتاج في الورشة',
                'icon' => 'fas fa-tasks',
                'category' => 'sales',
            ],
            'quotations' => [
                'name' => 'Quotations & Cost Estimates',
                'name_ar' => 'عروض الأسعار والتقديرات',
                'icon' => 'fas fa-file-invoice',
                'category' => 'sales',
            ],
            'invoices' => [
                'name' => 'Sales Invoices & Billing',
                'name_ar' => 'فواتير المبيعات والتحصيل',
                'icon' => 'fas fa-file-invoice-dollar',
                'category' => 'sales',
            ],
            'delivery_notes' => [
                'name' => 'Delivery Notes (Challan)',
                'name_ar' => 'سندات التسليم والشحن',
                'icon' => 'fas fa-truck',
                'category' => 'sales',
            ],
            'customers' => [
                'name' => 'Customer Relationship Management (CRM)',
                'name_ar' => 'إدارة العملاء وكشوفات الحساب',
                'icon' => 'fas fa-user-friends',
                'category' => 'crm',
            ],
            'products' => [
                'name' => 'Products & Materials Catalog',
                'name_ar' => 'المنتجات وخامات المستودع',
                'icon' => 'fas fa-boxes',
                'category' => 'inventory',
            ],
            'services' => [
                'name' => 'Custom Services Catalog',
                'name_ar' => 'دليل الخدمات المخصصة',
                'icon' => 'fas fa-concierge-bell',
                'category' => 'inventory',
            ],
            'categories' => [
                'name' => 'Product & Material Categories',
                'name_ar' => 'تصنيفات الأصناف والمواد',
                'icon' => 'fas fa-tags',
                'category' => 'inventory',
            ],
            'units' => [
                'name' => 'Units of Measurement',
                'name_ar' => 'وحدات القياس (متر، سم، طقم...)',
                'icon' => 'fas fa-balance-scale',
                'category' => 'inventory',
            ],
            'employees' => [
                'name' => 'Employees & Staff (HRM)',
                'name_ar' => 'الموظفين والموارد البشرية',
                'icon' => 'fas fa-id-badge',
                'category' => 'hrm',
            ],
            'departments' => [
                'name' => 'Departments & Machine Units',
                'name_ar' => 'الأقسام ووحدات التشغيل',
                'icon' => 'fas fa-cogs',
                'category' => 'hrm',
            ],
            'travel_tourism' => [
                'name' => 'Umrah, Bus Trips & Seat Booking',
                'name_ar' => 'إدارة العمرة والرحلات وحجز مقاعد الحافلات',
                'icon' => 'fas fa-bus-alt',
                'category' => 'travel',
            ],
        ];
    }

    /**
     * Default modules by business type.
     */
    public static function defaultModulesForType(string $type): array
    {
        return match ($type) {
            'travel_umrah' => [
                'travel_tourism',
                'invoices',
                'customers',
                'employees',
                'departments',
            ],
            'general_trading' => [
                'invoices',
                'delivery_notes',
                'customers',
                'products',
                'categories',
                'units',
                'employees',
            ],
            'services_agency' => [
                'quotations',
                'invoices',
                'customers',
                'services',
                'employees',
            ],
            'custom' => array_keys(self::availableModules()),
            default => [ // 'printing_advertising'
                'job_orders',
                'quotations',
                'invoices',
                'delivery_notes',
                'customers',
                'products',
                'services',
                'categories',
                'units',
                'employees',
                'departments',
            ],
        };
    }

    /**
     * Check if a specific module is enabled for this shop.
     */
    public function hasModule(string $moduleKey): bool
    {
        if (is_array($this->enabled_modules) && count($this->enabled_modules) > 0) {
            return in_array($moduleKey, $this->enabled_modules, true);
        }

        $type = $this->business_type ?: 'printing_advertising';
        $defaults = self::defaultModulesForType($type);
        return in_array($moduleKey, $defaults, true);
    }

    protected static function booted()
    {
        static::saved(function ($shop) {
            if (!empty($shop->name)) {
                \App\Services\TranslationService::warmCache($shop->name);
            }
            if (!empty($shop->city)) {
                \App\Services\TranslationService::warmCache($shop->city);
            }
            if (!empty($shop->notes)) {
                \App\Services\TranslationService::warmCache($shop->notes);
            }
        });
    }
}
