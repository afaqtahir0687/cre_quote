<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobOrderPayment extends Model
{
    protected $fillable = [
        'job_order_id',
        'amount',
        'method',
        'reference',
        'received_at',
    ];

    protected $casts = ['received_at' => 'datetime'];

    public function jobOrder(): BelongsTo
    {
        return $this->belongsTo(JobOrder::class);
    }
}
