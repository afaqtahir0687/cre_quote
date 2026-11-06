<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DynamicTranslation extends Model
{
    protected $fillable = [
        'hash',
        'source_text',
        'translated_text',
        'source_lang',
        'target_lang',
    ];
}

