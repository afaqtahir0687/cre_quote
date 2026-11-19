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
        'title_ar',
        'slug',
        'subtitle',
        'subtitle_ar',
        'content',
        'content_ar',
        'footer_column',
        'meta_title',
        'meta_title_ar',
        'meta_description',
        'meta_description_ar',
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
     * Effective localized Title (English or Arabic based on active locale).
     */
    public function getEffectiveTitleAttribute(): string
    {
        if (app()->getLocale() === 'ar' && !empty($this->title_ar)) {
            return $this->title_ar;
        }
        return $this->title ?? '';
    }

    /**
     * Effective localized Subtitle.
     */
    public function getEffectiveSubtitleAttribute(): ?string
    {
        if (app()->getLocale() === 'ar' && !empty($this->subtitle_ar)) {
            return $this->subtitle_ar;
        }
        return $this->subtitle;
    }

    /**
     * Effective localized Content / Body.
     */
    public function getEffectiveContentAttribute(): string
    {
        if (app()->getLocale() === 'ar' && !empty($this->content_ar)) {
            return $this->content_ar;
        }
        return $this->content ?? '';
    }

    /**
     * Effective localized Meta Title.
     */
    public function getEffectiveMetaTitleAttribute(): ?string
    {
        if (app()->getLocale() === 'ar' && !empty($this->meta_title_ar)) {
            return $this->meta_title_ar;
        }
        return $this->meta_title ?: $this->effective_title;
    }

    /**
     * Effective localized Meta Description.
     */
    public function getEffectiveMetaDescriptionAttribute(): ?string
    {
        if (app()->getLocale() === 'ar' && !empty($this->meta_description_ar)) {
            return $this->meta_description_ar;
        }
        return $this->meta_description ?: ($this->effective_subtitle ?: Str::limit(strip_tags($this->effective_content), 160));
    }

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
