<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProposalOption extends Model
{
    use HasFactory;

    protected $table = 'proposal_options';

    protected $fillable = [
        'type',
        'name',
        'subtitle',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order', 'asc');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}

