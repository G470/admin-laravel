<?php

namespace App\Http\Controllers\Inlando;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\Category;
use App\Models\Location;
use App\Helpers\DynamicRentalFields;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SearchController extends Controller
{
    /**
     * Display the search results page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Rental::where('status', 'active');

        // Search by keyword - handle both 'q' and 'query' parameters
        $searchTerm = $request->input('query') ?: $request->input('q');
        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by category - handle both 'category' and 'category_id' parameters
        $categoryId = $request->input('category') ?: $request->input('category_id');
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        // second check, always test if search query is a category name. If true, get the category id
        if ($searchTerm) {
            $category = Category::where('name', 'like', "%{$searchTerm}%")->first();
            if ($category) {
                $categoryId = $category->id;
            }
        }

        // Filter by location
        if ($request->filled('location')) {
            $query->whereHas('location', function ($q) use ($request) {
                $q->where('city', 'like', "%{$request->input('location')}%")
                    ->orWhere('name', 'like', "%{$request->input('location')}%");
            });
        }

        // Filter by country
        if ($request->filled('country')) {
            $query->whereHas('location', function ($q) use ($request) {
                $q->where('country', $request->input('country'));
            });
        }

        // Filter by countryCode
        if ($request->filled('countryCode')) {
            $query->whereHas('location', function ($q) use ($request) {
                $q->where('country', $request->input('countryCode'));
            });
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }

        // Apply dynamic field filters
        $dynamicFilters = $request->input('filters', []);

        // Handle JSON encoded filters from URL
        if (is_string($dynamicFilters)) {
            $dynamicFilters = json_decode($dynamicFilters, true) ?: [];
        }

        if (!empty($dynamicFilters) && is_array($dynamicFilters)) {
            $query = DynamicRentalFields::applyFilters($query, $dynamicFilters);
        }

        $rentals = $query->with(['category', 'location', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // Apply push boosts to search results
        $rentals = $this->applyPushBoosts($rentals, $categoryId, $request->input('location'));

        // If no results found and a search query was provided, show all rentals instead
        if ($rentals->count() == 0 && ($searchTerm || $request->filled('location') || $categoryId)) {
            $rentals = Rental::where('status', 'active')
                ->with(['category', 'location', 'user'])
                ->orderBy('created_at', 'desc')
                ->paginate(12);

            // Apply push boosts to fallback results
            $rentals = $this->applyPushBoosts($rentals, $categoryId, $request->input('location'));
        }

        $categories = Category::all();

        // Pass search parameters to view - normalize parameter names
        $searchParams = [
            'query' => $searchTerm,
            'location' => $request->input('location'),
            'dateFrom' => $request->input('dateFrom') ?: $request->input('date_from'),
            'dateTo' => $request->input('dateTo') ?: $request->input('date_to'),
            'categoryId' => $categoryId,
            'minPrice' => $request->input('min_price'),
            'maxPrice' => $request->input('max_price'),
            'dynamicFilters' => $dynamicFilters,
        ];

        return view('inlando.search-results', compact('rentals', 'categories') + $searchParams);
    }

    /**
     * Apply push boosts to search results
     *
     * @param \Illuminate\Pagination\LengthAwarePaginator $rentals
     * @param int|null $categoryId
     * @param string|null $location
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    protected function applyPushBoosts($rentals, $categoryId, $location)
    {
        $boostedRentals = collect();
        $normalRentals = collect();

        foreach ($rentals as $rental) {
            $pushBoost = $this->getPushBoost($rental->id, $categoryId, $rental->location_id);

            if ($pushBoost) {
                // Add boosted rental to the top
                $boostedRentals->push($rental);
            } else {
                // Keep normal rental in original order
                $normalRentals->push($rental);
            }
        }

        // Combine boosted and normal rentals
        $combinedRentals = $boostedRentals->merge($normalRentals);

        // Create a new paginator with the reordered results
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $combinedRentals,
            $rentals->total(),
            $rentals->perPage(),
            $rentals->currentPage(),
            [
                'path' => $rentals->path(),
                'pageName' => $rentals->getPageName(),
            ]
        );
    }

    /**
     * Get push boost for a rental
     *
     * @param int $rentalId
     * @param int|null $categoryId
     * @param int|null $locationId
     * @return array|null
     */
    protected function getPushBoost($rentalId, $categoryId, $locationId)
    {
        if (!$categoryId || !$locationId) {
            return null;
        }

        $cacheKey = "rental_push_{$rentalId}_{$categoryId}_{$locationId}";
        return Cache::get($cacheKey);
    }
}
