<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\BelongsToShop;

class Department extends Model
{
    use BelongsToShop;

    protected $fillable = [
        'shop_id',
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function assignments(): HasMany
    {
        return $this->hasMany(JobAssignment::class);
    }

    public function getLocalizedNameAttribute(): string
    {
        return __($this->name) !== $this->name ? __($this->name) : tdb($this->name);
    }

    protected static function booted()
    {
        static::saved(function ($dept) {
            if (!empty($dept->name)) {
                \App\Services\TranslationService::warmCache($dept->name);
            }
        });
    }
}
