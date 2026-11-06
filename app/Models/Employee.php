<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToShop;

class Employee extends Model
{
    use BelongsToShop;

    protected $fillable = [
        'shop_id',
        'name',
        'designation',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::saved(function ($emp) {
            if (!empty($emp->name)) {
                \App\Services\TranslationService::warmCache($emp->name);
            }
            if (!empty($emp->designation)) {
                \App\Services\TranslationService::warmCache($emp->designation);
            }
        });
    }
}
