<?php

namespace App\Http\Controllers\Inlando;

use App\Http\Controllers\Controller;
use App\Models\CitySeo;
use App\Models\Category;
use App\Models\Location;
use App\Models\Rental;
use App\Models\MasterLocation;
use Illuminate\Http\Request;
use App\Models\DefaultSeoSetting;

class CategoryLocationController extends Controller
{
    /**
     * Show category + location combination page
     * URL: /mieten/{category1}/{category2?}/{category3?}/{location}
     */
    public function show(Request $request, $category1, $category2 = null, $category3 = null, $location)
    {
        // Parse the category hierarchy
        $categoryPath = array_filter([$category1, $category2, $category3]);
        $category = $this->findCategoryByPath($categoryPath);
        
        if (!$category) {
            abort(404, 'Category not found');
        }

        // Parse location (city or state)
        $locationData = $this->parseLocation($location);
        
        if (!$locationData) {
            abort(404, 'Location not found');
        }

        // Get rentals for this category + location combination
        $rentals = $this->getRentalsForCategoryLocation($category, $locationData);
        
        // Get or create SEO data
        $seoData = $this->getSeoData($category, $locationData);
        
        // Get related categories (siblings)
        $relatedCategories = $this->getRelatedCategories($category);
        
        // Get nearby locations
        $nearbyLocations = $this->getNearbyLocations($locationData);

        return view('frontend.category-location', compact(
            'category',
            'locationData', 
            'rentals',
            'seoData',
            'relatedCategories',
            'nearbyLocations',
            'categoryPath'
        ));
    }

    /**
     * Find category by hierarchical path
     */
    private function findCategoryByPath(array $categoryPath)
    {
        $currentCategory = null;
        
        foreach ($categoryPath as $index => $categorySlug) {
            $query = Category::where('slug', $categorySlug)->online();
            
            if ($index === 0) {
                // First level - no parent
                $query->whereNull('parent_id');
            } else {
                // Subsequent levels - must be child of previous
                $query->where('parent_id', $currentCategory->id);
            }
            
            $currentCategory = $query->first();
            
            if (!$currentCategory) {
                return null;
            }
        }
        
        return $currentCategory;
    }

    /**
     * Parse location slug to get location data
     */
    private function parseLocation($locationSlug)
    {
        // Try to find exact CitySeo match first
        $citySeo = CitySeo::where('slug', $locationSlug)->online()->first();
        
        if ($citySeo) {
            return [
                'type' => 'city_seo',
                'data' => $citySeo,
                'city' => $citySeo->city,
                'state' => $citySeo->state,
                'country' => $citySeo->country,
                'name' => $citySeo->name ?: $citySeo->city
            ];
        }

        // Try to find by city name
        $cityName = str_replace('-', ' ', $locationSlug);
        $locations = Location::where('city', 'LIKE', '%' . $cityName . '%')
                           ->with('rentals')
                           ->get();
        
        if ($locations->count() > 0) {
            $firstLocation = $locations->first();
            return [
                'type' => 'city',
                'data' => $locations,
                'city' => $firstLocation->city,
                'state' => null,
                'country' => $firstLocation->country,
                'name' => $firstLocation->city
            ];
        }

        // Try master locations
        $masterLocation = MasterLocation::where('city_encoded', $locationSlug)
                                      ->orWhere('city', 'LIKE', '%' . $cityName . '%')
                                      ->first();
        
        if ($masterLocation) {
            return [
                'type' => 'master_location',
                'data' => $masterLocation,
                'city' => $masterLocation->city,
                'state' => $masterLocation->state,
                'country' => $masterLocation->country,
                'name' => $masterLocation->city
            ];
        }

        return null;
    }

    /**
     * Get rentals for category + location combination
     */
    private function getRentalsForCategoryLocation($category, $locationData)
    {
        $query = Rental::with(['location', 'category', 'vendor'])
                      ->where('status', 'published');

        // Filter by category (including children)
        $categoryIds = $this->getCategoryWithChildren($category);
        $query->whereIn('category_id', $categoryIds);

        // Filter by location
        $query->whereHas('location', function ($q) use ($locationData) {
            $q->where('city', 'LIKE', '%' . $locationData['city'] . '%');
            
            if ($locationData['country']) {
                $q->where('country', $locationData['country']);
            }
        });

        return $query->paginate(20);
    }

    /**
     * Get category with all its children IDs
     */
    private function getCategoryWithChildren($category)
    {
        $categoryIds = [$category->id];
        
        // Add direct children
        $children = Category::where('parent_id', $category->id)->online()->get();
        foreach ($children as $child) {
            $categoryIds[] = $child->id;
            
            // Add grandchildren
            $grandchildren = Category::where('parent_id', $child->id)->online()->get();
            foreach ($grandchildren as $grandchild) {
                $categoryIds[] = $grandchild->id;
            }
        }
        
        return $categoryIds;
    }

