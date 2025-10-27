<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'customer_name',
        'customer_email',
        'amount',
        'status',
        'due_date',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending' => 'Ausstehend',
            'paid' => 'Bezahlt',
            'overdue' => 'Überfällig',
            'cancelled' => 'Storniert',
            default => 'Unbekannt'
        };
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'pending' => 'warning',
            'paid' => 'success',
            'overdue' => 'danger',
            'cancelled' => 'secondary',
            default => 'primary'
        };
    }
}