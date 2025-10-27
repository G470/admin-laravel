<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocationContactDetail extends Model
{
    protected $fillable = [
        'location_id',
        'use_custom_contact_details',
        'company_name',
        'salutation',
        'first_name',
        'last_name',
        'street',
        'house_number',
        'postal_code',
        'city',
        'country_id',
        'phone',
        'mobile',
        'whatsapp',
        'website',
        'show_company_name',
        'show_salutation',
        'show_first_name',
        'show_last_name',
        'show_street',
        'show_house_number',
        'show_postal_code',
        'show_city',
        'show_country',
        'show_phone',
        'show_mobile',
        'show_whatsapp',
        'show_website',
        'contact_updated_at',
    ];

    protected $casts = [
        'use_custom_contact_details' => 'boolean',
        'show_company_name' => 'boolean',
        'show_salutation' => 'boolean',
        'show_first_name' => 'boolean',
        'show_last_name' => 'boolean',
        'show_street' => 'boolean',
        'show_house_number' => 'boolean',
        'show_postal_code' => 'boolean',
        'show_city' => 'boolean',
        'show_country' => 'boolean',
        'show_phone' => 'boolean',
        'show_mobile' => 'boolean',
        'show_whatsapp' => 'boolean',
        'show_website' => 'boolean',
        'contact_updated_at' => 'datetime',
    ];

    /**
     * Get the location that owns the contact details
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get the country for the contact details
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get effective contact details (custom or default)
     */
    public function getEffectiveContactDetailsAttribute(): array
    {
        if (!$this->use_custom_contact_details) {
            $defaultDetails = $this->location->vendor->defaultContactDetails;
            return $defaultDetails ? $defaultDetails->visible_contact_data : [];
        }

        return $this->getVisibleContactDataAttribute();
    }

    /**
     * Get visible contact fields for frontend display
     */
    public function getVisibleContactDataAttribute(): array
    {
        $fields = [
            'company_name' => $this->company_name,
            'salutation' => $this->salutation,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'street' => $this->street,
            'house_number' => $this->house_number,
            'postal_code' => $this->postal_code,
            'city' => $this->city,
            'country' => optional($this->country)->name,
            'phone' => $this->phone,
            'mobile' => $this->mobile,
            'whatsapp' => $this->whatsapp,
            'website' => $this->website,
        ];

        $visibleFields = [];
        foreach ($fields as $field => $value) {
            if ($this->{'show_' . $field} && !empty($value)) {
                $visibleFields[$field] = $value;
            }
        }

        return $visibleFields;
    }

    /**
     * Update contact details with visibility settings
     */
    public function updateContactDetails(array $contactData, array $visibilityData, bool $useCustom = false): bool
    {
        $updateData = array_merge($contactData, $visibilityData, [
            'use_custom_contact_details' => $useCustom,
            'contact_updated_at' => now(),
        ]);

        return $this->update($updateData);
    }
}