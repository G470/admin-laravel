<?php

namespace App\Services\Frontend;

use App\Models\Rental;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class SearchService
{
    /**
     * Perform rental search with filters
     */
    public function searchRentals(Request $request): array
    {
        $query = Rental::with(['category', 'location', 'images', 'vendor'])
            ->where('status', 'active');

        // Apply search filters
        $this->applySearchFilters($query, $request);
        $this->applyCategoryFilter($query, $request);
        $this->applyLocationFilter($query, $request);
        $this->applyPriceFilter($query, $request);

        // Get results with pagination
        $rentals = $query->orderBy('created_at', 'desc')->paginate(12);

        return [
            'rentals' => $rentals,
            'total_count' => $rentals->total(),
            'filters_applied' => $this->getAppliedFilters($request),
        ];
    }

    /**
     * Apply text search filters
     */
    private function applySearchFilters(Builder $query, Request $request): void
    {
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhereHas('category', function ($categoryQuery) use ($search) {
                      $categoryQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }
    }

    /**
     * Apply category filter
     */
    private function applyCategoryFilter(Builder $query, Request $request): void
    {
        if ($categoryId = $request->get('category_id')) {
            $query->where('category_id', $categoryId);
        }
    }

    /**
     * Apply location filter
     */
    private function applyLocationFilter(Builder $query, Request $request): void
    {
        if ($location = $request->get('location')) {
            $query->whereHas('location', function ($locationQuery) use ($location) {
                $locationQuery->where('city', 'LIKE', "%{$location}%")
                             ->orWhere('street', 'LIKE', "%{$location}%");
            });
        }
    }

    /**
     * Apply price range filter
     */
    private function applyPriceFilter(Builder $query, Request $request): void
    {
        if ($minPrice = $request->get('min_price')) {
            $query->where(function ($q) use ($minPrice) {
                $q->where('price_range_hour', '>=', $minPrice)
                  ->orWhere('price_range_day', '>=', $minPrice)
                  ->orWhere('price_range_once', '>=', $minPrice);
            });
        }

        if ($maxPrice = $request->get('max_price')) {
            $query->where(function ($q) use ($maxPrice) {
                $q->where('price_range_hour', '<=', $maxPrice)
                  ->orWhere('price_range_day', '<=', $maxPrice)
                  ->orWhere('price_range_once', '<=', $maxPrice);
            });
        }
    }

    /**
     * Get applied filters for display
     */
    private function getAppliedFilters(Request $request): array
    {
        $filters = [];

        if ($search = $request->get('search')) {
            $filters['search'] = $search;
        }

        if ($categoryId = $request->get('category_id')) {
            $category = Category::find($categoryId);
            $filters['category'] = $category ? $category->name : 'Unknown';
        }

        if ($location = $request->get('location')) {
            $filters['location'] = $location;
        }

        if ($request->get('min_price') || $request->get('max_price')) {
            $filters['price_range'] = [
                'min' => $request->get('min_price'),
                'max' => $request->get('max_price'),
            ];
        }

        return $filters;
    }

    /**
     * Get popular search categories
     */
    public function getPopularCategories(int $limit = 6): array
    {
        return Category::withCount('rentals')
            ->orderByDesc('rentals_count')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
