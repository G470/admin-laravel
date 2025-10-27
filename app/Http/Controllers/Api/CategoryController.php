<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * Get category suggestions for search autocomplete
     */
    public function suggestions(Request $request): JsonResponse
    {
        $query = $request->get('query', '');
        $limit = min($request->get('limit', 10), 20); // Max 20 results

        // Validate minimum query length
        if (strlen(trim($query)) < 2) {
            return response()->json([
                'categories' => [],
                'searchOptions' => [],
                'query' => $query
            ]);
        }
        try {
            // Get matching categories
            $categories = Category::where('name', 'LIKE', '%' . $query . '%')
                ->where('status', 'online')
                ->orderBy('name', 'asc')
                ->limit($limit)
                ->get(['id', 'name', 'slug']);

            // Generate search options for the query in specific categories
            $searchOptions = [];
            if (!empty(trim($query))) {
                // Get top categories for "search in category" options
                // Get up to 6 most popular categories, or fallback to any available categories
                $topCategories = Category::where('status', 'online')
                    ->orderBy('name', 'asc')
                    ->limit(4)
                    ->get(['id', 'name', 'slug']);

                foreach ($topCategories as $category) {
                    $searchOptions[] = [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'searchText' => $query . ' in ' . $category->name
                    ];
                }

                // Always add "search in all categories" option
                $searchOptions[] = [
                    'id' => null,
                    'name' => 'allen Kategorien',
                    'slug' => null,
                    'searchText' => $query . ' in allen Kategorien'
                ];
            }

            return response()->json([
                'categories' => $categories->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'type' => 'category'
                    ];
                }),
                'searchOptions' => $searchOptions,
                'query' => $query
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'categories' => [],
                'searchOptions' => [],
                'error' => 'Fehler beim Laden der Kategorien',
                'query' => $query
            ], 500);
        }
    }

    /**
     * Get a single category by ID
     */
    public function show($id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);

            return response()->json([
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'status' => $category->status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Kategorie nicht gefunden'
            ], 404);
        }
    }
}
