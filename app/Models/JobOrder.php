<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobOrder extends Model
{
    use SoftDeletes, \App\Traits\BelongsToShop;
    protected $fillable = [
        'shop_id',
        'order_number',
        'quote_id',
        'customer_id',
        'employee_id',
        'updated_by',
        'order_date',
        'status',
        'notes',
        'subtotal',
        'total_tax',
        'total_discount',
        'shipping',
        'grand_total',
        'total_amount',
        'paid_amount',
        'payment_status',
        'payment_details',
        'invoice_id',
    ];

    protected $casts = [
        'order_date' => 'date',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'payment_details' => 'array',
    ];

    /**
     * The quote this job order originates from.
     */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    /**
     * The customer for the job order.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Items belonging to the job order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(JobOrderItem::class);
    }

    // Payments made for this job order
    public function payments()
    {
        return $this->hasMany(JobOrderPayment::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Department work assignments for this job order.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(JobAssignment::class);
    }

    // Calculated attributes
    public function getTotalAmountAttribute($value)
    {
        if ($this->status === 'cancelled') {
            return 0.00;
        }
        return $this->grand_total ?? $value ?? $this->items->sum('total');
    }

    public function getPaidAmountAttribute($value)
    {
        if ($this->status === 'cancelled') {
            return 0.00;
        }
        if ($this->invoice_id && $this->invoice) {
            return (float) $this->invoice->amount_paid;
        }
        return (float) $this->payments()->sum('amount');
    }

    public function getRemainingBalanceAttribute()
    {
        if ($this->status === 'cancelled') {
            return 0.00;
        }
        if ($this->invoice_id && $this->invoice) {
            return (float) $this->invoice->balance_due;
        }
        $total = ($this->grand_total !== null && (float)$this->grand_total > 0) ? (float)$this->grand_total : (float)($this->total_amount ?? 0);
        return max(0, $total - (float)$this->paid_amount);
    }

    public function getPaymentStatusLabelAttribute()
    {
        if ($this->status === 'cancelled') {
            return 'Cancelled';
        }
        if ($this->remaining_balance <= 0 && ($this->grand_total ?? $this->total_amount) > 0) {
            return 'Paid';
        }
        return ucfirst($this->payment_status);
    }
}
?>




