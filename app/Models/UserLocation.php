<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserLocation extends Model
{
    protected $fillable = [
        'user_id',
        'master_location_id',
        'street',
        'house_number',
        'address_line_2',
        'label',
        'is_primary',
        'is_active',
        'notification_email',
        'use_custom_notifications',
        'notification_updated_at',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
        'use_custom_notifications' => 'boolean',
        'notification_updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns this location
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the master location data (city, postcode, etc.)
     */
    public function masterLocation(): BelongsTo
    {
        return $this->belongsTo(MasterLocation::class);
    }

    /**
     * Get full address string
     */
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            trim($this->street . ' ' . $this->house_number),
            $this->address_line_2,
            $this->masterLocation?->postcode . ' ' . $this->masterLocation?->city,
            $this->masterLocation?->country ? strtoupper($this->masterLocation->country) : null
        ]);

        return implode(', ', $parts);
    }

    /**
     * Get display name for this location
     */
    public function getDisplayNameAttribute()
    {
        if ($this->label) {
            return $this->label;
        }

        if ($this->masterLocation) {
            return $this->masterLocation->city . ', ' . $this->masterLocation->postcode;
        }

        return 'Standort #' . $this->id;
    }

    /**
     * Scope for active locations
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for primary location
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Get the effective notification email for this location
     */
    public function getEffectiveNotificationEmailAttribute(): ?string
    {
        if ($this->use_custom_notifications && $this->notification_email) {
            return $this->notification_email;
        }
        
        return $this->user->default_notification_email;
    }

    /**
     * Update notification settings for this location
     */
    public function updateNotificationSettings(string $email = null, bool $useCustom = false): bool
    {
        return $this->update([
            'notification_email' => $useCustom ? $email : null,
            'use_custom_notifications' => $useCustom,
            'notification_updated_at' => now(),
        ]);
    }
}
