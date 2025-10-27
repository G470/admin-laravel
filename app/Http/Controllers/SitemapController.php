<?php

namespace App\Http\Controllers;

use App\Models\CitySeo;
use App\Models\Category;
use App\Models\Location;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SitemapController extends Controller
{
    /**
     * Generate main sitemap index
     */
    public function index()
    {
        $sitemaps = [
            ['url' => route('sitemap.static'), 'lastmod' => now()->toISOString()],
            ['url' => route('sitemap.locations'), 'lastmod' => $this->getLastLocationUpdate()],
            ['url' => route('sitemap.categories'), 'lastmod' => $this->getLastCategoryUpdate()],
            ['url' => route('sitemap.category-locations'), 'lastmod' => $this->getLastCitySeoUpdate()],
            ['url' => route('sitemap.rentals'), 'lastmod' => $this->getLastRentalUpdate()],
        ];

        return response()->view('sitemaps.index', compact('sitemaps'))
                        ->header('Content-Type', 'application/xml');
    }

    /**
     * Generate static pages sitemap
     */
    public function static()
    {
        $urls = [
            ['url' => route('home'), 'priority' => '1.0', 'changefreq' => 'daily'],
            ['url' => route('cities.overview'), 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['url' => route('about'), 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['url' => route('how-it-works'), 'priority' => '0.5', 'changefreq' => 'monthly'],
        ];

        return response()->view('sitemaps.urlset', compact('urls'))
                        ->header('Content-Type', 'application/xml');
    }

    /**
     * Generate locations sitemap
     */
    public function locations()
    {
        $urls = [];

        // Add individual location pages
        $cities = Location::select('city', 'country')
                         ->withCount('rentals')
                         ->having('rentals_count', '>', 0)
                         ->groupBy('city', 'country')
                         ->get();

        foreach ($cities as $city) {
            $slug = Str::slug($city->city);
            $urls[] = [
                'url' => route('location.show', ['location' => $slug]),
                'priority' => '0.7',
                'changefreq' => 'weekly',
                'lastmod' => now()->toISOString()
            ];
        }

        // Add CitySeo specific pages
        $citySeos = CitySeo::whereNull('category_id')->online()->get();
        foreach ($citySeos as $citySeo) {
            $urls[] = [
                'url' => route('location.show', ['location' => $citySeo->slug]),
                'priority' => '0.8',
                'changefreq' => 'weekly',
                'lastmod' => $citySeo->updated_at->toISOString()
            ];
        }

        return response()->view('sitemaps.urlset', compact('urls'))
                        ->header('Content-Type', 'application/xml');
    }

    /**
     * Generate categories sitemap
     */
    public function categories()
    {
        $urls = [];

        $categories = Category::online()->ordered()->get();
        
        foreach ($categories as $category) {
            $categoryPath = $this->buildCategoryPath($category);
            
            $urls[] = [
                'url' => route('categories.show', ['category' => $categoryPath]),
                'priority' => $category->parent_id ? '0.6' : '0.7',
                'changefreq' => 'weekly',
                'lastmod' => $category->updated_at->toISOString()
            ];
        }

        return response()->view('sitemaps.urlset', compact('urls'))
                        ->header('Content-Type', 'application/xml');
    }

    /**
     * Generate category + location combination sitemap
     */
    public function categoryLocations()
    {
        $urls = [];

        // Get all CitySeo entries with categories
        $citySeos = CitySeo::with('category')->whereNotNull('category_id')->online()->get();
        
        foreach ($citySeos as $citySeo) {
            if ($citySeo->category) {
                $categoryPath = $this->buildCategoryPath($citySeo->category);
                $locationSlug = $citySeo->slug;
                
                $url = "/mieten/{$categoryPath}/{$locationSlug}";
                
                $urls[] = [
                    'url' => url($url),
                    'priority' => '0.8',
                    'changefreq' => 'weekly',
                    'lastmod' => $citySeo->updated_at->toISOString()
                ];
            }
        }

        // Generate combinations for popular category + location pairs
        $popularCombinations = $this->getPopularCategoryLocationCombinations();
        
        foreach ($popularCombinations as $combo) {
            $categoryPath = $this->buildCategoryPath($combo['category']);
            $locationSlug = Str::slug($combo['city']);
            
            $url = "/mieten/{$categoryPath}/{$locationSlug}";
            
            // Only add if not already added from CitySeo
            $exists = collect($urls)->contains(function ($item) use ($url) {
                return $item['url'] === url($url);
            });
            
            if (!$exists) {
                $urls[] = [
                    'url' => url($url),
                    'priority' => '0.7',
                    'changefreq' => 'weekly',
                    'lastmod' => now()->toISOString()
                ];
            }
        }

        return response()->view('sitemaps.urlset', compact('urls'))
                        ->header('Content-Type', 'application/xml');
    }

    /**
     * Generate rentals sitemap
     */
    public function rentals()
    {
        $urls = [];

        $rentals = Rental::where('status', 'published')
                        ->with('category', 'location')
                        ->get();

        foreach ($rentals as $rental) {
            $urls[] = [
                'url' => route('rental.show', ['id' => $rental->id]),
                'priority' => '0.6',
                'changefreq' => 'monthly',
                'lastmod' => $rental->updated_at->toISOString()
            ];
        }

        return response()->view('sitemaps.urlset', compact('urls'))
                        ->header('Content-Type', 'application/xml');
    }

    /**
     * Build hierarchical category path
     */
    private function buildCategoryPath($category)
    {
        $path = [];
        $current = $category;
        
        // Build path from child to parent
        while ($current) {
            array_unshift($path, $current->slug);
            $current = $current->parent;
        }
        
        return implode('/', $path);
    }

    /**
     * Get popular category + location combinations
     */
    private function getPopularCategoryLocationCombinations($limit = 100)
    {
        // Get combinations that have more than 3 rentals
        return \DB::table('rentals')
                 ->join('categories', 'rentals.category_id', '=', 'categories.id')
                 ->join('locations', 'rentals.location_id', '=', 'locations.id')
                 ->select('categories.id as category_id', 'categories.slug as category_slug', 
                         'categories.name as category_name', 'locations.city',
                         \DB::raw('COUNT(*) as rental_count'))
                 ->where('rentals.status', 'published')
                 ->where('categories.status', 'online')
                 ->groupBy('categories.id', 'categories.slug', 'categories.name', 'locations.city')
                 ->having('rental_count', '>', 3)
                 ->orderByDesc('rental_count')
                 ->limit($limit)
                 ->get()
                 ->map(function ($row) {
                     return [
                         'category' => (object) [
                             'id' => $row->category_id,
                             'slug' => $row->category_slug,
                             'name' => $row->category_name
                         ],
                         'city' => $row->city,
                         'rental_count' => $row->rental_count
                     ];
                 });
    }

    /**
     * Get last update timestamps
     */
    private function getLastLocationUpdate()
    {
        $lastLocation = Location::latest('updated_at')->first();
        $lastCitySeo = CitySeo::latest('updated_at')->first();
        
        $timestamps = array_filter([
            $lastLocation?->updated_at,
            $lastCitySeo?->updated_at
        ]);
        
        return $timestamps ? max($timestamps)->toISOString() : now()->toISOString();
    }

    private function getLastCategoryUpdate()
    {
        $lastCategory = Category::latest('updated_at')->first();
        return $lastCategory ? $lastCategory->updated_at->toISOString() : now()->toISOString();
    }

    private function getLastCitySeoUpdate()
    {
        $lastCitySeo = CitySeo::latest('updated_at')->first();
        return $lastCitySeo ? $lastCitySeo->updated_at->toISOString() : now()->toISOString();
    }

    private function getLastRentalUpdate()
    {
        $lastRental = Rental::latest('updated_at')->first();
        return $lastRental ? $lastRental->updated_at->toISOString() : now()->toISOString();
    }
}
