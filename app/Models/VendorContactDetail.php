<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorContactDetail extends Model
{
    protected $fillable = [
        'vendor_id',
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
    ];

    protected $casts = [
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
    ];

    /**
     * Get the vendor that owns the contact details
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    /**
     * Get the country for the contact details
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get formatted address string
     */
    public function getFormattedAddressAttribute(): string
    {
        $parts = array_filter([
            $this->street,
            $this->house_number,
            $this->postal_code . ' ' . $this->city,
            optional($this->country)->name
        ]);

        return implode(', ', $parts);
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
}