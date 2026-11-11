<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsSolution extends Model
{
    protected $fillable = [
        'tag',
        'title',
        'description',
        'checklist',
        'icon_class',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'checklist' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}

