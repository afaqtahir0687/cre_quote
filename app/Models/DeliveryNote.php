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
        'employee_id',
        'delivery_address',
        'notes',
        'status',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
