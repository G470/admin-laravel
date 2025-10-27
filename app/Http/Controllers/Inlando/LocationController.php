<?php

namespace App\Http\Controllers\Inlando;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\MasterLocation;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Get location suggestions for autocomplete from master locations
     */
    public function suggestions(Request $request)
    {
        $query = $request->get('query');
        $country = $request->get('country', 'de'); // Default to Germany
        
        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }
        
        $locations = MasterLocation::forCountry($country)
            ->search($query)
            ->orderBy('city')
            ->orderBy('postcode')
            ->limit(10)
            ->get()
            ->map(function ($location) {
                return [
                    'id' => $location->id,
                    'name' => $location->display_name,
                    'city' => $location->city,
                    'postcode' => $location->postcode,
                    'country' => strtoupper($location->country),
                    'display' => "{$location->city} ({$location->postcode})"
                ];
            });
            
        return response()->json($locations);
    }

    /**
     * Legacy method for vendor-specific locations (kept for backward compatibility)
     */
    public function vendorSuggestions(Request $request)
    {
        $query = $request->get('query');
        
        if (!$query || strlen($query) < 3) {
            return response()->json([]);
        }
        
        $locations = Location::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('city', 'LIKE', "%{$query}%")
                  ->orWhere('postal_code', 'LIKE', "%{$query}%");
            })
            ->select('name', 'city', 'country', 'postal_code')
            ->limit(10)
            ->get()
            ->map(function ($location) {
                return [
                    'name' => $location->city ?: $location->name,
                    'country' => $location->country
                ];
            });
            
        return response()->json($locations);
    }

    /**
     * Show city overview page with all cities that have rentals
     */
    public function cityOverview(Request $request)
    {
        $country = $request->get('country', 'DE');
        
        // Get cities grouped by first letter
        $cities = Location::select('city', 'country')
                         ->withCount('rentals')
                         ->having('rentals_count', '>', 0)
                         ->where('country', $country)
                         ->groupBy('city', 'country')
                         ->orderBy('city')
                         ->get()
                         ->groupBy(function ($location) {
                             return strtoupper(substr($location->city, 0, 1));
                         });

        // Get top cities by rental count
        $topCities = Location::select('city', 'country')
                           ->withCount('rentals')
                           ->having('rentals_count', '>', 5)
                           ->where('country', $country)
                           ->groupBy('city', 'country')
                           ->orderByDesc('rentals_count')
                           ->limit(20)
                           ->get();

        // Get states/regions
        $states = Location::select('state', 'country')
                        ->whereNotNull('state')
                        ->where('state', '!=', '')
                        ->withCount('rentals')
                        ->having('rentals_count', '>', 0)
                        ->where('country', $country)
                        ->groupBy('state', 'country')
                        ->orderBy('state')
                        ->get();

        return view('frontend.city-overview', compact('cities', 'topCities', 'states', 'country'));
    }

    /**
     * Show individual location page (all categories)
     */
    public function show($locationSlug)
    {
        // Try to find CitySeo first
        $citySeo = \App\Models\CitySeo::where('slug', $locationSlug)
                                    ->whereNull('category_id') // General location page
                                    ->online()
                                    ->first();

        if ($citySeo) {
            $locationData = [
                'city' => $citySeo->city,
                'state' => $citySeo->state,
                'country' => $citySeo->country,
                'name' => $citySeo->name ?: $citySeo->city
            ];
        } else {
            // Parse location from slug
            $cityName = str_replace('-', ' ', $locationSlug);
            $location = Location::where('city', 'LIKE', '%' . $cityName . '%')
                              ->with('rentals')
                              ->first();
                              
            if (!$location) {
                abort(404, 'Location not found');
            }
            
            $locationData = [
                'city' => $location->city,
                'state' => null,
                'country' => $location->country,
                'name' => $location->city
            ];
        }

        // Get all rentals in this location
        $rentals = \App\Models\Rental::with(['location', 'category', 'vendor'])
                                   ->where('status', 'published')
                                   ->whereHas('location', function ($q) use ($locationData) {
                                       $q->where('city', 'LIKE', '%' . $locationData['city'] . '%')
                                         ->where('country', $locationData['country']);
                                   })
                                   ->paginate(20);

        // Get top categories in this location
        $topCategories = \App\Models\Category::withCount(['rentals' => function ($query) use ($locationData) {
                                                $query->whereHas('location', function ($q) use ($locationData) {
                                                    $q->where('city', 'LIKE', '%' . $locationData['city'] . '%')
                                                      ->where('country', $locationData['country']);
                                                });
                                            }])
                                            ->having('rentals_count', '>', 0)
                                            ->orderByDesc('rentals_count')
                                            ->limit(12)
                                            ->get();

        // SEO data
        $seoData = $citySeo ? [
            'title' => $citySeo->meta_title ?: "Vermieten in {$locationData['name']} - Inlando",
            'description' => $citySeo->meta_description ?: "Finden Sie alles zur Miete in {$locationData['name']}. Große Auswahl, faire Preise, einfache Buchung bei Inlando.",
            'keywords' => $citySeo->meta_keywords,
            'content' => $citySeo->content
        ] : [
            'title' => "Vermieten in {$locationData['name']} - Inlando",
            'description' => "Finden Sie alles zur Miete in {$locationData['name']}. Große Auswahl, faire Preise, einfache Buchung bei Inlando.",
            'keywords' => "mieten, {$locationData['name']}, Vermietung",
            'content' => "<h1>Vermieten in {$locationData['name']}</h1><p>Entdecken Sie alle verfügbaren Mietangebote in {$locationData['name']}.</p>"
        ];

        return view('frontend.location', compact('locationData', 'rentals', 'topCategories', 'seoData'));
    }
}
