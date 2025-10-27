<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\Category;
use App\Models\CitySeo;
use App\Helpers\DynamicRentalFields;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RentalController extends Controller
{
    public function index()
    {
        // Livewire component handles all data loading
        return view('content.admin.rentals');
    }

    public function show(Rental $rental)
    {
        $rental->load(['vendor', 'category', 'city', 'location', 'bookings', 'reviews', 'fieldValues']);

        return view('content.admin.rentals.show', compact('rental'));
    }

    public function edit(Rental $rental)
    {
        $rental->load(['vendor', 'category', 'city', 'location', 'fieldValues']);
        $vendors = \App\Models\User::where('is_vendor', true)->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $cities = CitySeo::orderBy('city')->get();
        $locations = \App\Models\Location::orderBy('name')->get();

        return view('content.admin.rentals.edit', compact('rental', 'vendors', 'categories', 'cities', 'locations'));
    }

    public function create()
    {
        $vendors = \App\Models\User::where('is_vendor', true)->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $cities = CitySeo::orderBy('city')->get();

        // Temporarily redirect to index until create view is created
        return redirect()->route('admin.rentals.index')->with('info', 'Erstellungs-Funktion wird bald implementiert.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'vendor_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'city_id' => 'required|exists:city_seos,id',
            'address' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'status' => 'required|in:active,inactive,pending',
            'featured' => 'boolean',
            'images' => 'required|array|min:1',
            'images.*' => 'image|max:2048',
            'amenities' => 'nullable|array',
            'rules' => 'nullable|array',
            'cancellation_policy' => 'nullable|string',
            'availability' => 'nullable|array',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'dynamic_fields' => 'nullable|array',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('images')) {
            $validated['images'] = collect($request->file('images'))->map(function ($image) {
                return $image->store('rentals', 'public');
            })->toArray();
        }

        $rental = Rental::create($validated);

        // Save dynamic field values
        if (isset($validated['dynamic_fields'])) {
            DynamicRentalFields::saveFieldValues($rental->id, $validated['dynamic_fields']);
        }

        return redirect()->route('admin.rentals.index')
            ->with('success', 'Vermietungsobjekt wurde erfolgreich erstellt.');
    }

    public function update(Request $request, Rental $rental)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'vendor_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'nullable|exists:locations,id',
            'city_id' => 'nullable|exists:city_seos,id',
            'price_range_hour' => 'required|numeric|min:0',
            'price_range_day' => 'nullable|numeric|min:0',
            'price_range_once' => 'nullable|numeric|min:0',
            'service_fee' => 'nullable|numeric|min:0',
            'currency' => 'required|string|max:3',
            'status' => 'required|in:active,inactive,pending,rejected',
            'featured' => 'boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
            'amenities' => 'nullable|array',
            'rules' => 'nullable|array',
            'cancellation_policy' => 'nullable|string',
            'availability' => 'nullable|array',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'dynamic_fields' => 'nullable|array',
        ]);

        // Handle featured checkbox
        $validated['featured'] = $request->has('featured');

        // Handle images
        if ($request->hasFile('images')) {
            $validated['images'] = collect($request->file('images'))->map(function ($image) {
                return $image->store('rentals', 'public');
            })->toArray();
        }

        $rental->update($validated);

        // Save dynamic field values
        if (isset($validated['dynamic_fields'])) {
            DynamicRentalFields::saveFieldValues($rental->id, $validated['dynamic_fields']);
        }

        return redirect()->route('admin.rentals.index')
            ->with('success', 'Vermietungsobjekt wurde erfolgreich aktualisiert.');
    }

    public function destroy(Rental $rental)
    {
        if ($rental->bookings()->exists()) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dieses Vermietungsobjekt kann nicht gelöscht werden, da es noch Buchungen enthält.'
                ], 400);
            }

            return redirect()->route('admin.rentals.index')
                ->with('error', 'Dieses Vermietungsobjekt kann nicht gelöscht werden, da es noch Buchungen enthält.');
        }

        $rental->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Vermietungsobjekt wurde erfolgreich gelöscht.'
            ]);
        }

        return redirect()->route('admin.rentals.index')
            ->with('success', 'Vermietungsobjekt wurde erfolgreich gelöscht.');
    }

    public function toggleStatus(Rental $rental)
    {
        $newStatus = $rental->status === 'active' ? 'inactive' : 'active';
        $rental->update(['status' => $newStatus]);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status wurde erfolgreich aktualisiert.',
                'new_status' => $newStatus
            ]);
        }

        return redirect()->route('admin.rentals.index')
            ->with('success', 'Status wurde erfolgreich aktualisiert.');
    }

    public function toggleFeatured(Rental $rental)
    {
        $rental->update(['featured' => !$rental->featured]);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Featured-Status wurde erfolgreich aktualisiert.',
                'featured' => $rental->featured
            ]);
        }

        return redirect()->route('admin.rentals.index')
            ->with('success', 'Featured-Status wurde erfolgreich aktualisiert.');
    }
}