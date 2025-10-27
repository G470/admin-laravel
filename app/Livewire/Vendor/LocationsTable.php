<?php

namespace App\Livewire\Vendor;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Location;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class LocationsTable extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $sortField = 'id';
    public $sortDirection = 'asc';

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        //
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function clearSearch()
    {
        $this->search = '';
        $this->resetPage();
    }

    public function deleteLocation($locationId)
    {
        try {
            $user = Auth::user();
            
            $query = Location::where('id', $locationId);
            
            // If not admin, only allow access to own locations
            if (!$user->is_admin) {
                $query->where('vendor_id', $user->id);
            }
            
            $location = $query->first();
            
            if (!$location) {
                session()->flash('error', 'Standort nicht gefunden oder keine Berechtigung.');
                return;
            }
            
            // Check if it's the main location
            if ($location->is_main) {
                session()->flash('error', 'Der Hauptstandort kann nicht gelöscht werden. Legen Sie zuerst einen anderen Standort als Hauptstandort fest.');
                return;
            }
            
            // Check if location has rentals
            if ($location->rentals()->exists()) {
                session()->flash('error', 'Dieser Standort kann nicht gelöscht werden, da er noch Vermietungsobjekte enthält.');
                return;
            }
            
            $location->delete();
            session()->flash('success', 'Standort wurde erfolgreich gelöscht.');
        } catch (\Exception $e) {
            session()->flash('error', 'Fehler beim Löschen des Standorts: ' . $e->getMessage());
        }
    }

    public function setMainLocation($locationId)
    {
        try {
            $user = Auth::user();
            
            $query = Location::where('id', $locationId);
            
            // If not admin, only allow access to own locations
            if (!$user->is_admin) {
                $query->where('vendor_id', $user->id);
            }
            
            $location = $query->first();
            
            if (!$location) {
                session()->flash('error', 'Standort nicht gefunden oder keine Berechtigung.');
                return;
            }
            
            // Start transaction
            DB::transaction(function () use ($location, $user) {
                // Remove main status from all other locations
                $locationQuery = Location::where('vendor_id', $location->vendor_id);
                if (!$user->is_admin) {
                    $locationQuery->where('vendor_id', $user->id);
                }
                $locationQuery->update(['is_main' => false]);
                
                // Set this location as main
                $location->update(['is_main' => true]);
            });
            
            session()->flash('success', 'Hauptstandort wurde erfolgreich aktualisiert.');
        } catch (\Exception $e) {
            session()->flash('error', 'Fehler beim Setzen des Hauptstandorts: ' . $e->getMessage());
        }
    }

    public function getLocationsProperty()
    {
        $user = Auth::user();
        
        $query = Location::with('vendor');
        
        // If not admin, only show own locations
        if (!$user->is_admin) {
            $query->where('vendor_id', $user->id);
        }
        
        // Apply search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('street_address', 'like', '%' . $this->search . '%')
                  ->orWhere('city', 'like', '%' . $this->search . '%')
                  ->orWhere('postal_code', 'like', '%' . $this->search . '%')
                  ->orWhere('name', 'like', '%' . $this->search . '%');
            });
        }
        
        // Apply sorting
        $query->orderBy($this->sortField, $this->sortDirection);
        
        return $query->paginate($this->perPage);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.vendor.locations-table', [
            'locations' => $this->locations,
        ]);
    }
}
