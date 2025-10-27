<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'billing_cycle',
        'trial_days',
        'features',
        'status',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'price' => 'decimal:2',
        'trial_days' => 'integer',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format((float) $this->price, 2, ',', '.') . ' €';
    }

    /**
     * Get the billing cycle display text
     */
    public function getBillingCycleTextAttribute(): string
    {
        return match ($this->billing_cycle) {
            'monthly' => 'Monatlich',
            'quarterly' => 'Vierteljährlich',
            'annually' => 'Jährlich',
            default => $this->billing_cycle,
        };
    }

    /**
     * Scope a query to only include active subscription plans.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include featured subscription plans.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