    /**
     * Get SEO data for category + location combination
     */
    private function getSeoData($category, $locationData)
    {
        // Try to find specific CitySeo for this category + location
        $citySeo = CitySeo::where('city', $locationData['city'])
                         ->where('country', $locationData['country'])
                         ->where('category_id', $category->id)
                         ->online()
                         ->first();

        if ($citySeo) {
            return [
                'title' => $citySeo->meta_title,
                'description' => $citySeo->meta_description,
                'keywords' => $citySeo->meta_keywords,
                'content' => $citySeo->content,
                'source' => 'specific_seo'
            ];
        }

        // Try general location SEO (all categories)
        $generalCitySeo = CitySeo::where('city', $locationData['city'])
                                ->where('country', $locationData['country'])
                                ->whereNull('category_id')
                                ->online()
                                ->first();

        if ($generalCitySeo) {
            return [
                'title' => $this->generateTitle($category, $locationData, $generalCitySeo->meta_title),
                'description' => $this->generateDescription($category, $locationData, $generalCitySeo->meta_description),
                'keywords' => $generalCitySeo->meta_keywords,
                'content' => $this->generateContent($category, $locationData, $generalCitySeo->content),
                'source' => 'general_seo'
            ];
        }

        // Use default SEO settings with templates
        return DefaultSeoSetting::getDefaultSeo($category, $locationData, $locationData['country']);
    }

    /**
     * Generate default SEO content
     */
    private function generateDefaultSeo($category, $locationData)
    {
        $cityName = $locationData['name'];
        $categoryName = $category->name;
        
        return [
            'title' => "{$categoryName} mieten in {$cityName} - Inlando",
            'description' => "Finden Sie {$categoryName} zur Miete in {$cityName}. Große Auswahl, faire Preise, einfache Buchung bei Inlando.",
            'keywords' => "{$categoryName}, mieten, {$cityName}, Vermietung",
            'content' => $this->generateDefaultContent($category, $locationData),
            'source' => 'default'
        ];
    }

    /**
     * Generate default content for category + location
     */
    private function generateDefaultContent($category, $locationData)
    {
        $cityName = $locationData['name'];
        $categoryName = $category->name;
        
        return "
        <h1>{$categoryName} mieten in {$cityName}</h1>
        <p>Suchen Sie {$categoryName} zur Miete in {$cityName}? Bei Inlando finden Sie eine große Auswahl an hochwertigen {$categoryName} von verifizierten Anbietern.</p>
        
        <h2>Warum {$categoryName} in {$cityName} bei Inlando mieten?</h2>
        <ul>
            <li>Große Auswahl an {$categoryName} in {$cityName}</li>
            <li>Faire und transparente Preise</li>
            <li>Einfache Online-Buchung</li>
            <li>Verifizierte Anbieter</li>
            <li>Schnelle Verfügbarkeitsprüfung</li>
        </ul>
        
        <p>Entdecken Sie jetzt alle verfügbaren {$categoryName} in {$cityName} und buchen Sie direkt online.</p>
        ";
    }

    /**
     * Generate dynamic title
     */
    private function generateTitle($category, $locationData, $baseTitle = null)
    {
        if ($baseTitle) {
            return str_replace(['{category}', '{city}'], [$category->name, $locationData['name']], $baseTitle);
        }
        
        return "{$category->name} mieten in {$locationData['name']} - Inlando";
    }

    /**
     * Generate dynamic description
     */
    private function generateDescription($category, $locationData, $baseDescription = null)
    {
        if ($baseDescription) {
            return str_replace(['{category}', '{city}'], [$category->name, $locationData['name']], $baseDescription);
        }
        
        return "Finden Sie {$category->name} zur Miete in {$locationData['name']}. Große Auswahl, faire Preise, einfache Buchung bei Inlando.";
    }

    /**
     * Generate dynamic content
     */
    private function generateContent($category, $locationData, $baseContent = null)
    {
        if ($baseContent) {
            return str_replace(['{category}', '{city}'], [$category->name, $locationData['name']], $baseContent);
        }
        
        return $this->generateDefaultContent($category, $locationData);
    }

    /**
     * Get related categories (siblings)
     */
    private function getRelatedCategories($category)
    {
        if ($category->parent_id) {
            return Category::where('parent_id', $category->parent_id)
                          ->where('id', '!=', $category->id)
                          ->online()
                          ->ordered()
                          ->limit(6)
                          ->get();
        }

        return Category::whereNull('parent_id')
                      ->where('id', '!=', $category->id)
                      ->online()
                      ->ordered()
                      ->limit(6)
                      ->get();
    }

    /**
     * Get nearby locations
     */
    private function getNearbyLocations($locationData)
    {
        $baseQuery = Location::where('country', $locationData['country'])
                            ->where('city', '!=', $locationData['city'])
                            ->withCount('rentals')
                            ->having('rentals_count', '>', 0);

        // If we have coordinates, order by distance
        if (isset($locationData['data']->latitude, $locationData['data']->longitude)) {
            $lat = $locationData['data']->latitude;
            $lng = $locationData['data']->longitude;
            
            $baseQuery->selectRaw("*, 
                (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance", 
                [$lat, $lng, $lat])
                ->orderBy('distance');
        } else {
            $baseQuery->orderBy('city');
        }

        return $baseQuery->limit(10)->get();
    }
}
