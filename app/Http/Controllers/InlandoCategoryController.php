<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Helpers\DynamicRentalFields;

class InlandoCategoryController extends Controller
{
    /**
     * Configuration for Vuexy layout
     *
     * @return array
     */
    private function getPageConfigs()
    {
        return [
            'bodyClass' => 'landing-page',
            'navbarType' => 'fixed',
            'footerFixed' => false,
            'pageHeader' => false,
            'defaultLayout' => 'front'
        ];
    }

    /**
     * Display the homepage with categories.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Configure layout options for Vuexy theme
        $pageConfigs = $this->getPageConfigs();

        // Get categories from database instead of hardcoded arrays
        $categories = Category::active()
            ->whereNull('parent_id')
            ->with([
                'children' => function ($query) {
                    $query->active()->orderBy('name');
                }
            ])
            ->orderBy('name')
            ->get();

        // Get event-related categories dynamically
        $eventItems = Category::active()
            ->where(function ($query) {
                $query->where('name', 'like', '%event%')
                    ->orWhere('name', 'like', '%bühne%')
                    ->orWhere('name', 'like', '%dekoration%')
                    ->orWhere('name', 'like', '%kostüm%')
                    ->orWhere('name', 'like', '%technik%');
            })
            ->limit(8)
            ->get()
            ->map(function ($category) {
                return (object) [
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'description' => $category->description ?: 'Perfekt für dein nächstes Event',
                    'image' => $category->category_image ?: asset('assets/images/events/default.svg')
                ];
            });

        // Get vehicle-related categories dynamically  
        $vehicles = Category::active()
            ->where(function ($query) {
                $query->where('name', 'like', '%wohnmobil%')
                    ->orWhere('name', 'like', '%anhänger%')
                    ->orWhere('name', 'like', '%transporter%')
                    ->orWhere('name', 'like', '%fahrzeug%');
            })
            ->limit(6)
            ->get()
            ->map(function ($category) {
                return (object) [
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'description' => $category->description ?: 'Flexibel und unabhängig unterwegs',
                    'image' => $category->category_image ?: asset('assets/images/vehicles/default.svg')
                ];
            });

        // Get construction-related categories dynamically
        $constructionTools = Category::active()
            ->where(function ($query) {
                $query->where('name', 'like', '%bau%')
                    ->orWhere('name', 'like', '%maschine%')
                    ->orWhere('name', 'like', '%abbruch%')
                    ->orWhere('name', 'like', '%bühne%')
                    ->orWhere('name', 'like', '%aufzug%');
            })
            ->limit(6)
            ->get()
            ->map(function ($category) {
                return (object) [
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'description' => $category->description ?: 'Professionelles Equipment für dein Projekt',
                    'image' => $category->category_image ?: asset('assets/images/construction/default.svg')
                ];
            });

        // Return view with dynamic data
        return view('inlando.categories', compact(
            'categories',
            'eventItems',
            'vehicles',
            'constructionTools'
        ))->with('pageConfigs', $pageConfigs);
    }

    /**
     * Show the search results page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function search(Request $request)
    {
        // Configure layout options for Vuexy theme
        $pageConfigs = $this->getPageConfigs();

        // Get search parameters
        $query = $request->query('query');
        $location = $request->query('location');
        $dateFrom = $request->query('dateFrom');
        $dateTo = $request->query('dateTo');

        // Build the search query
        $rentals = \App\Models\Rental::with(['vendor', 'category', 'location'])
            ->where('status', 'active');

        // Filter by search query if provided
        if ($query) {
            $rentals->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            });
        }

        // Filter by location if provided
        if ($location) {
            $rentals->whereHas('location', function ($locationQuery) use ($location) {
                $locationQuery->where('name', 'like', "%{$location}%")
                    ->orWhere('city', 'like', "%{$location}%")
                    ->orWhere('postal_code', 'like', "%{$location}%");
            });
        }

        // Apply dynamic field filters
        $dynamicFilters = $request->input('filters', []);

        // Handle JSON encoded filters from URL
        if (is_string($dynamicFilters)) {
            $dynamicFilters = json_decode($dynamicFilters, true) ?: [];
        }

        if (!empty($dynamicFilters) && is_array($dynamicFilters)) {
            $rentals = DynamicRentalFields::applyFilters($rentals, $dynamicFilters);
        }

        // Get the results
        $rentals = $rentals->paginate(12);

        // If no results found and a search query was provided, show all rentals instead
        if ($rentals->count() == 0 && ($query || $location)) {
            $rentals = \App\Models\Rental::with(['vendor', 'category', 'location'])
                ->where('status', 'active')
                ->paginate(12);
        }

        return view('inlando.search-results', [
            'query' => $query,
            'location' => $location,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'rentals' => $rentals,
            'pageConfigs' => $pageConfigs,
        ]);
    }

    /**
     * Display a specific rental detail page.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function rentalDetail($id)
    {
        // Configure layout options for Vuexy theme
        $pageConfigs = $this->getPageConfigs();

        // Get the rental with related data including dynamic fields
        $rental = \App\Models\Rental::with(['vendor', 'category', 'location', 'fieldValues.field'])
            ->where('status', 'active')
            ->findOrFail($id);

        return view('inlando.rental-detail', [
            'rental' => $rental,
            'pageConfigs' => $pageConfigs,
        ]);
    }

    /**
     * Display a specific category.
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function show($slug)
    {
        // Configure layout options for Vuexy theme
        $pageConfigs = $this->getPageConfigs();

        // Get rentals for this category
        $rentals = \App\Models\Rental::with(['vendor', 'category', 'location'])
            ->where('status', 'active')
            ->whereHas('category', function ($q) use ($slug) {
                $q->where('slug', $slug)
                    ->orWhere('name', 'like', "%{$slug}%");
            })
            ->paginate(12);

        // If no results found for this category, show all rentals instead
        if ($rentals->count() == 0) {
            $rentals = \App\Models\Rental::with(['vendor', 'category', 'location'])
                ->where('status', 'active')
                ->paginate(12);
        }

        return view('inlando.search-results', [
            'query' => ucfirst($slug),
            'location' => null,
            'dateFrom' => null,
            'dateTo' => null,
            'rentals' => $rentals,
            'pageConfigs' => $pageConfigs,
        ]);
    }

    /**
     * Display the events category page.
     *
     * @return \Illuminate\View\View
     */
    public function events()
    {
        // Configure layout options for Vuexy theme
        $pageConfigs = $this->getPageConfigs();

        // Get event-related rentals
        $rentals = \App\Models\Rental::with(['vendor', 'category', 'location'])
            ->where('status', 'active')
            ->whereHas('category', function ($q) {
                $q->where('name', 'like', '%event%')
                    ->orWhere('name', 'like', '%bühne%')
                    ->orWhere('name', 'like', '%dekoration%')
                    ->orWhere('name', 'like', '%kostüm%')
                    ->orWhere('name', 'like', '%technik%');
            })
            ->paginate(12);

        // If no event rentals found, show all rentals instead
        if ($rentals->count() == 0) {
            $rentals = \App\Models\Rental::with(['vendor', 'category', 'location'])
                ->where('status', 'active')
                ->paginate(12);
        }

        return view('inlando.search-results', [
            'query' => 'Eventartikel',
            'location' => null,
            'dateFrom' => null,
            'dateTo' => null,
            'rentals' => $rentals,
            'pageConfigs' => $pageConfigs,
        ]);
    }

