<?php

namespace App\Traits;

use App\Models\Shop;
use App\Scopes\ShopScope;

trait BelongsToShop
{
    /**
     * Boot the trait and register global scope & creating listener.
     */
    protected static function bootBelongsToShop(): void
    {
        static::addGlobalScope(new ShopScope);

        static::creating(function ($model) {
            if (empty($model->shop_id)) {
                $shopId = current_shop_id();
                if ($shopId) {
                    $model->shop_id = $shopId;
                }
            }
        });
    }

    /**
     * The shop this record belongs to.
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
