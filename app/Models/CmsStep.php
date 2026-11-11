<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsStep extends Model
{
    protected $fillable = [
        'step_number',
        'title',
        'description',
        'badge_color',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'step_number' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}

