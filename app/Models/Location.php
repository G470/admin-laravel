<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Location extends Model
{
    protected $fillable = [
        'vendor_id',
        'name',
        'street_address',
        'additional_address',
        'postal_code',
        'city',
        'country',
        'country_id',
        'phone',
        'description',
        'is_main',
        'is_active',
        'latitude',
        'longitude',
        'notification_email',
        'use_custom_notifications',
        'notification_updated_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_main' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
        'use_custom_notifications' => 'boolean',
        'notification_updated_at' => 'datetime',
    ];

    /**
     * Get the country that owns the location.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    /**
     * Get the rentals for the location.
     */
    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }

    /**
     * Get rentals that use this location as an additional location (many-to-many relationship)
     */
    public function additionalRentals()
    {
        return $this->belongsToMany(Rental::class, 'rental_locations');
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    /**
     * Get the opening hours for this location
     */
    public function openings()
    {
        return $this->hasMany(Opening::class);
    }

    /**
     * Get opening hours for a specific day
     */
    public function getOpeningForDay($dayOfWeek)
    {
        return $this->openings()->where('day_of_week', $dayOfWeek)->first();
    }

    /**
     * Check if location is currently open
     */
    public function isCurrentlyOpen()
    {
        return Opening::isCurrentlyOpen($this->id);
    }

    /**
     * Get today's opening hours
     */
    public function getTodaysHours()
    {
        return Opening::getTodaysHours($this->id);
    }

    /**
     * Get the effective notification email for this location
     */
    public function getEffectiveNotificationEmailAttribute(): ?string
    {
        if ($this->use_custom_notifications && $this->notification_email) {
            return $this->notification_email;
        }

        return $this->vendor->default_notification_email;
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

    /**
     * Get the contact details for this location
     */
    public function contactDetails()
    {
        return $this->hasOne(LocationContactDetail::class);
    }

    /**
     * Get effective contact details (location-specific or default)
     */
    public function getEffectiveContactDetailsAttribute(): array
    {
        $locationContact = $this->contactDetails;

        if ($locationContact && $locationContact->use_custom_contact_details) {
            return $locationContact->visible_contact_data;
        }

        $defaultContact = $this->vendor->defaultContactDetails;
        return $defaultContact ? $defaultContact->visible_contact_data : [];
    }
}