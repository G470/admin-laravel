<?php

namespace App\Livewire\Vendor\Rentals;

use Livewire\Component;
use App\Models\Location;
use App\Models\MasterLocation;
use App\Models\Country;
use Illuminate\Support\Facades\Auth;

class Locations extends Component
{
    public $selectedLocations = [];
    public $searchTerm = '';
    public $filteredLocations = [];
    public $userLocations = [];
    public $locationsByCountry = []; // NEW: Grouped by country
    public $showCreateForm = false;

    // Form data for creating new location
    public $newLocation = [
        'street_address' => '',
        'additional_address' => '',
        'postal_code' => '',
        'city' => '',
        'country' => 'DE',
        'name' => '',
        'phone' => '',
        'description' => '',
        'is_main' => false,
        'latitude' => null,
        'longitude' => null
    ];

    // Initial data from parent component
    public $initialData = [];

        public function mount($initialData = [])
    {
        $this->initialData = $initialData;

        // Load user's existing locations
        $this->loadUserLocations();

        // Pre-select locations if provided
        if (isset($initialData['location_ids']) && is_array($initialData['location_ids'])) {
            $this->selectedLocations = array_map(function ($id) {
                return 'location-' . $id;
            }, $initialData['location_ids']);
        }
    }

    public function boot()
    {
        // Emit initial location update after component is fully loaded
        if (!empty($this->selectedLocations)) {
            $this->dispatch('locationsUpdated', [
                'locations' => $this->selectedLocations,
                'location_ids' => $this->getLocationIds()
            ]);
        }
    }

    public function loadUserLocations()
    {
        $user = Auth::user();
        $this->userLocations = Location::where('vendor_id', $user->id)
            ->where('is_active', true)
            ->orderBy('is_main', 'desc')
            ->orderBy('name')
            ->get()
            ->map(function ($location) {
                return [
                    'id' => $location->id,
                    'name' => $location->name ?: ($location->city . ', ' . $location->postal_code),
                    'full_address' => trim($location->street_address . ' ' . $location->additional_address . ', ' . $location->postal_code . ' ' . $location->city),
                    'is_main' => $location->is_main,
                    'city' => $location->city,
                    'postal_code' => $location->postal_code,
                    'country' => $location->country
                ];
            })
            ->toArray();

        // Group locations by country
        $this->groupLocationsByCountry();
    }

    public function groupLocationsByCountry()
    {
        $this->locationsByCountry = collect($this->userLocations)
            ->groupBy('country')
            ->map(function ($locations, $country) {
                return [
                    'country_code' => $country,
                    'country_name' => $this->getCountryName($country),
                    'locations' => $locations->toArray(),
                    'count' => $locations->count()
                ];
            })
            ->toArray();
    }

    private function getCountryName($countryCode)
    {
        $country = Country::where('code', $countryCode)
            ->where('is_active', true)
            ->first();

        return $country ? $country->name : $countryCode;
    }

    // NEW: Bulk Actions
    public function selectAllLocations()
    {
        $this->selectedLocations = array_map(function ($location) {
            return 'location-' . $location['id'];
        }, $this->userLocations);

        $this->emitLocationUpdate();
    }

    public function deselectAllLocations()
    {
        $this->selectedLocations = [];
        $this->emitLocationUpdate();
    }

    public function selectLocationsByCountry($countryCode)
    {
        $countryLocationIds = collect($this->userLocations)
            ->where('country', $countryCode)
            ->map(function ($location) {
                return 'location-' . $location['id'];
            })
            ->toArray();

        // Add to existing selection (don't replace)
        $this->selectedLocations = array_unique(
            array_merge($this->selectedLocations, $countryLocationIds)
        );

        $this->emitLocationUpdate();
    }

    public function deselectLocationsByCountry($countryCode)
    {
        $countryLocationIds = collect($this->userLocations)
            ->where('country', $countryCode)
            ->map(function ($location) {
                return 'location-' . $location['id'];
            })
            ->toArray();

        // Remove from existing selection
        $this->selectedLocations = array_diff($this->selectedLocations, $countryLocationIds);

        $this->emitLocationUpdate();
    }

    public function updatedSearchTerm()
    {
        if (strlen($this->searchTerm) >= 2) {
            $this->searchLocations();
        } else {
            $this->filteredLocations = [];
        }
    }

    public function searchLocations()
    {
        $this->filteredLocations = MasterLocation::forCountry($this->newLocation['country'])
            ->search($this->searchTerm)
            ->orderBy('city')
            ->orderBy('postcode')
            ->limit(10)
            ->get()
            ->map(function ($location) {
                return [
                    'id' => $location->id,
                    'name' => $location->display_name,
                    'city' => $location->city,
                    'postcode' => $location->postcode,
                    'country' => strtoupper($location->country),
                    'display' => "{$location->city} ({$location->postcode})"
                ];
            })
            ->toArray();
    }

