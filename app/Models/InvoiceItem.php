<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'description',
        'item_text',
        'unit_price',
        'quantity',
        'tax_percentage',
        'discount',
        'total'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
