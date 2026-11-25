<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToShop;

class Expense extends Model
{
    use BelongsToShop;

    protected $fillable = [
        'shop_id',
        'user_id',
        'employee_id',
        'expense_date',
        'category',
        'title',
        'amount',
        'payment_method',
        'reference_no',
        'attachment',
        'notes',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    /**
     * Office / Branch (Shop).
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * User who created / recorded the expense.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Staff / Employee associated with the expense.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Standard expense categories dictionary.
     */
    public static function categories(): array
    {
        return [
            'tea_refreshment'      => __('Tea & Refreshment'),
            'office_supplies'      => __('Office Supplies & Stationery'),
            'utilities_bills'      => __('Utilities & Bills (Electricity/Net)'),
            'travel_transport'     => __('Travel & Fuel'),
            'maintenance_cleaning' => __('Maintenance & Cleaning'),
            'rent'                 => __('Office Rent'),
            'food_meals'           => __('Food & Staff Meals'),
            'courier_postal'       => __('Courier & Postal'),
            'other'                => __('Other Expense'),
        ];
    }

    public function categoryLabel(): string
    {
        return self::categories()[$this->category] ?? ucfirst(str_replace('_', ' ', $this->category));
    }

    public function categoryBadge(): string
    {
        $colors = [
            'tea_refreshment'      => ['bg' => 'bg-warning-subtle', 'text' => 'text-warning-emphasis', 'icon' => 'fas fa-mug-hot'],
            'office_supplies'      => ['bg' => 'bg-info-subtle', 'text' => 'text-info-emphasis', 'icon' => 'fas fa-pencil-ruler'],
            'utilities_bills'      => ['bg' => 'bg-danger-subtle', 'text' => 'text-danger-emphasis', 'icon' => 'fas fa-bolt'],
            'travel_transport'     => ['bg' => 'bg-primary-subtle', 'text' => 'text-primary-emphasis', 'icon' => 'fas fa-car'],
            'maintenance_cleaning' => ['bg' => 'bg-secondary-subtle', 'text' => 'text-secondary-emphasis', 'icon' => 'fas fa-tools'],
            'rent'                 => ['bg' => 'bg-dark-subtle', 'text' => 'text-dark-emphasis', 'icon' => 'fas fa-building'],
            'food_meals'           => ['bg' => 'bg-success-subtle', 'text' => 'text-success-emphasis', 'icon' => 'fas fa-utensils'],
            'courier_postal'       => ['bg' => 'bg-info-subtle', 'text' => 'text-info', 'icon' => 'fas fa-envelope'],
            'other'                => ['bg' => 'bg-light text-dark border', 'text' => '', 'icon' => 'fas fa-receipt'],
        ];

        $cfg = $colors[$this->category] ?? ['bg' => 'bg-light text-dark border', 'text' => '', 'icon' => 'fas fa-receipt'];
        $label = e($this->categoryLabel());

        return '<span class="badge ' . $cfg['bg'] . ' ' . $cfg['text'] . ' px-2 py-1"><i class="' . $cfg['icon'] . ' me-1"></i>' . $label . '</span>';
    }

    /**
     * Standard payment methods dictionary.
     */
    public static function paymentMethods(): array
    {
        return [
            'cash'          => __('Cash'),
            'bank_transfer' => __('Bank Transfer'),
            'card'          => __('Card'),
            'online'        => __('Online / Wallet'),
        ];
    }

    public function paymentMethodLabel(): string
    {
        return self::paymentMethods()[$this->payment_method] ?? ucfirst($this->payment_method);
    }
}
