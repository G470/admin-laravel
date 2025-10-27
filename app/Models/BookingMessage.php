<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'user_id',
        'message',
        'is_vendor_message',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'is_vendor_message' => 'boolean',
    ];

    // Relationships
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeFromVendor($query)
    {
        return $query->where('is_vendor_message', true);
    }

    public function scopeFromCustomer($query)
    {
        return $query->where('is_vendor_message', false);
    }

    // Methods
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    public function isUnread()
    {
        return is_null($this->read_at);
    }
}
