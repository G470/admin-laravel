<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Country;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class LocationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Standorte-Übersicht
    public function index()
    {
        // Livewire component handles all data loading
        return view('content.vendor.locations.index');
    }

    // Standort erstellen - Formular anzeigen
    public function create()
    {
        $location = null; // Für neue Standorte
        $openingHours = []; // No opening hours for new locations
        $countries = Country::where('is_active', true)->orderBy('name')->get();
        return view('content.vendor.locations.edit', compact('location', 'openingHours', 'countries'));
    }

    // Standort erstellen/bearbeiten
    public function location($locationId = null)
    {
        $user = Auth::user();
        $location = null;
        $openingHours = [];
        $countries = Country::where('is_active', true)->orderBy('name')->get();

        if ($locationId) {
            // Wenn Admin, erlaube Zugriff auf alle Standorte
            if ($user->is_admin) {
                $location = Location::findOrFail($locationId);
            } else {
                $location = Location::where('id', $locationId)
                    ->where('vendor_id', $user->id ?? 0)
                    ->firstOrFail();
            }

            // Load opening hours for this location
            $openingHours = DB::table('openings')
                ->where('location_id', $location->id)
                ->orderBy('day_of_week')
                ->get()
                ->keyBy('day_of_week')
                ->toArray();
        }

        return view('content.vendor.locations.edit', compact('location', 'openingHours', 'countries'));
    }

    public function update(Request $request, $locationId = null)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'street_address' => 'required|string|max:255',
            'additional_address' => 'nullable|string|max:255',
            'postal_code' => 'required|string|max:20',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:2|exists:countries,code',
            'location_name' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'location_description' => 'nullable|string',
            'is_main' => 'nullable|in:0,1',
            'is_active' => 'nullable|in:0,1',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180'
        ]);

        // Convert string values to boolean
        $validated['is_main'] = !empty($validated['is_main']) && $validated['is_main'] !== '0';
        $validated['is_active'] = !empty($validated['is_active']) && $validated['is_active'] !== '0';

        // Map country code to country_id from database
        $country = \App\Models\Country::where('code', $validated['country'])->first();
        $validated['country_id'] = $country ? $country->id : 1; // Default to ID 1 (Germany) if not found

        try {
            DB::beginTransaction();

            if ($locationId) {
                // Wenn Admin, erlaube Zugriff auf alle Standorte
                if ($user->is_admin) {
                    $location = Location::findOrFail($locationId);
                } else {
                    $location = Location::where('id', $locationId)
                        ->where('vendor_id', $user->id)
                        ->firstOrFail();
                }
            } else {
                $location = new Location();
                $location->vendor_id = $user->id ?? 0;
            }

            // Wenn dieser Standort als Hauptstandort markiert wird, setze alle anderen auf false
            if ($validated['is_main']) {
                // Wenn Admin, aktualisiere nur die Standorte des entsprechenden Vendors
                if ($user->is_admin) {
                    Location::where('vendor_id', $location->vendor_id)
                        ->where('id', '!=', $locationId)
                        ->update(['is_main' => false]);
                } else {
                    Location::where('vendor_id', $user->id ?? 0)
                        ->where('id', '!=', $locationId)
                        ->update(['is_main' => false]);
                }
            }

            // Get country_id from country code
            $country = Country::where('code', $validated['country'])->first();
            $countryId = $country ? $country->id : 1; // Default to first country if not found

            $location->fill([
                'name' => $validated['location_name'],
                'street_address' => $validated['street_address'],
                'additional_address' => $validated['additional_address'],
                'postal_code' => $validated['postal_code'],
                'city' => $validated['city'],
                'country' => $validated['country'],
                'country_id' => $countryId,
                'phone' => $validated['contact_phone'],
                'description' => $validated['location_description'],
                'is_main' => $validated['is_main'],
                'is_active' => $validated['is_active'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude']
            ]);

            $location->save();

            DB::commit();

            return redirect()->route('vendor-locations')
                ->with('success', $locationId ? 'Standort wurde aktualisiert.' : 'Standort wurde erstellt.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Fehler beim Speichern des Standorts: ' . $e->getMessage());
        }
    }

    // Standort speichern (POST für neue Standorte)
    public function store(Request $request)
    {
        return $this->update($request, null);
    }

    // Standort speichern (alternative Methode für Routen-Kompatibilität)
    public function save(Request $request, $locationId = null)
    {
        return $this->update($request, $locationId);
    }

    // Standort löschen
    public function destroy($locationId)
    {
        $user = Auth::user();

        try {
            DB::beginTransaction();

            // Wenn Admin, erlaube Zugriff auf alle Standorte
            if ($user->is_admin) {
                $location = Location::findOrFail($locationId);
            } else {
                $location = Location::where('id', $locationId)
                    ->where('vendor_id', $user->id)
                    ->firstOrFail();
            }

            // Prüfe, ob es der Hauptstandort ist
            if ($location->is_main) {
                return redirect()->route('vendor-locations')
                    ->with('error', 'Der Hauptstandort kann nicht gelöscht werden. Legen Sie zuerst einen anderen Standort als Hauptstandort fest.');
            }

            // Prüfe, ob der Standort noch Vermietungsobjekte hat
            if ($location->rentals()->exists()) {
                return redirect()->route('vendor-locations')
                    ->with('error', 'Dieser Standort kann nicht gelöscht werden, da er noch Vermietungsobjekte enthält.');
            }

            $location->delete();

            DB::commit();

            return redirect()->route('vendor-locations')
                ->with('success', 'Standort wurde erfolgreich gelöscht.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('vendor-locations')
                ->with('error', 'Fehler beim Löschen des Standorts: ' . $e->getMessage());
        }
    }

    // Hauptstandort setzen
    public function setMain($locationId)
    {
        $user = Auth::user();

        try {
            DB::beginTransaction();

            // Wenn Admin, erlaube Zugriff auf alle Standorte
            if ($user->is_admin) {
                $location = Location::findOrFail($locationId);
            } else {
                $location = Location::where('id', $locationId)
                    ->where('vendor_id', $user->id)
                    ->firstOrFail();
            }

            // Setze alle anderen Standorte des Vendors auf nicht-Hauptstandort
            if ($user->is_admin) {
                Location::where('vendor_id', $location->vendor_id)
                    ->update(['is_main' => false]);
            } else {
                Location::where('vendor_id', $user->id ?? 0)
                    ->update(['is_main' => false]);
            }

            // Setze diesen Standort als Hauptstandort
            $location->update(['is_main' => true]);

            DB::commit();

            return redirect()->route('vendor-locations')
                ->with('success', 'Hauptstandort wurde erfolgreich aktualisiert.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('vendor-locations')
                ->with('error', 'Fehler beim Setzen des Hauptstandorts: ' . $e->getMessage());
        }
    }
}