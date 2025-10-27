<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badword extends Model
{
    use HasFactory;

    protected $fillable = [
        'word',
        'replacement',
        'status'
    ];

    protected $casts = [
        'status' => 'string'
    ];
}