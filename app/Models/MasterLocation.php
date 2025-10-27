<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterLocation extends Model
{
    protected $table = 'master_locations';

    protected $fillable = [
        'postcode',
        'city',
        'city_encoded',
        'zip',
        'subcity',
        'state',
        'country',
        'lat',
        'lng'
    ];

    protected $casts = [
        'lat' => 'decimal:8',
        'lng' => 'decimal:8',
    ];

    /**
     * Scope for country filtering
     */
    public function scopeForCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    /**
     * Scope for search functionality
     */
    public function scopeSearch($query, $searchTerm)
    {
        return $query->where(function ($q) use ($searchTerm) {
            $q->where('city', 'LIKE', "%{$searchTerm}%")
              ->orWhere('postcode', 'LIKE', "%{$searchTerm}%")
              ->orWhere('subcity', 'LIKE', "%{$searchTerm}%");
        });
    }

    /**
     * Get formatted display name
     */
    public function getDisplayNameAttribute()
    {
        $parts = array_filter([
            $this->city,
            $this->subcity ? "({$this->subcity})" : null,
            $this->postcode
        ]);
        
        return implode(' ', $parts);
    }
}