    /**
     * Display details for a specific event item.
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function eventDetails($slug)
    {
        // Configure layout options for Vuexy theme
        $pageConfigs = $this->getPageConfigs();

        // Get specific event item rentals
        $rentals = \App\Models\Rental::with(['vendor', 'category', 'location'])
            ->where('status', 'active')
            ->where(function ($q) use ($slug) {
                $q->where('title', 'like', "%{$slug}%")
                    ->orWhere('description', 'like', "%{$slug}%");
            })
            ->paginate(12);

        // If no specific event item rentals found, show all rentals instead
        if ($rentals->count() == 0) {
            $rentals = \App\Models\Rental::with(['vendor', 'category', 'location'])
                ->where('status', 'active')
                ->paginate(12);
        }

        return view('inlando.search-results', [
            'query' => ucfirst($slug),
            'location' => null,
            'dateFrom' => null,
            'dateTo' => null,
            'rentals' => $rentals,
            'pageConfigs' => $pageConfigs,
        ]);
    }

    /**
     * Display the vehicles category page.
     *
     * @return \Illuminate\View\View
     */
    public function vehicles()
    {
        // Configure layout options for Vuexy theme
        $pageConfigs = $this->getPageConfigs();

        // Get vehicle-related rentals
        $rentals = \App\Models\Rental::with(['vendor', 'category', 'location'])
            ->where('status', 'active')
            ->whereHas('category', function ($q) {
                $q->where('name', 'like', '%wohnmobil%')
                    ->orWhere('name', 'like', '%anhänger%')
                    ->orWhere('name', 'like', '%transporter%')
                    ->orWhere('name', 'like', '%fahrzeug%');
            })
            ->paginate(12);

        // If no vehicle rentals found, show all rentals instead
        if ($rentals->count() == 0) {
            $rentals = \App\Models\Rental::with(['vendor', 'category', 'location'])
                ->where('status', 'active')
                ->paginate(12);
        }

        return view('inlando.search-results', [
            'query' => 'Fahrzeuge',
            'location' => null,
            'dateFrom' => null,
            'dateTo' => null,
            'rentals' => $rentals,
            'pageConfigs' => $pageConfigs,
        ]);
    }

