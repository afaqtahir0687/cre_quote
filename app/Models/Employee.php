<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'name',
        'designation',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
