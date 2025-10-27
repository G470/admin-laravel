<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorSubscriptionHistory extends Model
{
    protected $table = 'vendor_subscription_history';

    protected $fillable = [
        'vendor_id',
        'subscription_id',
        'action',
        'old_price',
        'new_price',
        'rental_count',
        'category_count',
        'location_count',
        'notes'
    ];

    protected $casts = [
        'old_price' => 'decimal:2',
        'new_price' => 'decimal:2',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(VendorSubscription::class, 'subscription_id');
    }
}