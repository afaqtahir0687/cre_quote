<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TravelTrip extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'shop_id',
        'trip_code',
        'title',
        'description',
        'bus_name',
        'bus_number',
        'total_seats',
        'departure_city',
        'destination_city',
        'departure_date',
        'return_date',
        'duration_days',
        'price_without_hotel',
        'price_with_hotel',
        'hotel_name',
        'hotel_rating',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'departure_date' => 'datetime',
        'return_date' => 'datetime',
        'duration_days' => 'integer',
        'total_seats' => 'integer',
        'price_without_hotel' => 'decimal:2',
        'price_with_hotel' => 'decimal:2',
    ];

    /**
     * Shop this trip belongs to.
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * User who created this trip.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * All passenger bookings for this trip.
     */
    public function bookings()
    {
        return $this->hasMany(TravelBooking::class);
    }

    /**
     * Confirmed passenger bookings.
     */
    public function confirmedBookings()
    {
        return $this->hasMany(TravelBooking::class)->where('booking_status', 'confirmed');
    }

    /**
     * Array of all booked seat numbers for this trip.
     */
    public function bookedSeatNumbers(): array
    {
        return $this->confirmedBookings()->pluck('seat_number')->map(fn($s) => (int)$s)->all();
    }

    /**
     * Check if a specific seat is already booked.
     */
    public function isSeatBooked(int $seatNumber): bool
    {
        return in_array($seatNumber, $this->bookedSeatNumbers(), true);
    }

    /**
     * Get booking for a specific seat number.
     */
    public function getBookingForSeat(int $seatNumber): ?TravelBooking
    {
        return $this->confirmedBookings()->where('seat_number', $seatNumber)->first();
    }

    /**
     * Total booked seats count.
     */
    public function bookedSeatsCount(): int
    {
        return $this->confirmedBookings()->count();
    }

    /**
     * Total available seats remaining.
     */
    public function availableSeatsCount(): int
    {
        return max(0, (int)$this->total_seats - $this->bookedSeatsCount());
    }

    /**
     * Check if bus is completely sold out.
     */
    public function isSoldOut(): bool
    {
        return $this->availableSeatsCount() <= 0;
    }

    /**
     * Total revenue collected for this trip.
     */
    public function totalRevenue(): float
    {
        return (float) $this->confirmedBookings()->sum('total_amount');
    }

    /**
     * Scope trips by active shop.
     */
    public function scopeForCurrentShop($query)
    {
        $shopId = current_shop_id();
        if ($shopId) {
            return $query->where('shop_id', $shopId);
        }
        return $query;
    }

    /**
     * Generate unique trip code.
     */
    public static function generateTripCode(int $shopId): string
    {
        $count = self::where('shop_id', $shopId)->count() + 1;
        return 'TRIP-' . date('Y') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }
}
