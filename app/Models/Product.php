<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToShop;

class Product extends Model
{
    use BelongsToShop;

    protected $fillable = [
        'shop_id', 'name', 'sku', 'description', 'price', 'tax_percentage', 'stock', 'type'
    ];
}
