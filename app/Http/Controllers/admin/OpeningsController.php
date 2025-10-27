<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Opening;
use App\Models\Location;

class OpeningsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Opening::with(['location', 'location.user']);
        
        // Filter by location if specified
        if ($request->has('location_id') && $request->location_id) {
            $query->where('location_id', $request->location_id);
        }
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->whereHas('location', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('address', 'like', '%' . $request->search . '%');
            });
        }
        
        $openings = $query->orderBy('location_id')
                         ->orderBy('day_of_week')
                         ->paginate(15);
        
        // Get all locations for filter dropdown
        $locations = Location::with('user')
                            ->orderBy('name')
                            ->get();
        
        return view('admin.openings.index', compact('openings', 'locations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $locations = Location::with('user')
                            ->orderBy('name')
                            ->get();
        
        $selectedLocationId = $request->get('location_id');
        
        return view('admin.openings.create', compact('locations', 'selectedLocationId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'location_id' => 'required|exists:locations,id',
            'day_of_week' => 'required|integer|between:0,6', // 0 = Sunday, 6 = Saturday
            'open_time' => 'required|date_format:H:i',
            'close_time' => 'required|date_format:H:i|after:open_time',
            'is_closed' => 'boolean',
            'break_start' => 'nullable|date_format:H:i',
            'break_end' => 'nullable|date_format:H:i|after:break_start',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            // Check if opening hours already exist for this location and day
            $existing = Opening::where('location_id', $validated['location_id'])
                              ->where('day_of_week', $validated['day_of_week'])
                              ->first();
            
            if ($existing) {
                return back()->withErrors(['day_of_week' => 'Öffnungszeiten für diesen Tag existieren bereits.'])
                           ->withInput();
            }
            
            Opening::create($validated);
            
            return redirect()->route('admin.openings.index')
                           ->with('success', 'Öffnungszeiten erfolgreich erstellt!');
                           
        } catch (\Exception $e) {
            Log::error('Failed to create opening hours: ' . $e->getMessage());
            return back()->withErrors(['general' => 'Fehler beim Erstellen der Öffnungszeiten.'])
                        ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $opening = Opening::with(['location', 'location.user'])->findOrFail($id);
        
        return view('admin.openings.show', compact('opening'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $opening = Opening::with('location')->findOrFail($id);
        
        $locations = Location::with('user')
                            ->orderBy('name')
                            ->get();
        
        return view('admin.openings.edit', compact('opening', 'locations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $opening = Opening::findOrFail($id);
        
        $validated = $request->validate([
            'location_id' => 'required|exists:locations,id',
            'day_of_week' => 'required|integer|between:0,6',
            'open_time' => 'required|date_format:H:i',
            'close_time' => 'required|date_format:H:i|after:open_time',
            'is_closed' => 'boolean',
            'break_start' => 'nullable|date_format:H:i',
            'break_end' => 'nullable|date_format:H:i|after:break_start',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            // Check if opening hours already exist for this location and day (excluding current record)
            $existing = Opening::where('location_id', $validated['location_id'])
                              ->where('day_of_week', $validated['day_of_week'])
                              ->where('id', '!=', $opening->id)
                              ->first();
            
            if ($existing) {
                return back()->withErrors(['day_of_week' => 'Öffnungszeiten für diesen Tag existieren bereits.'])
                           ->withInput();
            }
            
            $opening->update($validated);
            
            return redirect()->route('admin.openings.index')
                           ->with('success', 'Öffnungszeiten erfolgreich aktualisiert!');
                           
        } catch (\Exception $e) {
            Log::error('Failed to update opening hours: ' . $e->getMessage());
            return back()->withErrors(['general' => 'Fehler beim Aktualisieren der Öffnungszeiten.'])
                        ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $opening = Opening::findOrFail($id);
            $opening->delete();
            
            return redirect()->route('admin.openings.index')
                           ->with('success', 'Öffnungszeiten erfolgreich gelöscht!');
                           
        } catch (\Exception $e) {
            Log::error('Failed to delete opening hours: ' . $e->getMessage());
            return back()->withErrors(['general' => 'Fehler beim Löschen der Öffnungszeiten.']);
        }
    }

    /**
     * Bulk create opening hours for a location
     */
    public function bulkCreate(Request $request)
    {
        $validated = $request->validate([
            'location_id' => 'required|exists:locations,id',
            'days' => 'required|array|min:1',
            'days.*' => 'integer|between:0,6',
            'open_time' => 'required|date_format:H:i',
            'close_time' => 'required|date_format:H:i|after:open_time',
            'break_start' => 'nullable|date_format:H:i',
            'break_end' => 'nullable|date_format:H:i|after:break_start',
        ]);

        try {
            $created = 0;
            $skipped = 0;
            
            foreach ($validated['days'] as $dayOfWeek) {
                // Check if opening hours already exist
                $existing = Opening::where('location_id', $validated['location_id'])
                                  ->where('day_of_week', $dayOfWeek)
                                  ->first();
                
                if ($existing) {
                    $skipped++;
                    continue;
                }
                
                Opening::create([
                    'location_id' => $validated['location_id'],
                    'day_of_week' => $dayOfWeek,
                    'open_time' => $validated['open_time'],
                    'close_time' => $validated['close_time'],
                    'break_start' => $validated['break_start'],
                    'break_end' => $validated['break_end'],
                    'is_closed' => false,
                ]);
                
                $created++;
            }
            
            $message = "Öffnungszeiten erstellt: {$created}";
            if ($skipped > 0) {
                $message .= ", übersprungen (bereits vorhanden): {$skipped}";
            }
            
            return redirect()->route('admin.openings.index')
                           ->with('success', $message);
                           
        } catch (\Exception $e) {
            Log::error('Failed to bulk create opening hours: ' . $e->getMessage());
            return back()->withErrors(['general' => 'Fehler beim Erstellen der Öffnungszeiten.'])
                        ->withInput();
        }
    }

    /**
     * Get opening hours for a specific location (AJAX)
     */
    public function getByLocation($locationId)
    {
        try {
            $openings = Opening::where('location_id', $locationId)
                              ->orderBy('day_of_week')
                              ->get();
            
            return response()->json([
                'success' => true,
                'openings' => $openings
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Laden der Öffnungszeiten.'
            ], 500);
        }
    }
}
