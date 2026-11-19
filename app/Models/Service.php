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
        'name_ar',
        'sub_tag',
        'sub_tag_ar',
        'headline',
        'headline_ar',
        'badge_text',
        'badge_text_ar',
        'slug',
        'icon',
        'image',
        'description',
        'description_ar',
        'content',
        'content_ar',
        'features',
        'features_ar',
        'feature_1_title',
        'feature_1_text',
        'feature_1_icon',
        'feature_2_title',
        'feature_2_text',
        'feature_2_icon',
        'process_steps',
        'process_steps_ar',
        'why_choose_us',
        'starting_price',
        'price_unit',
        'cta_title',
        'cta_title_ar',
        'cta_subtitle',
        'cta_subtitle_ar',
        'meta_title',
        'meta_description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'features' => 'array',
        'features_ar' => 'array',
        'process_steps' => 'array',
        'process_steps_ar' => 'array',
        'why_choose_us' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'starting_price' => 'decimal:2',
    ];

    public function getEffectiveNameAttribute(): string
    {
        if (app()->getLocale() === 'ar' && !empty($this->name_ar)) {
            return $this->name_ar;
        }
        return $this->name ?? '';
    }

    public function getEffectiveSubTagAttribute(): ?string
    {
        if (app()->getLocale() === 'ar' && !empty($this->sub_tag_ar)) {
            return $this->sub_tag_ar;
        }
        return $this->sub_tag;
    }

    public function getEffectiveHeadlineAttribute(): ?string
    {
        if (app()->getLocale() === 'ar' && !empty($this->headline_ar)) {
            return $this->headline_ar;
        }
        return $this->headline;
    }

    public function getEffectiveBadgeTextAttribute(): ?string
    {
        if (app()->getLocale() === 'ar' && !empty($this->badge_text_ar)) {
            return $this->badge_text_ar;
        }
        return $this->badge_text;
    }

    public function getEffectiveDescriptionAttribute(): ?string
    {
        if (app()->getLocale() === 'ar' && !empty($this->description_ar)) {
            return $this->description_ar;
        }
        return $this->description;
    }

    public function getEffectiveContentAttribute(): ?string
    {
        if (app()->getLocale() === 'ar' && !empty($this->content_ar)) {
            return $this->content_ar;
        }
        return $this->content;
    }

    public function getEffectiveFeaturesAttribute(): array
    {
        if (app()->getLocale() === 'ar' && !empty($this->features_ar) && is_array($this->features_ar)) {
            return $this->features_ar;
        }
        return is_array($this->features) ? $this->features : [];
    }

    public function getEffectiveProcessStepsAttribute(): array
    {
        if (app()->getLocale() === 'ar' && !empty($this->process_steps_ar) && is_array($this->process_steps_ar)) {
            return $this->process_steps_ar;
        }
        return is_array($this->process_steps) ? $this->process_steps : [];
    }

    public function getEffectiveCtaTitleAttribute(): ?string
    {
        if (app()->getLocale() === 'ar' && !empty($this->cta_title_ar)) {
            return $this->cta_title_ar;
        }
        return $this->cta_title;
    }

    public function getEffectiveCtaSubtitleAttribute(): ?string
    {
        if (app()->getLocale() === 'ar' && !empty($this->cta_subtitle_ar)) {
            return $this->cta_subtitle_ar;
        }
        return $this->cta_subtitle;
    }

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
