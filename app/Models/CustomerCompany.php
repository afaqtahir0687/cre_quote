<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerCompany extends Model
{
    protected $fillable = [
        'customer_id',
        'company_name',
        'phone',
        'vat_no',
        'cr_no',
        'address',
        'city',
        'zip',
        'country',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * The customer that owns this company / branch.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
