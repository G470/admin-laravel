<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RentalPushCreditTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'rental_push_id',
        'vendor_id',
        'credits_used',
        'push_executed_at',
        'next_push_at',
        'transaction_reference'
    ];

    protected $casts = [
        'push_executed_at' => 'datetime',
        'next_push_at' => 'datetime',
        'credits_used' => 'integer'
    ];

    // Relationships
    public function rentalPush()
    {
        return $this->belongsTo(RentalPush::class);
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    // Scopes
    public function scopeForVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    public function scopeForRentalPush($query, $rentalPushId)
    {
        return $query->where('rental_push_id', $rentalPushId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('push_executed_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('push_executed_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('push_executed_at', now()->month)
            ->whereYear('push_executed_at', now()->year);
    }

    // Static methods
    public static function getTotalCreditsUsedByVendor($vendorId, $period = null)
    {
        $query = static::forVendor($vendorId);

        if ($period === 'today') {
            $query->today();
        } elseif ($period === 'week') {
            $query->thisWeek();
        } elseif ($period === 'month') {
            $query->thisMonth();
        }

        return $query->sum('credits_used');
    }

    public static function getVendorTransactionHistory($vendorId, $limit = 20)
    {
        return static::forVendor($vendorId)
            ->with(['rentalPush.rental', 'rentalPush.category', 'rentalPush.location'])
            ->orderBy('push_executed_at', 'desc')
            ->limit($limit)
            ->get();
    }
}