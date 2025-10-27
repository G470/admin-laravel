<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MapsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Exception;

class GeocodingController extends Controller
{
    protected $mapsService;

    public function __construct(MapsService $mapsService)
    {
        $this->mapsService = $mapsService;
    }

    /**
     * Geocode an address
     */
    public function geocode(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'address' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validierungsfehler',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check if maps service is available
            if (!$this->mapsService->hasMapsService()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kein Karten-Dienst verfügbar',
                    'error' => 'no_maps_service'
                ], 503);
            }

            $address = $request->input('address');
            $result = $this->mapsService->geocodeAddress($address);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Adresse konnte nicht geocodiert werden',
                    'error' => 'geocoding_failed'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Adresse erfolgreich geocodiert'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Geocoding-Fehler: ' . $e->getMessage(),
                'error' => 'geocoding_error'
            ], 500);
        }
    }

    /**
     * Reverse geocode coordinates
     */
    public function reverseGeocode(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validierungsfehler',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check if maps service is available
            if (!$this->mapsService->hasMapsService()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kein Karten-Dienst verfügbar',
                    'error' => 'no_maps_service'
                ], 503);
            }

            $latitude = (float) $request->input('latitude');
            $longitude = (float) $request->input('longitude');

            $result = $this->mapsService->reverseGeocode($latitude, $longitude);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Koordinaten konnten nicht umgekehrt geocodiert werden',
                    'error' => 'reverse_geocoding_failed'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Koordinaten erfolgreich umgekehrt geocodiert'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Reverse Geocoding-Fehler: ' . $e->getMessage(),
                'error' => 'reverse_geocoding_error'
            ], 500);
        }
    }

    /**
     * Get maps service status
     */
    public function status(): JsonResponse
    {
        try {
            $status = $this->mapsService->getServiceStatus();

            return response()->json([
                'success' => true,
                'data' => $status,
                'message' => 'Service-Status abgerufen'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Status-Abfrage fehlgeschlagen: ' . $e->getMessage(),
                'error' => 'status_error'
            ], 500);
        }
    }

    /**
     * Batch geocoding for multiple addresses
     */
    public function batchGeocode(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'addresses' => 'required|array|min:1|max:10',
            'addresses.*' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validierungsfehler',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check if maps service is available
            if (!$this->mapsService->hasMapsService()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kein Karten-Dienst verfügbar',
                    'error' => 'no_maps_service'
                ], 503);
            }

            $addresses = $request->input('addresses');
            $results = [];
            $errors = [];

            foreach ($addresses as $index => $address) {
                try {
                    $result = $this->mapsService->geocodeAddress($address);
                    if ($result) {
                        $results[] = [
                            'index' => $index,
                            'address' => $address,
                            'result' => $result
                        ];
                    } else {
                        $errors[] = [
                            'index' => $index,
                            'address' => $address,
                            'error' => 'Geocoding fehlgeschlagen'
                        ];
                    }
                } catch (Exception $e) {
                    $errors[] = [
                        'index' => $index,
                        'address' => $address,
                        'error' => $e->getMessage()
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'results' => $results,
                    'errors' => $errors,
                    'total_processed' => count($addresses),
                    'successful' => count($results),
                    'failed' => count($errors)
                ],
                'message' => 'Batch-Geocoding abgeschlossen'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Batch-Geocoding-Fehler: ' . $e->getMessage(),
                'error' => 'batch_geocoding_error'
            ], 500);
        }
    }

    /**
     * Clear geocoding cache
     */
    public function clearCache(): JsonResponse
    {
        try {
            $this->mapsService->clearCache();

            return response()->json([
                'success' => true,
                'message' => 'Geocoding-Cache erfolgreich geleert'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cache-Löschung fehlgeschlagen: ' . $e->getMessage(),
                'error' => 'cache_clear_error'
            ], 500);
        }
    }
}