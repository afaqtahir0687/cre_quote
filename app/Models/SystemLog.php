<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToShop;

class SystemLog extends Model
{
    use BelongsToShop;

    protected $fillable = [
        'shop_id',
        'user_id',
        'action',
        'model_type',
        'description',
    ];

    protected static function booted()
    {
        static::creating(function ($log) {
            if (empty($log->model_type)) {
                $log->model_type = 'System';
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
