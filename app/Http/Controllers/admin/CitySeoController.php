<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CitySeo;
use App\Models\Category;
use App\Models\Rental;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CitySeoController extends Controller
{
    public function index()
    {
        $cities = CitySeo::orderBy('sort_order')->orderBy('name')->get();
        return view('content.admin.cities-seo', compact('cities'));
    }

    public function create()
    {
        $categories = \App\Models\Category::online()->ordered()->get();
        return view('content.admin.cities-seo-create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'required|string|max:2',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'status' => 'required|in:online,offline',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'featured_image' => 'nullable|image|max:2048',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'population' => 'nullable|integer|min:0',
        ]);

        // Generate slug if not provided
        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['city'] . '-' . $validated['country']);

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('cities', 'public');
        }

        CitySeo::create($validated);

        // Clear cache and trigger sitemap regeneration
        \Cache::forget('sitemap_category_locations');

        return redirect()->route('admin.cities-seo.index')
            ->with('success', 'Location SEO Eintrag wurde erfolgreich erstellt.');
    }

    public function edit(CitySeo $citySeo)
    {
        $categories = \App\Models\Category::online()->ordered()->get();
        return view('content.admin.cities-seo-edit', compact('citySeo', 'categories'));
    }

    public function update(Request $request, CitySeo $citySeo)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'required|string|max:2',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'status' => 'required|in:online,offline',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'featured_image' => 'nullable|image|max:2048',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'population' => 'nullable|integer|min:0',
        ]);

        // Generate slug if not provided
        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['city'] . '-' . $validated['country']);

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('cities', 'public');
        }

        $citySeo->update($validated);

        return redirect()->route('admin.cities-seo.index')
            ->with('success', 'Location SEO Eintrag wurde erfolgreich aktualisiert.');
    }

    public function destroy(CitySeo $citySeo)
    {
        // Check if there are rentals in this city before deleting
        $relatedRentalsCount = $citySeo->getRelatedRentalsCount();
        
        if ($relatedRentalsCount > 0) {
            return redirect()->route('admin.cities-seo.index')
                ->with('error', "Diese Location kann nicht gelöscht werden, da sie noch {$relatedRentalsCount} Vermietungsobjekte enthält.");
        }

        $citySeo->delete();

        return redirect()->route('admin.cities-seo.index')
            ->with('success', 'Location SEO Eintrag wurde erfolgreich gelöscht.');
    }
}