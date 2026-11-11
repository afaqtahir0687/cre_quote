<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomepageSlide extends Model
{
    use HasFactory;

    protected $table = 'homepage_slides';

    protected $fillable = [
        'title',
        'badge_text',
        'heading',
        'heading_highlight',
        'subheading',
        'description',
        'primary_btn_text',
        'primary_btn_action',
        'primary_btn_url',
        'secondary_btn_text',
        'secondary_btn_url',
        'bg_image',
        'bg_image_mobile',
        'fg_image',
        'bg_color',
        'text_align',
        'content_position',
        'slide_type',
        'animation_type',
        'display_duration',
        'trust_bullets',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'trust_bullets' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'display_duration' => 'integer',
    ];

    /**
     * Scope for active slides ordered by sort_order.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
    }

    /**
     * Check if slide is the existing ERP preview hero slide.
     */
    public function isExistingHero(): bool
    {
        return $this->slide_type === 'existing_hero';
    }

    /**
     * Check if button triggers the proposal modal.
     */
    public function opensProposalModal(): bool
    {
        return $this->primary_btn_action === 'open_proposal_modal';
    }
}

