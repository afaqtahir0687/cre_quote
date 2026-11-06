<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToShop;

class Quote extends Model
{
    use SoftDeletes, BelongsToShop;

    protected $fillable = [
        'shop_id', 'quote_number', 'quote_date', 'due_date', 'reference', 'customer_id', 'customer_company_id', 'show_customer_name', 'employee_id', 'subtotal', 'total_tax', 'total_discount', 'shipping', 'grand_total', 'status', 'notes', 'terms', 'images'
    ];

    protected $casts = [
        'images' => 'array',
        'show_customer_name' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function customerCompany()
    {
        return $this->belongsTo(CustomerCompany::class, 'customer_company_id');
    }

    public function getBillingCompanyAttribute()
    {
        return $this->customerCompany ?: ($this->customer?->defaultCompany());
    }

    public function items()
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getLocalizedNotesAttribute(): string
    {
        return tdb($this->notes);
    }

    public function getLocalizedTermsAttribute(): string
    {
        return tdb($this->terms);
    }

    protected static function booted()
    {
        static::saved(function ($quote) {
            if (!empty($quote->notes)) {
                \App\Services\TranslationService::warmCache($quote->notes);
            }
            if (!empty($quote->terms)) {
                \App\Services\TranslationService::warmCache($quote->terms);
            }
        });
    }
}
