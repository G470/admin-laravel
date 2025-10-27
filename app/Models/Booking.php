<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'renter_id',
        'rental_id',
        'status',
        'total_amount',
        'commission_amount',
        'start_date',
        'end_date',
        'rental_type',
        'message',
        'vendor_notes',
        'booking_token',
        'total_price',
        'guest_email',
        'guest_name',
        'guest_phone',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($booking) {
            if (!$booking->booking_token) {
                $booking->booking_token = Str::random(32);
            }
        });
    }

    // Relationships
    public function renter()
    {
        return $this->belongsTo(User::class, 'renter_id');
    }

    public function rental()
    {
        return $this->belongsTo(Rental::class, 'rental_id');
    }

    public function vendor()
    {
        return $this->hasOneThrough(User::class, Rental::class, 'id', 'id', 'rental_id', 'vendor_id');
    }

    public function messages()
    {
        return $this->hasMany(BookingMessage::class);
    }

    // Status methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isConfirmed()
    {
        return $this->status === 'confirmed';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    public function canBeConfirmed()
    {
        return $this->status === 'pending';
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Ausstehend',
            'confirmed' => 'Bestätigt',
            'cancelled' => 'Storniert',
            'completed' => 'Abgeschlossen',
            default => 'Unbekannt'
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'confirmed' => 'success',
            'cancelled' => 'danger',
            'completed' => 'info',
            default => 'secondary'
        };
    }

    public function getBookingUrlAttribute()
    {
        return route('booking.token', $this->booking_token);
    }
}