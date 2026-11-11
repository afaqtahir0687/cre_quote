<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProposalRequest extends Model
{
    use HasFactory;

    protected $table = 'proposal_requests';

    protected $fillable = [
        'reference_no',
        'service',
        'customer_location',
        'campaign_goal',
        'monthly_budget',
        'starting_timeline',
        'website_url',
        'first_name',
        'last_name',
        'email',
        'phone',
        'ip_address',
        'status',
        'internal_notes',
        'notes_updated_at',
        'notes_updated_by',
    ];

    protected $casts = [
        'notes_updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'notes_updated_by');
    }

    /**
     * Generate unique reference number like PROP-2026-0042.
     */
    public static function generateReferenceNo(): string
    {
        $year = date('Y');
        $count = static::whereYear('created_at', $year)->count() + 1;
        do {
            $ref = sprintf('PROP-%s-%04d', $year, $count);
            $exists = static::where('reference_no', $ref)->exists();
            if ($exists) {
                $count++;
            }
        } while ($exists);

        return $ref;
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function statusBadgeClass(): string
    {
        return match (strtolower(str_replace(' ', '_', $this->status))) {
            'pending' => 'badge-soft-warning',
            'contacted' => 'badge-soft-info',
            'in_discussion' => 'badge-soft-primary',
            'proposal_sent' => 'badge-soft-purple',
            'converted' => 'badge-soft-success',
            'rejected' => 'badge-soft-danger',
            default => 'badge-soft-secondary',
        };
    }
}

