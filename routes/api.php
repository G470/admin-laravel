<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\Api\PostalCodeController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\RentalController;
use App\Http\Controllers\Inlando\LocationController;
use App\Http\Controllers\Api\RentalFieldController;
use App\Http\Controllers\Api\GeocodingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/generate-landing-page', [LandingPageController::class, 'triggerN8nWebhook']);
Route::get('/landing-pages/{slug}', [LandingPageController::class, 'show']);
Route::post('/landingpages', [LandingPageController::class, 'store']);

// Postal code suggestions API
Route::get('/postal-codes/suggestions', [PostalCodeController::class, 'suggestions']);

// Location suggestions for vendor location edit form
Route::get('/postal-codes/location-suggestions', [PostalCodeController::class, 'locationSuggestions']);

// Location autocomplete for search functionality
Route::get('/locations', [LocationController::class, 'suggestions']);

// Location suggestions API
Route::get('/locations/suggestions', function (Request $request) {
    $query = $request->get('query', '');
    $countryCode = $request->get('countryCode', 'DE');

    if (strlen($query) < 2) {
        return response()->json(['suggestions' => []]);
    }

    try {
        $suggestions = \App\Models\CountryPostalCode::getSuggestions($countryCode, $query, 10)
            ->select('postal_code', 'city', 'sub_city', 'region')
            ->get()
            ->map(function ($item) {
                return [
                    'postal_code' => $item->postal_code,
                    'city' => $item->city,
                    'sub_city' => $item->sub_city,
                    'region' => $item->region,
                    'display_name' => $item->display_name,
                    'full_address' => $item->full_address
                ];
            });

        return response()->json(['suggestions' => $suggestions]);
    } catch (\Exception $e) {
        return response()->json(['suggestions' => [], 'error' => 'No data available for this country']);
    }
})->name('api.locations.suggestions');

// Category suggestions API
Route::get('/categories/suggestions', [CategoryController::class, 'suggestions']);

// Get single category by ID
Route::get('/categories/{id}', [CategoryController::class, 'show']);

// Favorite rentals API
Route::post('/rentals/favorites', [RentalController::class, 'getFavorites'])->name('api.rentals.favorites');

// Dynamic Rental Fields API Routes
Route::prefix('rental-fields')->group(function () {
    Route::get('/templates', [RentalFieldController::class, 'getTemplates']);
    Route::get('/templates/{categoryId}', [RentalFieldController::class, 'getTemplatesByCategory']);
    Route::get('/fields/{templateId}', [RentalFieldController::class, 'getFields']);
    Route::get('/values/{rentalId}', [RentalFieldController::class, 'getValues']);
    Route::post('/values/{rentalId}', [RentalFieldController::class, 'saveValues']);
});

// Geocoding API Routes
Route::prefix('geocoding')->group(function () {
    Route::post('/geocode', [GeocodingController::class, 'geocode'])->name('api.geocoding.geocode');
    Route::post('/reverse', [GeocodingController::class, 'reverseGeocode'])->name('api.geocoding.reverse');
    Route::get('/status', [GeocodingController::class, 'status'])->name('api.geocoding.status');
    Route::post('/batch', [GeocodingController::class, 'batchGeocode'])->name('api.geocoding.batch');
    Route::delete('/cache', [GeocodingController::class, 'clearCache'])->name('api.geocoding.cache.clear');
});
