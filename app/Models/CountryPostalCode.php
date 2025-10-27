<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class CountryPostalCode extends Model
{
    protected $fillable = [
        'country_code',
        'postal_code',
        'city',
        'sub_city',
        'region',
        'latitude',
        'longitude',
        'population',
        'additional_data'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'population' => 'integer',
        'additional_data' => 'array',
    ];

    /**
     * Dynamically set the table name based on country code
     */
    public function setTableForCountry(string $countryCode): self
    {
        $this->table = 'postal_codes_' . strtolower($countryCode);
        return $this;
    }

    /**
     * Create a new instance for a specific country
     */
    public static function forCountry(string $countryCode): self
    {
        $instance = new static();
        return $instance->setTableForCountry($countryCode);
    }

    /**
     * Get postal codes for a specific country
     */
    public static function getForCountry(string $countryCode): Builder
    {
        return static::forCountry($countryCode)->newQuery();
    }

    /**
     * Search postal codes and cities
     */
    public function scopeSearch(Builder $query, string $searchTerm): Builder
    {
        return $query->where(function ($q) use ($searchTerm) {
            $q->where('postal_code', 'LIKE', "%{$searchTerm}%")
                ->orWhere('city', 'LIKE', "%{$searchTerm}%")
                ->orWhere('sub_city', 'LIKE', "%{$searchTerm}%");
        });
    }

    /**
     * Filter by region
     */
    public function scopeInRegion(Builder $query, string $region): Builder
    {
        return $query->where('region', $region);
    }

    /**
     * Filter records with coordinates
     */
    public function scopeWithCoordinates(Builder $query): Builder
    {
        return $query->whereNotNull('latitude')
            ->whereNotNull('longitude');
    }

    /**
     * Filter records with population data
     */
    public function scopeWithPopulation(Builder $query): Builder
    {
        return $query->whereNotNull('population');
    }

    /**
     * Order by population (descending by default)
     */
    public function scopeOrderByPopulation(Builder $query, string $direction = 'desc'): Builder
    {
        return $query->orderBy('population', $direction);
    }

    /**
     * Get formatted display name
     */
    public function getDisplayNameAttribute(): string
    {
        $parts = array_filter([
            $this->postal_code,
            $this->city,
            $this->sub_city ? "({$this->sub_city})" : null,
            $this->region
        ]);

        return implode(' ', $parts);
    }

    /**
     * Get full address format
     */
    public function getFullAddressAttribute(): string
    {
        $parts = [];

        if ($this->city) {
            $parts[] = $this->city;
        }

        if ($this->sub_city) {
            $parts[] = "({$this->sub_city})";
        }

        if ($this->postal_code) {
            $parts[] = $this->postal_code;
        }

        if ($this->region) {
            $parts[] = $this->region;
        }

        return implode(', ', $parts);
    }

    /**
     * Check if coordinates are available
     */
    public function hasCoordinates(): bool
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    /**
     * Get distance to another postal code (Haversine formula)
     */
    public function distanceTo(CountryPostalCode $other): ?float
    {
        if (!$this->hasCoordinates() || !$other->hasCoordinates()) {
            return null;
        }

        $earthRadius = 6371; // Earth's radius in kilometers

        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($other->latitude);
        $lonTo = deg2rad($other->longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos($latFrom) * cos($latTo) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Find postal codes within radius
     */
    public static function withinRadius(string $countryCode, float $latitude, float $longitude, float $radiusKm = 10): Builder
    {
        return static::forCountry($countryCode)
            ->withCoordinates()
            ->selectRaw("
                *,
                (6371 * acos(
                    cos(radians(?)) * 
                    cos(radians(latitude)) * 
                    cos(radians(longitude) - radians(?)) + 
                    sin(radians(?)) * 
                    sin(radians(latitude))
                )) AS distance
            ", [$latitude, $longitude, $latitude])
            ->having('distance', '<=', $radiusKm)
            ->orderBy('distance');
    }

    /**
     * Get statistics for a country's postal codes
     */
    public static function getCountryStats(string $countryCode): array
    {
        $query = static::forCountry($countryCode);

        return [
            'total_records' => $query->count(),
            'unique_cities' => $query->distinct('city')->count(),
            'unique_regions' => $query->distinct('region')->count(),
            'records_with_coordinates' => $query->withCoordinates()->count(),
            'records_with_population' => $query->withPopulation()->count(),
            'total_population' => $query->sum('population'),
            'avg_population' => $query->avg('population'),
            'max_population' => $query->max('population'),
            'largest_city' => $query->orderByPopulation()->first()?->city,
        ];
    }

    /**
     * Get top cities by population
     */
    public static function getTopCities(string $countryCode, int $limit = 10): Builder
    {
        return static::forCountry($countryCode)
            ->withPopulation()
            ->orderByPopulation()
            ->limit($limit);
    }

    /**
     * Auto-complete suggestions for search
     */
    public static function getSuggestions(string $countryCode, string $query, int $limit = 10): Builder
    {
        return static::forCountry($countryCode)
            ->search($query)
            ->orderByPopulation()
            ->limit($limit);
    }

    /**
     * Export data for a country
     */
    public static function exportCountryData(string $countryCode, array $columns = ['*']): \Illuminate\Support\Collection
    {
        return static::forCountry($countryCode)
            ->select($columns)
            ->orderBy('postal_code')
            ->orderBy('city')
            ->get();
    }

    /**
     * Bulk update coordinates for records without them
     */
    public static function updateMissingCoordinates(string $countryCode, array $coordinateData): int
    {
        $updated = 0;
        $query = static::forCountry($countryCode);

        foreach ($coordinateData as $data) {
            $result = $query->where('postal_code', $data['postal_code'])
                ->where('city', $data['city'])
                ->whereNull('latitude')
                ->update([
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                    'updated_at' => now()
                ]);
            $updated += $result;
        }

        return $updated;
    }

    /**
     * Clean duplicate records
     */
    public static function cleanDuplicates(string $countryCode): int
    {
        $query = static::forCountry($countryCode);

        // Keep the record with the most complete data (with coordinates and population)
        $duplicates = $query->select('postal_code', 'city', 'sub_city')
            ->selectRaw('COUNT(*) as count, GROUP_CONCAT(id) as ids')
            ->groupBy('postal_code', 'city', 'sub_city')
            ->having('count', '>', 1)
            ->get();

        $deletedCount = 0;

        foreach ($duplicates as $duplicate) {
            $ids = explode(',', $duplicate->ids);
            $records = $query->whereIn('id', $ids)
                ->orderByDesc('population')
                ->orderByDesc('latitude')
                ->get();

            // Keep the first (best) record, delete the rest
            $keepId = $records->first()->id;
            $deleteIds = $records->pluck('id')->filter(fn($id) => $id !== $keepId);

            $deletedCount += $query->whereIn('id', $deleteIds)->delete();
        }

        return $deletedCount;
    }
}