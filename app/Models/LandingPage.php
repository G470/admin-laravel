<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    protected $fillable = [
        'slug',
        'category',
        'city',
        'translations',
        'image_url',
        'alt_text'
    ];

    protected $casts = [
        'translations' => 'array',
    ];
}
