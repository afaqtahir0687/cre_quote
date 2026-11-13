<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CmsPage extends Model
{
    use HasFactory;

    protected $table = 'cms_pages';

    protected $fillable = [
        'title',
        'slug',
        'subtitle',
        'content',
        'footer_column',
        'meta_title',
        'meta_description',
        'sort_order',
        'is_active',
        'is_system',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_system' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Scope active pages ordered.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
    }

    /**
     * Scope by footer column.
     */
    public function scopeForFooter($query, string $column)
    {
        return $query->active()->where('footer_column', $column);
    }

    /**
     * URL helper for frontend link.
     */
    public function getUrlAttribute(): string
    {
        return route('website.page', $this->slug);
    }

    /**
     * Boot model to auto-generate slug if missing.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($page) {
            if (empty($page->slug) && !empty($page->title)) {
                $page->slug = Str::slug($page->title);
            } else {
                $page->slug = Str::slug($page->slug);
            }
        });
    }
}

