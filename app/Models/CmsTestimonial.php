<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsTestimonial extends Model
{
    protected $fillable = [
        'name',
        'designation',
        'company',
        'avatar',
        'content',
        'rating',
        'is_approved',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_approved', true)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }
}

