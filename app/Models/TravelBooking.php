<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TravelBooking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'shop_id',
        'employee_id',
        'travel_trip_id',
        'customer_id',
        'booking_reference',
        'passenger_name',
        'passenger_phone',
        'passenger_id_number',
        'passenger_gender',
        'passenger_nationality',
        'pickup_location',
        'dropoff_location',
        'return_dropoff_location',
        'journey_type',
        'one_way_direction',
        'salesman_name',
        'salesman_phone',
        'seat_number',
        'seat_class',
        'package_type',
        'hotel_name',
        'hotel_address',
        'room_type',
        'nights_count',
        'travel_fare',
        'hotel_expense',
        'extra_charges',
        'discount',
        'tax_amount',
        'total_amount',
        'paid_amount',
        'balance_due',
        'payment_status',
        'payment_method',
        'booking_status',
        'invoice_id',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'seat_number' => 'integer',
        'nights_count' => 'integer',
        'travel_fare' => 'decimal:2',
        'hotel_expense' => 'decimal:2',
        'extra_charges' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_due' => 'decimal:2',
    ];

    /**
     * Shop this booking belongs to.
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Employee who handled this booking.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * The trip this booking is for.
     */
    public function trip()
    {
        return $this->belongsTo(TravelTrip::class, 'travel_trip_id');
    }

    /**
     * Linked customer in CRM (if any).
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Linked sales invoice (if generated).
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * User who created the booking.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope bookings by current shop (sub-branches see their own sales; head office sees entire fleet).
     */
    public function scopeForCurrentShop($query)
    {
        $shop = current_shop();
        if ($shop) {
            if ($shop->parent_shop_id) {
                return $query->where('shop_id', $shop->id);
            }
            $ids = $shop->allBranchAndParentShopIds();
            return $query->whereIn('shop_id', $ids);
        }
        $shopId = current_shop_id();
        if ($shopId) {
            return $query->where('shop_id', $shopId);
        }
        return $query;
    }

    /**
     * Generate unique booking reference.
     */
    public static function generateReference(int $shopId): string
    {
        $count = self::where('shop_id', $shopId)->count() + 1;
        return 'BK-' . date('Ym') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Effective Salesman name.
     */
    public function getEffectiveSalesmanAttribute(): string
    {
        if (!empty($this->salesman_name)) {
            return $this->salesman_name;
        }
        if ($this->creator && !empty($this->creator->name)) {
            return $this->creator->name;
        }
        return 'Umrah Agent';
    }

    /**
     * Effective Salesman Phone.
     */
    public function getEffectiveSalesmanPhoneAttribute(): ?string
    {
        if (!empty($this->salesman_phone)) {
            return $this->salesman_phone;
        }
        if ($this->creator && !empty($this->creator->employee?->phone)) {
            return $this->creator->employee->phone;
        }
        if ($this->creator && !empty($this->creator->phone)) {
            return $this->creator->phone;
        }
        return $this->shop?->phone;
    }

    /**
     * Effective Hotel Name.
     */
    public function getEffectiveHotelNameAttribute(): ?string
    {
        return $this->hotel_name ?: $this->trip?->hotel_name;
    }

    /**
     * Seat Class Badge HTML.
     */
    public function seatClassBadge(): string
    {
        return match(strtolower($this->seat_class ?: 'economy')) {
            'vip' => '<span class="badge bg-warning text-dark border border-warning-subtle fw-bold"><i class="fas fa-crown text-dark me-1"></i>VIP</span>',
            'business' => '<span class="badge bg-primary text-white fw-bold"><i class="fas fa-briefcase me-1"></i>Business</span>',
            default => '<span class="badge bg-secondary-subtle text-secondary border fw-medium"><i class="fas fa-chair me-1"></i>Economy</span>'
        };
    }

    /**
     * Effective Hotel Address.
     */
    public function getEffectiveHotelAddressAttribute(): ?string
    {
        return $this->hotel_address ?: $this->trip?->hotel_address;
    }

    /**
     * Effective Pickup / Boarding Location.
     */
    public function getEffectivePickupLocationAttribute(): string
    {
        if (!empty($this->pickup_location)) {
            return $this->pickup_location;
        }
        if ($this->shop && !empty($this->shop->name)) {
            return $this->shop->name;
        }
        return 'Office';
    }

    /**
     * Effective Drop-off Location / Destination Station.
     */
    public function getEffectiveDropoffLocationAttribute(): string
    {
        if (!empty($this->dropoff_location)) {
            return $this->dropoff_location;
        }
        return $this->trip ? ($this->trip->destination_city ?: 'Destination') : 'Destination';
    }

    /**
     * Effective Return Drop-off Location (e.g. midway stop or specific station on return trip).
     */
    public function getEffectiveReturnDropoffLocationAttribute(): string
    {
        if (!empty($this->return_dropoff_location)) {
            return $this->return_dropoff_location;
        }
        return $this->effective_pickup_location;
    }

    /**
     * Check if booking is one-way.
     */
    public function isOneWay(): bool
    {
        return $this->journey_type === 'one_way';
    }

    /**
     * Check if booking is round-trip (two way).
     */
    public function isRoundTrip(): bool
    {
        return $this->journey_type !== 'one_way';
    }

    /**
     * User-friendly journey type label.
     */
    public function journeyTypeLabel(): string
    {
        if ($this->isOneWay()) {
            $dir = $this->one_way_direction === 'return' ? __('Return Inbound (عودة)') : __('Outbound (ذهاب)');
            return __('One-Way (:dir)', ['dir' => $dir]);
        }
        return __('Two-Way / Round Trip (ذهاب وعودة)');
    }

    /**
     * Journey type badge HTML.
     */
    public function journeyTypeBadge(): string
    {
        if ($this->isOneWay()) {
            return '<span class="badge bg-info-subtle text-info border border-info-subtle fw-bold"><i class="fas fa-arrow-right me-1"></i>' . __('One Way (ذهاب فقط)') . '</span>';
        }
        return '<span class="badge bg-success-subtle text-success border border-success-subtle fw-bold"><i class="fas fa-exchange-alt me-1"></i>' . __('Round Trip (ذهاب وعودة)') . '</span>';
    }

    /**
     * Standard Room Types and their attributes.
     */
    public static function roomTypes(): array
    {
        return [
            // Standard / Sharing Rooms
            'quad_sharing' => [
                'label' => __('Quad Sharing (رباعي - 4 Beds)'),
                'category' => 'sharing',
                'multiplier' => 1.0,
                'surcharge' => 0,
            ],
            'triple_sharing' => [
                'label' => __('Triple Sharing (ثلاثي - 3 Beds)'),
                'category' => 'sharing',
                'multiplier' => 1.25,
                'surcharge' => 150,
            ],
            'double_sharing' => [
                'label' => __('Double Sharing (ثنائي - 2 Beds)'),
                'category' => 'sharing',
                'multiplier' => 1.5,
                'surcharge' => 300,
            ],

            // Private / Personal Rooms
            'single_private' => [
                'label' => __('Personal / Single Private Room (شخصي / مفرد)'),
                'category' => 'private',
                'multiplier' => 2.0,
                'surcharge' => 600,
            ],
            'private_room' => [
                'label' => __('Private Family Room (غرفة عائلية خاصة)'),
                'category' => 'private',
                'multiplier' => 2.2,
                'surcharge' => 700,
            ],

            // Luxury & VIP Rooms
            'luxury_haram_view' => [
                'label' => __('Luxury Haram View Room (إطلالة الحرم فاخرة)'),
                'category' => 'luxury',
                'multiplier' => 2.5,
                'surcharge' => 1000,
            ],
            'executive_suite' => [
                'label' => __('Executive Luxury Suite (جناح رئاسي / تنفيذي فاخر)'),
                'category' => 'luxury',
                'multiplier' => 3.0,
                'surcharge' => 1500,
            ],
            'custom' => [
                'label' => __('Custom Room / Rate (غرفة مخصصة / سعر يدوي)'),
                'category' => 'custom',
                'multiplier' => 1.0,
                'surcharge' => 0,
            ],
        ];
    }

    /**
     * User-friendly room type label.
     */
    public function roomTypeLabel(): string
    {
        $types = self::roomTypes();
        return $types[$this->room_type]['label'] ?? ucwords(str_replace('_', ' ', $this->room_type ?: 'quad_sharing'));
    }

    /**
     * Room type visual badge HTML.
     */
    public function roomTypeBadge(): string
    {
        $rt = $this->room_type ?: 'quad_sharing';
        $label = $this->roomTypeLabel();
        if (in_array($rt, ['luxury_haram_view', 'executive_suite'])) {
            return '<span class="badge bg-warning text-dark border border-warning-subtle fw-bold"><i class="fas fa-crown me-1 text-dark"></i>' . $label . '</span>';
        }
        if (in_array($rt, ['single_private', 'private_room'])) {
            return '<span class="badge bg-primary text-white fw-bold"><i class="fas fa-user-shield me-1"></i>' . $label . '</span>';
        }
        if ($rt === 'custom') {
            return '<span class="badge bg-secondary text-white fw-bold"><i class="fas fa-edit me-1"></i>' . $label . '</span>';
        }
        return '<span class="badge bg-light text-dark border fw-bold"><i class="fas fa-bed me-1 text-muted"></i>' . $label . '</span>';
    }

    /**
     * Payment badge class.
     */
    public function paymentBadgeClass(): string
    {
        return match ($this->payment_status) {
            'paid' => 'badge-soft-success',
            'partial' => 'badge-soft-warning',
            'unpaid' => 'badge-soft-danger',
            default => 'badge-soft-secondary',
        };
    }
}
