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
        'company_stamp',
        'bank_stamp_logo',
        'use_custom_bank_image',
        'bank_name',
        'bank_name_ar',
        'bank_account_name',
        'bank_account_no',
        'bank_iban',
        'bank_swift',
        'currency_symbol',
        'invoice_prefix',
        'quote_prefix',
        'show_customer_name_on_pdf',
    ];

    protected $casts = [
        'show_customer_name_on_pdf' => 'boolean',
        'use_custom_bank_image' => 'boolean',
    ];

    /**
     * Get the active setting for current shop.
     */
    public static function current(): ?self
    {
        return current_setting();
    }
}
