<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobAssignment extends Model
{
    protected $fillable = [
        'job_order_id',
        'department_id',
        'department_name',
        'employee_id',
        'assigned_to_name',
        'instructions',
        'due_date',
        'status',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function jobOrder(): BelongsTo
    {
        return $this->belongsTo(JobOrder::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getAssignedDisplayNameAttribute(): string
    {
        if ($this->employee) {
            return $this->employee->name;
        }
        return $this->assigned_to_name ?? 'Unassigned';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'completed' => 'badge-soft-success',
            'in_progress' => 'badge-soft-primary',
            'cancelled' => 'badge-soft-danger',
            default => 'badge-soft-warning',
        };
    }
}
