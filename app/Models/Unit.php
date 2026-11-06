<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToShop;

class Unit extends Model
{
    use HasFactory, SoftDeletes, BelongsToShop;

    protected $fillable = [
        'shop_id',
        'name',
        'code',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($unit) {
            if (!empty($unit->name)) {
                \App\Services\TranslationService::warmCache($unit->name);
            }
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getLocalizedNameAttribute(): string
    {
        return tdb($this->name);
    }
}

