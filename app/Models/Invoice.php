<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'invoice_number', 'invoice_date', 'due_date', 'customer_id', 'employee_id', 'subtotal', 'total_tax', 'total_discount', 'shipping', 'grand_total', 'amount_paid', 'balance_due', 'status', 'notes', 'terms', 'signature'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getAmountPaidAttribute($value)
    {
        if (strtolower($this->status) === 'cancelled') {
            return 0.00;
        }
        if (strtolower($this->status) === 'paid') {
            return $this->grand_total;
        }
        return $value;
    }

    public function getBalanceDueAttribute($value)
    {
        if (strtolower($this->status) === 'cancelled') {
            return 0.00;
        }
        if (strtolower($this->status) === 'paid') {
            return 0.00;
        }
        return $value;
    }
}



