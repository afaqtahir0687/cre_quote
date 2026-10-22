<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToShop;

class Setting extends Model
{
    use BelongsToShop;

    protected $fillable = [
        'shop_id',
        'company_name',
        'company_address',
        'company_city',
        'company_country',
        'company_phone',
        'company_email',
        'company_tax_id',
        'company_vat_no',
        'company_cr_no',
        'company_logo',
        'currency_symbol',
        'invoice_prefix',
        'quote_prefix',
    ];

    /**
     * Get the active setting for current shop.
     */
    public static function current(): ?self
    {
        return current_setting();
    }
}
