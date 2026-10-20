<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteItem extends Model
{
    protected $fillable = [
        'quote_id', 'description', 'item_text', 'unit_price', 'quantity', 'tax_percentage', 'discount', 'total', 'images'
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }
}
