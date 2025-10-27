<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PostalCode;
use App\Models\CountryPostalCode;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PostalCodeController extends Controller
{
    /**
     * Get postal code and city suggestions
     */
    public function suggestions(Request $request): JsonResponse
    {
        $query = $request->get('query', '');
        $country = $request->get('country', 'de');
        $limit = min($request->get('limit', 10), 50); // Max 50 results

        // Validate minimum query length
        if (strlen(trim($query)) < 3) {
            return response()->json([
                'suggestions' => [],
                'message' => 'Mindestens 3 Zeichen erforderlich'
            ]);
        }

        try {
            $suggestions = PostalCode::searchSuggestions($query, $country, $limit);

            return response()->json([
                'suggestions' => $suggestions,
                'count' => $suggestions->count(),
                'query' => $query,
                'country' => $country
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Fehler beim Laden der Vorschläge',
                'suggestions' => []
            ], 500);
        }
    }

    /**
     * Get location suggestions for vendor location edit form
     * Supports both postal code and city search with country filtering
     */
    public function locationSuggestions(Request $request): JsonResponse
    {
        $query = $request->get('query', '');
        $countryCode = $request->get('country', 'DE');
        $type = $request->get('type', 'both'); // 'postal_code', 'city', or 'both'
        $limit = min($request->get('limit', 10), 20);

        // Validate minimum query length
        if (strlen(trim($query)) < 2) {
            return response()->json([
                'suggestions' => [],
                'message' => 'Mindestens 2 Zeichen erforderlich'
            ]);
        }

        try {
            // Try to use CountryPostalCode model first (dynamic tables)
            $suggestions = collect();

            try {
                $countryPostalCodes = CountryPostalCode::getSuggestions($countryCode, $query, $limit)
                    ->get()
                    ->map(function ($item) {
                        return [
                            'postal_code' => $item->postal_code,
                            'city' => $item->city,
                            'sub_city' => $item->sub_city,
                            'region' => $item->region,
                            'display_name' => $item->display_name,
                            'full_address' => $item->full_address,
                            'type' => 'country_postal_code'
                        ];
                    });

                $suggestions = $suggestions->merge($countryPostalCodes);
            } catch (\Exception $e) {
                // Fallback to regular PostalCode model
                $postalCodes = PostalCode::searchSuggestions($query, strtolower($countryCode), $limit)
                    ->map(function ($item) {
                        return [
                            'postal_code' => $item['postal_code'],
                            'city' => $item['city'],
                            'region' => $item['region'] ?? null,
                            'display_name' => $item['display'],
                            'full_address' => $item['display'],
                            'type' => 'postal_code'
                        ];
                    });

                $suggestions = $suggestions->merge($postalCodes);
            }

            // Filter by type if specified
            if ($type === 'postal_code') {
                $suggestions = $suggestions->filter(function ($item) use ($query) {
                    return stripos($item['postal_code'], $query) !== false;
                });
            } elseif ($type === 'city') {
                $suggestions = $suggestions->filter(function ($item) use ($query) {
                    return stripos($item['city'], $query) !== false;
                });
            }

            // Remove duplicates and limit results
            $suggestions = $suggestions->unique(function ($item) {
                return $item['postal_code'] . '-' . $item['city'];
            })->take($limit);

            return response()->json([
                'suggestions' => $suggestions->values(),
                'count' => $suggestions->count(),
                'query' => $query,
                'country' => $countryCode,
                'type' => $type
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Fehler beim Laden der Standort-Vorschläge',
                'suggestions' => [],
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
