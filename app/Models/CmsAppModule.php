<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsAppModule extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon_svg',
        'icon_bg',
        'icon_color',
        'destination_url',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}

