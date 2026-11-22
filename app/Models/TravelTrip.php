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
        'employee_id',
        'trip_code',
        'title',
        'description',
        'bus_name',
        'bus_number',
        'driver_1_name',
        'driver_1_nationality',
        'driver_1_phone',
        'driver_2_name',
        'driver_2_nationality',
        'driver_2_phone',
        'total_seats',
        'vip_seats_count',
        'business_seats_count',
        'economy_seats_count',
        'vip_seat_numbers',
        'business_seat_numbers',
        'economy_seat_numbers',
        'departure_city',
        'destination_city',
        'departure_date',
        'return_date',
        'duration_days',
        'price_without_hotel',
        'one_way_price',
        'price_with_hotel',
        'vip_price',
        'business_price',
        'economy_price',
        'hotel_name',
        'hotel_address',
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
        'vip_seats_count' => 'integer',
        'business_seats_count' => 'integer',
        'economy_seats_count' => 'integer',
        'vip_seat_numbers' => 'array',
        'business_seat_numbers' => 'array',
        'economy_seat_numbers' => 'array',
        'price_without_hotel' => 'decimal:2',
        'one_way_price' => 'decimal:2',
        'price_with_hotel' => 'decimal:2',
        'vip_price' => 'decimal:2',
        'business_price' => 'decimal:2',
        'economy_price' => 'decimal:2',
    ];

    /**
     * Shop this trip belongs to.
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Assigned Employee for this trip.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
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
     * Get the seat class for a specific seat number ('vip', 'business', or 'economy').
     */
    public function getSeatClass(int $seatNumber): string
    {
        if (!empty($this->vip_seat_numbers) && is_array($this->vip_seat_numbers) && in_array($seatNumber, $this->vip_seat_numbers)) {
            return 'vip';
        }
        if (!empty($this->business_seat_numbers) && is_array($this->business_seat_numbers) && in_array($seatNumber, $this->business_seat_numbers)) {
            return 'business';
        }
        if (!empty($this->economy_seat_numbers) && is_array($this->economy_seat_numbers) && in_array($seatNumber, $this->economy_seat_numbers)) {
            return 'economy';
        }

        $vipCount = (int) ($this->vip_seats_count ?? 10);
        $bizCount = (int) ($this->business_seats_count ?? 15);

        if ($vipCount > 0 && $seatNumber <= $vipCount) {
            return 'vip';
        }
        if ($bizCount > 0 && $seatNumber <= ($vipCount + $bizCount)) {
            return 'business';
        }

        return 'economy';
    }

    /**
     * Get the specific seat fare for a given seat number.
     */
    public function getSeatFare(int $seatNumber): float
    {
        $class = $this->getSeatClass($seatNumber);
        if ($class === 'vip' && $this->vip_price !== null && (float)$this->vip_price > 0) {
            return (float) $this->vip_price;
        }
        if ($class === 'business' && $this->business_price !== null && (float)$this->business_price > 0) {
            return (float) $this->business_price;
        }
        if ($class === 'economy' && $this->economy_price !== null && (float)$this->economy_price > 0) {
            return (float) $this->economy_price;
        }

        return (float) $this->price_without_hotel;
    }

    /**
     * Get formatted seat class display label.
     */
    public static function seatClassLabel(string $class): string
    {
        return match(strtolower($class)) {
            'vip' => __('VIP Class (درجة كبار الشخصيات)'),
            'business' => __('Business Class (درجة رجال الأعمال)'),
            default => __('Economy Class (الدرجة السياحية)')
        };
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
     * Get booking summary breakdown grouped by branch/shop.
     */
    public function branchBreakdown()
    {
        return $this->confirmedBookings()
            ->with('shop')
            ->select('shop_id', \Illuminate\Support\Facades\DB::raw('count(*) as total_seats'), \Illuminate\Support\Facades\DB::raw('sum(total_amount) as total_revenue'), \Illuminate\Support\Facades\DB::raw('sum(paid_amount) as total_paid'), \Illuminate\Support\Facades\DB::raw('sum(balance_due) as total_due'))
            ->groupBy('shop_id')
            ->get();
    }

    /**
     * Scope trips by active shop and sister branches of the same organization.
     */
    public function scopeForCurrentShop($query)
    {
        $shop = current_shop();
        if ($shop) {
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
     * Generate unique trip code.
     */
    public static function generateTripCode(int $shopId): string
    {
        $count = self::where('shop_id', $shopId)->count() + 1;
        return 'TRIP-' . date('Y') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Standard list of Saudi Arabian cities with Riyadh strictly first.
     */
    public static function saudiCities(): array
    {
        return [
            'Riyadh' => 'Riyadh (الرياض)',
            'Jeddah' => 'Jeddah (جدة)',
            'Makkah' => 'Makkah Al-Mukarramah (مكة المكرمة)',
            'Madinah' => 'Madinah Al-Munawwarah (المدينة المنورة)',
            'Dammam' => 'Dammam (الدمام)',
            'Al-Khobar' => 'Al-Khobar (الخبر)',
            'Dhahran' => 'Dhahran (الظهران)',
            'Al-Ahsa' => 'Al-Ahsa / Hofuf (الأحساء / الهفوف)',
            'Taif' => 'Taif (الطائف)',
            'Tabuk' => 'Tabuk (تبوك)',
            'Buraydah' => 'Buraydah (بريدة)',
            'Unaizah' => 'Unaizah (عنيزة)',
            'Abha' => 'Abha (أبها)',
            'Khamis Mushait' => 'Khamis Mushait (خميس مشيط)',
            'Al-Jubail' => 'Al-Jubail (الجبيل)',
            'Yanbu' => 'Yanbu (ينبع)',
            'Hail' => 'Hail (حائل)',
            'Hafar Al-Batin' => 'Hafar Al-Batin (حفر الباطن)',
            'Najran' => 'Najran (نجران)',
            'Jizan' => 'Jizan (جازان)',
            'Al-Qatif' => 'Al-Qatif (القطيف)',
            'Sakaka' => 'Sakaka (سكاكا)',
            'Arar' => 'Arar (عرعر)',
            'Bisha' => 'Bisha (بيشة)',
            'Al-Bahah' => 'Al-Bahah (الباحة)',
        ];
    }

    /**
     * Primary destination cities for Umrah and Bus Trips.
     */
    public static function destinationCities(): array
    {
        return [
            'Makkah' => 'Makkah Al-Mukarramah (مكة المكرمة)',
            'Madinah' => 'Madinah Al-Munawwarah (المدينة المنورة)',
            'Makkah & Madinah' => 'Makkah & Madinah (مكة والمدينة)',
            'Jeddah' => 'Jeddah (جدة)',
            'Riyadh' => 'Riyadh (الرياض)',
            'Taif' => 'Taif (الطائف)',
            'Yanbu' => 'Yanbu (ينبع)',
            'Al-Ula' => 'Al-Ula (العلا)',
            'Dammam' => 'Dammam (الدمام)',
            'Tabuk' => 'Tabuk (تبوك)',
            'Abha' => 'Abha (أبها)',
        ];
    }

    /**
     * Standard trip duration packages in days.
     */
    public static function commonDurations(): array
    {
        return [
            1 => '1 Day (رحلة يوم واحد)',
            2 => '2 Days (يومان)',
            3 => '3 Days (3 أيام)',
            4 => '4 Days (4 أيام)',
            5 => '5 Days (5 أيام)',
            7 => '7 Days (أسبوع واحد)',
            10 => '10 Days (10 أيام)',
            14 => '14 Days (أسبوعان)',
            15 => '15 Days (15 يوماً)',
            20 => '20 Days (20 يوماً)',
            21 => '21 Days (3 أسابيع)',
            25 => '25 Days (25 يوماً)',
            28 => '28 Days (4 أسابيع)',
            30 => '30 Days (شهر كامل)',
            40 => '40 Days (40 يوماً)',
        ];
    }

    /**
     * Dynamic departure cities (merges preset cities with any existing past values).
     */
    public static function dynamicDepartureCities(): array
    {
        $cities = self::saudiCities();
        try {
            $existing = self::distinct()->whereNotNull('departure_city')->pluck('departure_city')->all();
            foreach ($existing as $c) {
                $trimmed = trim($c);
                if (!empty($trimmed) && !isset($cities[$trimmed])) {
                    $cities[$trimmed] = $trimmed;
                }
            }
        } catch (\Throwable $e) {
            // Graceful fallback
        }
        return $cities;
    }

    /**
     * Dynamic destination cities (merges preset destinations with any existing past values).
     */
    public static function dynamicDestinationCities(): array
    {
        $cities = self::destinationCities();
        try {
            $existing = self::distinct()->whereNotNull('destination_city')->pluck('destination_city')->all();
            foreach ($existing as $c) {
                $trimmed = trim($c);
                if (!empty($trimmed) && !isset($cities[$trimmed])) {
                    $cities[$trimmed] = $trimmed;
                }
            }
        } catch (\Throwable $e) {
            // Graceful fallback
        }
        return $cities;
    }

    /**
     * Dynamic durations (merges preset common durations with any existing database durations).
     */
    public static function dynamicDurations(): array
    {
        $durations = self::commonDurations();
        try {
            $existing = self::distinct()->whereNotNull('duration_days')->pluck('duration_days')->all();
            foreach ($existing as $d) {
                $d = (int) $d;
                if ($d > 0 && !isset($durations[$d])) {
                    $durations[$d] = "{$d} Days ({$d} يوماً)";
                }
            }
            ksort($durations);
        } catch (\Throwable $e) {
            // Graceful fallback
        }
        return $durations;
    }
}
