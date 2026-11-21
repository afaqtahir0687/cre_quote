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
        'travel_trip_id',
        'customer_id',
        'booking_reference',
        'passenger_name',
        'passenger_phone',
        'passenger_id_number',
        'passenger_gender',
        'passenger_nationality',
        'pickup_location',
        'salesman_name',
        'salesman_phone',
        'seat_number',
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