    public function selectMasterLocation($masterLocationId)
    {
        $masterLocation = MasterLocation::find($masterLocationId);
        if ($masterLocation) {
            $this->newLocation['postal_code'] = $masterLocation->postcode;
            $this->newLocation['city'] = $masterLocation->city;
            $this->newLocation['country'] = strtoupper($masterLocation->country);
            $this->searchTerm = $masterLocation->display_name;
            $this->filteredLocations = [];
        }
    }

    public function toggleLocation($locationKey)
    {
        $index = array_search($locationKey, $this->selectedLocations);

        if ($index !== false) {
            // Remove from selection
            unset($this->selectedLocations[$index]);
            $this->selectedLocations = array_values($this->selectedLocations);
        } else {
            // Add to selection
            $this->selectedLocations[] = $locationKey;
        }

        // Emit event to parent component
        $this->dispatch('locationsUpdated', [
            'locations' => $this->selectedLocations,
            'location_ids' => $this->getLocationIds()
        ]);
    }

    public function getLocationIds()
    {
        return array_map(function ($locationKey) {
            return (int) str_replace('location-', '', $locationKey);
        }, $this->selectedLocations);
    }

    // NEW: Helper method to emit location updates for bulk actions
    private function emitLocationUpdate()
    {
        // Emit event to parent component
        $this->dispatch('locationsUpdated', [
            'locations' => $this->selectedLocations,
            'location_ids' => $this->getLocationIds()
        ]);
    }

    public function showCreateLocationForm()
    {
        $this->showCreateForm = true;
        $this->resetNewLocationForm();
    }

    public function hideCreateLocationForm()
    {
        $this->showCreateForm = false;
        $this->resetNewLocationForm();
    }

    public function resetNewLocationForm()
    {
        $this->newLocation = [
            'street_address' => '',
            'additional_address' => '',
            'postal_code' => '',
            'city' => '',
            'country' => 'DE',
            'name' => '',
            'phone' => '',
            'description' => '',
            'is_main' => false,
            'latitude' => null,
            'longitude' => null
        ];
        $this->searchTerm = '';
        $this->filteredLocations = [];
    }

    public function createLocation()
    {
        $this->validate([
            'newLocation.street_address' => 'required|string|max:255',
            'newLocation.postal_code' => 'required|string|max:20',
            'newLocation.city' => 'required|string|max:255',
            'newLocation.country' => 'required|string|max:2',
            'newLocation.name' => 'nullable|string|max:255',
            'newLocation.phone' => 'nullable|string|max:20',
            'newLocation.description' => 'nullable|string',
        ]);

        $user = Auth::user();

        // If this is marked as main location, unset other main locations
        if ($this->newLocation['is_main']) {
            Location::where('vendor_id', $user->id)->update(['is_main' => false]);
        }

        // Map country code to country_id from database
        $country = \App\Models\Country::where('code', $this->newLocation['country'])->first();
        $countryId = $country ? $country->id : 1; // Default to Germany if not found

        $location = Location::create([
            'vendor_id' => $user->id,
            'name' => $this->newLocation['name'] ?: ($this->newLocation['city'] . ', ' . $this->newLocation['postal_code']),
            'street_address' => $this->newLocation['street_address'],
            'additional_address' => $this->newLocation['additional_address'],
            'postal_code' => $this->newLocation['postal_code'],
            'city' => $this->newLocation['city'],
            'country' => $this->newLocation['country'],
            'country_id' => $countryId,
            'phone' => $this->newLocation['phone'],
            'description' => $this->newLocation['description'],
            'is_main' => $this->newLocation['is_main'],
            'is_active' => true,
            'latitude' => $this->newLocation['latitude'],
            'longitude' => $this->newLocation['longitude']
        ]);

        // Auto-select the newly created location
        $this->selectedLocations[] = 'location-' . $location->id;

        // Reload user locations
        $this->loadUserLocations();

        // Hide form and reset
        $this->hideCreateLocationForm();

        // Emit success message
        $this->dispatch('locationCreated', [
            'message' => 'Standort erfolgreich erstellt und ausgewählt!',
            'location' => $location
        ]);

        // Update parent component
        $this->dispatch('locationsUpdated', [
            'locations' => $this->selectedLocations,
            'location_ids' => $this->getLocationIds()
        ]);
    }

    public function render()
    {
        return view('livewire.vendor.rentals.locations');
    }
}
