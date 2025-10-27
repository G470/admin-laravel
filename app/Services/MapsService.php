<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class MapsService
{
    protected $googleMapsApiKey;
    protected $openStreetMapApiKey;
    protected $googleMapsEnabled;
    protected $openStreetMapEnabled;

    public function __construct()
    {
        $this->googleMapsEnabled = Setting::get('google_maps_enabled', false);
        $this->openStreetMapEnabled = Setting::get('openstreetmap_enabled', false);
        $this->googleMapsApiKey = Setting::get('google_maps_api_key', '');
        $this->openStreetMapApiKey = Setting::get('openstreetmap_api_key', '');
    }

    /**
     * Check if any maps service is available
     */
    public function hasMapsService(): bool
    {
        return $this->googleMapsEnabled || $this->openStreetMapEnabled;
    }

    /**
     * Get preferred maps service
     */
    public function getPreferredMapsService(): ?string
    {
        if ($this->googleMapsEnabled) {
            return 'google_maps';
        } elseif ($this->openStreetMapEnabled) {
            return 'openstreetmap';
        }
        return null;
    }

    /**
     * Geocode an address using the preferred service
     */
    public function geocodeAddress(string $address): ?array
    {
        $service = $this->getPreferredMapsService();

        if (!$service) {
            throw new Exception('Kein Karten-Dienst verfügbar');
        }

        // Check cache first
        $cacheKey = 'geocode_' . md5($address . '_' . $service);
        $cachedResult = Cache::get($cacheKey);

        if ($cachedResult) {
            return $cachedResult;
        }

        try {
            $result = null;

            if ($service === 'google_maps') {
                $result = $this->geocodeWithGoogleMaps($address);
            } elseif ($service === 'openstreetmap') {
                $result = $this->geocodeWithOpenStreetMap($address);
            }

            if ($result) {
                // Cache the result for 24 hours
                Cache::put($cacheKey, $result, now()->addHours(24));
                return $result;
            }

        } catch (Exception $e) {
            Log::error("Geocoding error with {$service}: " . $e->getMessage());

            // Try fallback service
            $fallbackService = $service === 'google_maps' ? 'openstreetmap' : 'google_maps';
            if ($this->isServiceEnabled($fallbackService)) {
                return $this->geocodeWithFallback($address, $fallbackService);
            }

            throw $e;
        }

        return null;
    }

    /**
     * Geocode with Google Maps API
     */
    protected function geocodeWithGoogleMaps(string $address): ?array
    {
        if (!$this->googleMapsEnabled || empty($this->googleMapsApiKey)) {
            throw new Exception('Google Maps ist nicht konfiguriert');
        }

        $response = Http::timeout(10)->get('https://maps.googleapis.com/maps/api/geocode/json', [
            'address' => $address,
            'key' => $this->googleMapsApiKey,
            'language' => 'de',
            'region' => 'de'
        ]);

        if ($response->successful()) {
            $data = $response->json();

            if ($data['status'] === 'OK' && !empty($data['results'])) {
                $location = $data['results'][0]['geometry']['location'];
                $formattedAddress = $data['results'][0]['formatted_address'];

                return [
                    'latitude' => $location['lat'],
                    'longitude' => $location['lng'],
                    'formatted_address' => $formattedAddress,
                    'service' => 'google_maps',
                    'confidence' => $this->calculateGoogleMapsConfidence($data['results'][0])
                ];
            }
        }

        throw new Exception('Google Maps Geocoding fehlgeschlagen: ' . ($data['status'] ?? 'Unknown error'));
    }

    /**
     * Geocode with OpenStreetMap Nominatim
     */
    protected function geocodeWithOpenStreetMap(string $address): ?array
    {
        if (!$this->openStreetMapEnabled) {
            throw new Exception('OpenStreetMap ist nicht konfiguriert');
        }

        // Rate limiting for Nominatim (1 request per second)
        $this->checkNominatimRateLimit();

        $response = Http::timeout(10)->get('https://nominatim.openstreetmap.org/search', [
            'q' => $address,
            'format' => 'json',
            'limit' => 1,
            'addressdetails' => 1,
            'accept-language' => 'de'
        ]);

        if ($response->successful()) {
            $data = $response->json();

            if (!empty($data)) {
                $result = $data[0];

                return [
                    'latitude' => (float) $result['lat'],
                    'longitude' => (float) $result['lon'],
                    'formatted_address' => $result['display_name'],
                    'service' => 'openstreetmap',
                    'confidence' => $this->calculateOpenStreetMapConfidence($result)
                ];
            }
        }

        throw new Exception('OpenStreetMap Geocoding fehlgeschlagen');
    }

    /**
     * Reverse geocoding - get address from coordinates
     */
    public function reverseGeocode(float $latitude, float $longitude): ?array
    {
        $service = $this->getPreferredMapsService();

        if (!$service) {
            throw new Exception('Kein Karten-Dienst verfügbar');
        }

        // Check cache first
        $cacheKey = 'reverse_geocode_' . md5($latitude . '_' . $longitude . '_' . $service);
        $cachedResult = Cache::get($cacheKey);

        if ($cachedResult) {
            return $cachedResult;
        }

        try {
            $result = null;

            if ($service === 'google_maps') {
                $result = $this->reverseGeocodeWithGoogleMaps($latitude, $longitude);
            } elseif ($service === 'openstreetmap') {
                $result = $this->reverseGeocodeWithOpenStreetMap($latitude, $longitude);
            }

            if ($result) {
                // Cache the result for 24 hours
                Cache::put($cacheKey, $result, now()->addHours(24));
                return $result;
            }

        } catch (Exception $e) {
            Log::error("Reverse geocoding error with {$service}: " . $e->getMessage());
            throw $e;
        }

        return null;
    }

    /**
     * Reverse geocode with Google Maps
     */
    protected function reverseGeocodeWithGoogleMaps(float $latitude, float $longitude): ?array
    {
        if (!$this->googleMapsEnabled || empty($this->googleMapsApiKey)) {
            throw new Exception('Google Maps ist nicht konfiguriert');
        }

        $response = Http::timeout(10)->get('https://maps.googleapis.com/maps/api/geocode/json', [
            'latlng' => "{$latitude},{$longitude}",
            'key' => $this->googleMapsApiKey,
            'language' => 'de',
            'region' => 'de'
        ]);

        if ($response->successful()) {
            $data = $response->json();

            if ($data['status'] === 'OK' && !empty($data['results'])) {
                $result = $data['results'][0];

                return [
                    'address' => $result['formatted_address'],
                    'components' => $this->extractAddressComponents($result['address_components']),
                    'service' => 'google_maps'
                ];
            }
        }

        throw new Exception('Google Maps Reverse Geocoding fehlgeschlagen');
    }

    /**
     * Reverse geocode with OpenStreetMap
     */
    protected function reverseGeocodeWithOpenStreetMap(float $latitude, float $longitude): ?array
    {
        if (!$this->openStreetMapEnabled) {
            throw new Exception('OpenStreetMap ist nicht konfiguriert');
        }

        // Rate limiting for Nominatim
        $this->checkNominatimRateLimit();

        $response = Http::timeout(10)->get('https://nominatim.openstreetmap.org/reverse', [
            'lat' => $latitude,
            'lon' => $longitude,
            'format' => 'json',
            'addressdetails' => 1,
            'accept-language' => 'de'
        ]);

        if ($response->successful()) {
            $data = $response->json();

            if (isset($data['display_name'])) {
                return [
                    'address' => $data['display_name'],
                    'components' => $data['address'] ?? [],
                    'service' => 'openstreetmap'
                ];
            }
        }

        throw new Exception('OpenStreetMap Reverse Geocoding fehlgeschlagen');
    }

    /**
     * Calculate confidence score for Google Maps result
     */
    protected function calculateGoogleMapsConfidence(array $result): float
    {
        $confidence = 0.5; // Base confidence

        // Check geometry accuracy
        if (isset($result['geometry']['location_type'])) {
            switch ($result['geometry']['location_type']) {
                case 'ROOFTOP':
                    $confidence += 0.4;
                    break;
                case 'RANGE_INTERPOLATED':
                    $confidence += 0.3;
                    break;
                case 'GEOMETRIC_CENTER':
                    $confidence += 0.2;
                    break;
                case 'APPROXIMATE':
                    $confidence += 0.1;
                    break;
            }
        }

        return min($confidence, 1.0);
    }

    /**
     * Calculate confidence score for OpenStreetMap result
     */
    protected function calculateOpenStreetMapConfidence(array $result): float
    {
        $confidence = 0.3; // Base confidence for OSM

        // Check importance score
        if (isset($result['importance'])) {
            $confidence += min($result['importance'] * 0.7, 0.4);
        }

        return min($confidence, 1.0);
    }

    /**
     * Extract address components from Google Maps response
     */
    protected function extractAddressComponents(array $components): array
    {
        $extracted = [];

        foreach ($components as $component) {
            $types = $component['types'];
            $value = $component['long_name'];

            if (in_array('street_number', $types)) {
                $extracted['street_number'] = $value;
            } elseif (in_array('route', $types)) {
                $extracted['street'] = $value;
            } elseif (in_array('locality', $types)) {
                $extracted['city'] = $value;
            } elseif (in_array('postal_code', $types)) {
                $extracted['postal_code'] = $value;
            } elseif (in_array('country', $types)) {
                $extracted['country'] = $value;
            }
        }

        return $extracted;
    }

    /**
     * Check if a specific service is enabled
     */
    protected function isServiceEnabled(string $service): bool
    {
        if ($service === 'google_maps') {
            return $this->googleMapsEnabled && !empty($this->googleMapsApiKey);
        } elseif ($service === 'openstreetmap') {
            return $this->openStreetMapEnabled;
        }
        return false;
    }

    /**
     * Try fallback service for geocoding
     */
    protected function geocodeWithFallback(string $address, string $fallbackService): ?array
    {
        Log::info("Trying fallback service: {$fallbackService}");

        if ($fallbackService === 'google_maps') {
            return $this->geocodeWithGoogleMaps($address);
        } elseif ($fallbackService === 'openstreetmap') {
            return $this->geocodeWithOpenStreetMap($address);
        }

        return null;
    }

    /**
     * Rate limiting for Nominatim (1 request per second)
     */
    protected function checkNominatimRateLimit(): void
    {
        $cacheKey = 'nominatim_rate_limit';
        $lastRequest = Cache::get($cacheKey);

        if ($lastRequest) {
            $timeSinceLastRequest = now()->diffInMilliseconds($lastRequest);
            if ($timeSinceLastRequest < 1000) { // 1 second
                $sleepTime = 1000 - $timeSinceLastRequest;
                usleep($sleepTime * 1000); // Convert to microseconds
            }
        }

        Cache::put($cacheKey, now(), now()->addSeconds(2));
    }

    /**
     * Clear geocoding cache
     */
    public function clearCache(): void
    {
        Cache::flush();
    }

    /**
     * Get service status information
     */
    public function getServiceStatus(): array
    {
        return [
            'google_maps' => [
                'enabled' => $this->googleMapsEnabled,
                'has_api_key' => !empty($this->googleMapsApiKey),
                'status' => $this->googleMapsEnabled && !empty($this->googleMapsApiKey) ? 'ready' : 'not_configured'
            ],
            'openstreetmap' => [
                'enabled' => $this->openStreetMapEnabled,
                'has_api_key' => !empty($this->openStreetMapApiKey),
                'status' => $this->openStreetMapEnabled ? 'ready' : 'not_configured'
            ],
            'preferred_service' => $this->getPreferredMapsService(),
            'has_any_service' => $this->hasMapsService()
        ];
    }
}