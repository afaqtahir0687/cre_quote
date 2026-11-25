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
        'hotel_city',
        'room_type',
        'nights_count',
        'makkah_nights',
        'madinah_nights',
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
        'makkah_nights' => 'integer',
        'madinah_nights' => 'integer',
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
    /**
     * Standard Room categories for Travel & Umrah Hotel Accommodation.
     * 1- Share Room (غرفة مشتركة)
     * 2- Private Room (غرفة خاصة)
     * 3- Bridal Room (غرفة عروسين / جناح العرسان)
     */
    public static function roomTypes(): array
    {
        return [
            'share_room' => [
                'label' => __('Share Room (غرفة مشتركة)'),
                'category' => 'sharing',
                'multiplier' => 1.0,
                'surcharge' => 0,
            ],
            'private_room' => [
                'label' => __('Private Room (غرفة خاصة)'),
                'category' => 'private',
                'multiplier' => 1.0,
                'surcharge' => 0,
            ],
            'bridal_room' => [
                'label' => __('Bridal Room (غرفة عروسين / جناح العرسان)'),
                'category' => 'bridal',
                'multiplier' => 1.0,
                'surcharge' => 0,
            ],
        ];
    }

    /**
     * Standard Hotel cities / locations.
     */
    public static function hotelCities(): array
    {
        return [
            'Makkah' => __('Makkah Al-Mukarramah (مكة المكرمة)'),
            'Madinah' => __('Madinah Al-Munawwarah (المدينة المنورة)'),
            'Makkah & Madinah' => __('Both Holy Cities (مكة المكرمة والمدينة المنورة)'),
        ];
    }

    /**
     * User-friendly hotel city label.
     */
    public function hotelCityLabel(): string
    {
        $cities = self::hotelCities();
        return $cities[$this->hotel_city] ?? ($this->hotel_city ?: '—');
    }

    /**
     * Human-readable hotel nights and city stay distribution.
     * e.g. "5 Nights (3 Makkah + 2 Madinah)" or "3 Nights in Makkah"
     */
    public function hotelStaySummary(): string
    {
        $mk = (int) ($this->makkah_nights ?? 0);
        $md = (int) ($this->madinah_nights ?? 0);
        $total = (int) ($this->nights_count ?? 0);

        if ($mk > 0 && $md > 0) {
            return "{$total} " . __('Nights') . " ({$mk} " . __('Makkah') . " + {$md} " . __('Madinah') . ")";
        } elseif ($mk > 0) {
            return "{$mk} " . __('Nights') . " (" . __('Makkah') . ")";
        } elseif ($md > 0) {
            return "{$md} " . __('Nights') . " (" . __('Madinah') . ")";
        } elseif ($total > 0) {
            $city = $this->hotel_city ? ($this->hotelCities()[$this->hotel_city] ?? $this->hotel_city) : '';
            return $city ? "{$total} " . __('Nights') . " ({$city})" : "{$total} " . __('Nights');
        }

        return $this->hotelCityLabel() ?: '—';
    }

    /**
     * User-friendly room type label.
     */
    public function roomTypeLabel(): string
    {
        $types = self::roomTypes();
        if (isset($types[$this->room_type])) {
            return $types[$this->room_type]['label'];
        }
        return match($this->room_type) {
            'quad_sharing', 'triple_sharing', 'double_sharing' => __('Share Room (غرفة مشتركة)'),
            'single_private' => __('Private Room (غرفة خاصة)'),
            'bridal_room' => __('Bridal Room (غرفة عروسين / جناح العرسان)'),
            default => ucwords(str_replace('_', ' ', $this->room_type ?: 'share_room')),
        };
    }

    /**
     * Room type visual badge HTML.
     */
    public function roomTypeBadge(): string
    {
        $rt = $this->room_type ?: 'share_room';
        $label = $this->roomTypeLabel();
        if ($rt === 'bridal_room') {
            return '<span class="badge bg-warning text-dark border border-warning-subtle fw-bold"><i class="fas fa-gem me-1 text-danger"></i>' . $label . '</span>';
        }
        if (in_array($rt, ['private_room', 'single_private'])) {
            return '<span class="badge bg-primary text-white fw-bold"><i class="fas fa-user-shield me-1"></i>' . $label . '</span>';
        }
        return '<span class="badge bg-light text-primary border fw-bold"><i class="fas fa-users me-1"></i>' . $label . '</span>';
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
