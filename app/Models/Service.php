<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory;

    protected $table = 'services';

    protected $fillable = [
        'shop_id',
        'category',
        'name',
        'sub_tag',
        'headline',
        'badge_text',
        'slug',
        'icon',
        'image',
        'description',
        'content',
        'features',
        'feature_1_title',
        'feature_1_text',
        'feature_1_icon',
        'feature_2_title',
        'feature_2_text',
        'feature_2_icon',
        'process_steps',
        'why_choose_us',
        'starting_price',
        'price_unit',
        'cta_title',
        'cta_subtitle',
        'meta_title',
        'meta_description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'features' => 'array',
        'process_steps' => 'array',
        'why_choose_us' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'starting_price' => 'decimal:2',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($service) {
            if (empty($service->slug)) {
                $service->slug = Str::slug($service->name);
            }
        });
    }
}

