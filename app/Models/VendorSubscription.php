<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class VendorSubscription extends Model
{
    protected $fillable = [
        'vendor_id',
        'status',
        'start_date',
        'end_date',
        'next_billing_date',
        'cancellation_deadline',
        'monthly_price',
        'rental_count',
        'category_count',
        'location_count',
        'pricing_breakdown'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'next_billing_date' => 'datetime',
        'cancellation_deadline' => 'datetime',
        'monthly_price' => 'decimal:2',
        'pricing_breakdown' => 'array',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function history(): HasMany
    {
        return $this->hasMany(VendorSubscriptionHistory::class, 'subscription_id');
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format((float) $this->monthly_price, 2, ',', '.') . ' €';
    }

    public function getVatAmountAttribute(): float
    {
        return $this->monthly_price * 0.19; // 19% VAT
    }

    public function getTotalWithVatAttribute(): float
    {
        return $this->monthly_price + $this->vat_amount;
    }

    public function getFormattedTotalWithVatAttribute(): string
    {
        return number_format($this->total_with_vat, 2, ',', '.') . ' €';
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->end_date->gt(Carbon::now());
    }

    public function canBeCancelled(): bool
    {
        return $this->isActive() && $this->cancellation_deadline->gt(Carbon::now());
    }
}