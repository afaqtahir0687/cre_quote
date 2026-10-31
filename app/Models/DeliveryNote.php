<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToShop;

class DeliveryNote extends Model
{
    use BelongsToShop;

    protected $fillable = [
        'shop_id',
        'delivery_number',
        'delivery_date',
        'invoice_id',
        'customer_id',
        'customer_company_id',
        'show_customer_name',
        'employee_id',
        'delivery_address',
        'notes',
        'status',
    ];

    protected $casts = [
        'show_customer_name' => 'boolean',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

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

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
