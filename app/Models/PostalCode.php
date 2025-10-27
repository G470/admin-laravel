<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostalCode extends Model
{
    protected $fillable = [
        'country_code',
        'postal_code',
        'city',
        'region',
        'district',
        'population',
        'latitude',
        'longitude'
    ];

    protected $casts = [
        'population' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Search for postal codes or cities based on input
     */
    public static function searchSuggestions($query, $countryCode = 'de', $limit = 10)
    {
        $query = trim($query);
        
        if (strlen($query) < 3) {
            return collect();
        }

        // Search both postal codes and cities
        return self::where('country_code', strtolower($countryCode))
            ->where(function ($q) use ($query) {
                $q->where('postal_code', 'LIKE', $query . '%')
                  ->orWhere('city', 'LIKE', $query . '%');
            })
            ->orderByDesc('population')
            ->orderBy('city')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'postal_code' => $item->postal_code,
                    'city' => $item->city,
                    'region' => $item->region,
                    'display' => $item->postal_code . ' ' . $item->city . ($item->region ? ', ' . $item->region : ''),
                    'population' => $item->population
                ];
            });
    }

    /**
     * Get formatted address string
     */
    public function getFormattedAddressAttribute()
    {
        return $this->postal_code . ' ' . $this->city . ($this->region ? ', ' . $this->region : '');
    }
}
