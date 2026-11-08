<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToShop;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory, SoftDeletes, BelongsToShop;

    protected $fillable = [
        'shop_id',
        'name',
        'code',
        'slug',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($category) {
            if (empty($category->slug) && !empty($category->name)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::saved(function ($category) {
            if (!empty($category->name)) {
                \App\Services\TranslationService::warmCache($category->name);
            }
            if (!empty($category->description)) {
                \App\Services\TranslationService::warmCache($category->description);
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

