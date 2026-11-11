<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsFeature extends Model
{
    protected $fillable = [
        'title',
        'description',
        'icon_class',
        'icon_color_class',
        'highlights',
        'link_url',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'highlights' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}

