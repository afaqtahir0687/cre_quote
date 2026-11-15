<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessType extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'name_ar',
        'icon',
        'description',
        'description_ar',
        'default_modules',
        'default_departments',
        'sort_order',
        'is_active',
        'is_system',
    ];

    protected $casts = [
        'default_modules' => 'array',
        'default_departments' => 'array',
        'is_active' => 'boolean',
        'is_system' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Scope for active types.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered types.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Get shops associated with this business category.
     */
    public function shops()
    {
        return $this->hasMany(Shop::class, 'business_type', 'slug');
    }

    /**
     * Get localized name according to current app locale.
     */
    public function getLocalizedNameAttribute(): string
    {
        if (app()->getLocale() === 'ar' && !empty($this->name_ar)) {
            return $this->name_ar;
        }
        return $this->name;
    }

    /**
     * Get localized description according to current app locale.
     */
    public function getLocalizedDescriptionAttribute(): ?string
    {
        if (app()->getLocale() === 'ar' && !empty($this->description_ar)) {
            return $this->description_ar;
        }
        return $this->description;
    }
}
