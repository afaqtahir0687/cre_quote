<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'quote_number', 'quote_date', 'due_date', 'reference', 'customer_id', 'employee_id', 'subtotal', 'total_tax', 'total_discount', 'shipping', 'grand_total', 'status', 'notes', 'images'
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}



