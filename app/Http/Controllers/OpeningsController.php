<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OpeningsController extends Controller
{
    // Main opening hours index page
    public function indexMain()
    {
        $user = Auth::user();
        
        // Load vendor's default opening hours
        $defaultOpenings = DB::table('vendor_default_openings')
            ->where('vendor_id', $user->id)
            ->orderBy('day_of_week')
            ->get()
            ->keyBy('day_of_week');
        
        // Load all vendor locations with their opening hours
        $locations = Location::where('vendor_id', $user->id)
            ->where('is_active', true)
            ->with(['country'])
            ->get();
        
        // For each location, determine if it uses custom or default hours
        $locationsWithStatus = $locations->map(function ($location) {
            $customOpenings = DB::table('openings')
                ->where('location_id', $location->id)
                ->exists();
                
            $location->uses_custom_hours = $customOpenings;
            $location->opening_status = $customOpenings ? 'custom' : 'default';
            
            // Ensure country relationship is properly loaded
            if (!$location->country && $location->country_id) {
                $location->load('country');
            }
            
            return $location;
        });
        
        return view('content.vendor.openings.index-main', compact('defaultOpenings', 'locationsWithStatus'));
    }

    // Update vendor default opening hours
    public function updateDefaults(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'days' => 'required|array',
            'days.*.is_open' => 'nullable|in:0,1',
            'days.*.open_time' => 'nullable|string',
            'days.*.close_time' => 'nullable|string',
            'days.*.has_break' => 'nullable|in:0,1',
            'days.*.break_start' => 'nullable|string',
            'days.*.break_end' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Clear existing default hours
            DB::table('vendor_default_openings')
                ->where('vendor_id', $user->id)
                ->delete();

            // Insert new default hours
            foreach ($validated['days'] as $dayId => $dayData) {
                $isOpen = !empty($dayData['is_open']) && $dayData['is_open'] !== '0';
                $hasBreak = !empty($dayData['has_break']) && $dayData['has_break'] !== '0';
                
                if ($isOpen) {
                    DB::table('vendor_default_openings')->insert([
                        'vendor_id' => $user->id,
                        'day_of_week' => $dayId,
                        'open_time' => $dayData['open_time'],
                        'close_time' => $dayData['close_time'],
                        'is_closed' => false,
                        'break_start' => $hasBreak ? $dayData['break_start'] : null,
                        'break_end' => $hasBreak ? $dayData['break_end'] : null,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
            
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Standard-Öffnungszeiten wurden aktualisiert.']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Fehler beim Speichern: ' . $e->getMessage()], 500);
        }
    }

    // Remove location-specific opening hours
    public function removeLocationHours($locationId)
    {
        $user = Auth::user();
        
        $location = Location::where('vendor_id', $user->id)->findOrFail($locationId);
        
        DB::table('openings')->where('location_id', $location->id)->delete();
        
        return redirect()->route('vendor-openings-index')
            ->with('success', 'Individuelle Öffnungszeiten für ' . $location->name . ' wurden entfernt. Es gelten wieder die Standard-Öffnungszeiten.');
    }
    // Öffnungszeiten-Übersicht
    public function index(Request $request)
    {
        $location = null;
        if ($request->has('location_id')) {
            $location = Location::where('vendor_id', auth()->id())
                ->findOrFail($request->location_id);
        }
        return view('content.vendor.openings.index', compact('location'));
    }

    // Öffnungszeiten erstellen/bearbeiten
    public function opening($locationId = null)
    {
        $location = null;
        $existingOpenings = [];
        
        if ($locationId) {
            $location = Location::where('vendor_id', auth()->id())
                ->findOrFail($locationId);
                
            // Load existing opening hours
            $openings = DB::table('openings')
                ->where('location_id', $location->id)
                ->get()
                ->keyBy('day_of_week');
                
            $existingOpenings = $openings;
        }
        
        return view('content.vendor.openings.index', compact('location', 'existingOpenings'));
    }

    public function update(Request $request, $locationId)
    {
        $location = Location::where('vendor_id', auth()->id())
            ->findOrFail($locationId);

        $validated = $request->validate([
            'days' => 'required|array',
            'days.*.is_open' => 'nullable|in:0,1',
            'days.*.open_time' => 'nullable|string',
            'days.*.close_time' => 'nullable|string',
            'days.*.has_break' => 'nullable|in:0,1',
            'days.*.break_start' => 'nullable|string',
            'days.*.break_end' => 'nullable|string',
        ]);

        // Convert string values to boolean
        foreach ($validated['days'] as &$dayData) {
            $dayData['is_open'] = !empty($dayData['is_open']) && $dayData['is_open'] !== '0';
            $dayData['has_break'] = !empty($dayData['has_break']) && $dayData['has_break'] !== '0';
        }

        // Map day numbers to day names
        $dayNames = [
            1 => 'Montag',
            2 => 'Dienstag', 
            3 => 'Mittwoch',
            4 => 'Donnerstag',
            5 => 'Freitag',
            6 => 'Samstag',
            7 => 'Sonntag'
        ];

        try {
            // Delete existing opening hours for this location
            DB::table('openings')->where('location_id', $location->id)->delete();

            // Save new opening hours
            foreach ($validated['days'] as $dayId => $dayData) {
                // Skip if day is not open
                if (!$dayData['is_open']) {
                    continue;
                }

                $openingData = [
                    'location_id' => $location->id,
                    'day_of_week' => $dayId,
                    'open_time' => $dayData['open_time'] ?? null,
                    'close_time' => $dayData['close_time'] ?? null,
                    'is_closed' => false,
                    'break_start' => $dayData['has_break'] ? ($dayData['break_start'] ?? null) : null,
                    'break_end' => $dayData['has_break'] ? ($dayData['break_end'] ?? null) : null,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                DB::table('openings')->insert($openingData);
            }

            return redirect()->route('vendor-openings-edit', ['locationId' => $locationId])
                ->with('success', 'Öffnungszeiten wurden erfolgreich aktualisiert.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Fehler beim Speichern der Öffnungszeiten: ' . $e->getMessage());
        }
    }
}