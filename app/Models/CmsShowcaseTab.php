<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsShowcaseTab extends Model
{
    protected $fillable = [
        'tab_key',
        'tab_label',
        'tab_icon',
        'badge_text',
        'title',
        'description',
        'highlights',
        'screenshot_path',
        'cta_text',
        'cta_url',
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

