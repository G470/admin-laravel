<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'rental_id',
        'type',
        'path',
        'original_name'
    ];

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }
}