    /**
     * Display details for a specific vehicle.
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function vehicleDetails($slug)
    {
        // Configure layout options for Vuexy theme
        $pageConfigs = $this->getPageConfigs();

        // Get specific vehicle rentals
        $rentals = \App\Models\Rental::with(['vendor', 'category', 'location'])
            ->where('status', 'active')
            ->where(function ($q) use ($slug) {
                $q->where('title', 'like', "%{$slug}%")
                    ->orWhere('description', 'like', "%{$slug}%");
            })
            ->paginate(12);

        // If no specific vehicle rentals found, show all rentals instead
        if ($rentals->count() == 0) {
            $rentals = \App\Models\Rental::with(['vendor', 'category', 'location'])
                ->where('status', 'active')
                ->paginate(12);
        }

        return view('inlando.search-results', [
            'query' => ucfirst($slug),
            'location' => null,
            'dateFrom' => null,
            'dateTo' => null,
            'rentals' => $rentals,
            'pageConfigs' => $pageConfigs,
        ]);
    }

    /**
     * Display the construction equipment category page.
     *
     * @return \Illuminate\View\View
     */
    public function construction()
    {
        // Configure layout options for Vuexy theme
        $pageConfigs = $this->getPageConfigs();

        // Get construction equipment rentals
        $rentals = \App\Models\Rental::with(['vendor', 'category', 'location'])
            ->where('status', 'active')
            ->whereHas('category', function ($q) {
                $q->where('name', 'like', '%bau%')
                    ->orWhere('name', 'like', '%maschine%')
                    ->orWhere('name', 'like', '%abbruch%')
                    ->orWhere('name', 'like', '%bühne%')
                    ->orWhere('name', 'like', '%aufzug%');
            })
            ->paginate(12);

        // If no construction equipment rentals found, show all rentals instead
        if ($rentals->count() == 0) {
            $rentals = \App\Models\Rental::with(['vendor', 'category', 'location'])
                ->where('status', 'active')
                ->paginate(12);
        }

        return view('inlando.search-results', [
            'query' => 'Baumaschinen',
            'location' => null,
            'dateFrom' => null,
            'dateTo' => null,
            'rentals' => $rentals,
            'pageConfigs' => $pageConfigs,
        ]);
    }

    /**
     * Display details for a specific construction equipment.
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function constructionDetails($slug)
    {
        // Configure layout options for Vuexy theme
        $pageConfigs = $this->getPageConfigs();

        // Get specific construction equipment rentals
        $rentals = \App\Models\Rental::with(['vendor', 'category', 'location'])
            ->where('status', 'active')
            ->where(function ($q) use ($slug) {
                $q->where('title', 'like', "%{$slug}%")
                    ->orWhere('description', 'like', "%{$slug}%");
            })
            ->paginate(12);

        // If no specific construction equipment rentals found, show all rentals instead
        if ($rentals->count() == 0) {
            $rentals = \App\Models\Rental::with(['vendor', 'category', 'location'])
                ->where('status', 'active')
                ->paginate(12);
        }

        return view('inlando.search-results', [
            'query' => ucfirst($slug),
            'location' => null,
            'dateFrom' => null,
            'dateTo' => null,
            'rentals' => $rentals,
            'pageConfigs' => $pageConfigs,
        ]);
    }
}
