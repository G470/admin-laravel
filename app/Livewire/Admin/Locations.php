<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Location;
use App\Models\User;
use App\Models\Country;
use Illuminate\Database\Eloquent\Builder;

class Locations extends Component
{
    use WithPagination;

    // Filter properties
    public $search = '';
    public $countryFilter = '';
    public $statusFilter = '';
    public $vendorFilter = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $perPage = 15;

    // Modal properties
    public $showDeleteModal = false;
    public $locationToDelete = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'countryFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'vendorFilter' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function mount()
    {
        // Initialize component
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCountryFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingVendorFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->countryFilter = '';
        $this->statusFilter = '';
        $this->vendorFilter = '';
        $this->sortField = 'name';
        $this->sortDirection = 'asc';
        $this->resetPage();
    }

    public function confirmDelete($locationId)
    {
        $this->locationToDelete = $locationId;
        $this->showDeleteModal = true;
    }

    public function deleteLocation()
    {
        if ($this->locationToDelete) {
            $location = Location::find($this->locationToDelete);

            if ($location) {
                // Check if location has rentals
                $rentalCount = $location->rentals()->count();

                if ($rentalCount > 0) {
                    session()->flash('error', "Standort kann nicht gelöscht werden. {$rentalCount} Vermietungen sind damit verknüpft.");
                } else {
                    $locationName = $location->name;
                    $location->delete();
                    session()->flash('success', "Standort '{$locationName}' wurde erfolgreich gelöscht.");
                }
            }
        }

        $this->closeDeleteModal();
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->locationToDelete = null;
    }

    public function toggleStatus($locationId)
    {
        $location = Location::find($locationId);

        if ($location) {
            $location->is_active = !$location->is_active;
            $location->save();

            $status = $location->is_active ? 'aktiviert' : 'deaktiviert';
            session()->flash('success', "Standort '{$location->name}' wurde {$status}.");
        }
    }

    public function getLocationsProperty()
    {
        return Location::query()
            ->with(['vendor:id,name,email', 'country:id,name,code', 'rentals:id,location_id'])
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('city', 'like', '%' . $this->search . '%')
                        ->orWhere('street_address', 'like', '%' . $this->search . '%')
                        ->orWhere('postal_code', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->countryFilter, function (Builder $query) {
                if ($this->countryFilter === 'DE') {
                    $query->where('country', 'Deutschland');
                } elseif ($this->countryFilter === 'AT') {
                    $query->where('country', 'Österreich');
                } elseif ($this->countryFilter === 'CH') {
                    $query->where('country', 'Schweiz');
                } else {
                    $query->where('country', $this->countryFilter);
                }
            })
            ->when($this->statusFilter, function (Builder $query) {
                if ($this->statusFilter === 'active') {
                    $query->where('is_active', true);
                } elseif ($this->statusFilter === 'inactive') {
                    $query->where('is_active', false);
                }
            })
            ->when($this->vendorFilter, function (Builder $query) {
                $query->where('vendor_id', $this->vendorFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function getVendorsProperty()
    {
        return User::where('is_vendor', true)
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();
    }

    public function getCountriesProperty()
    {
        return [
            'DE' => 'Deutschland',
            'AT' => 'Österreich',
            'CH' => 'Schweiz',
            'FR' => 'Frankreich',
            'IT' => 'Italien',
            'NL' => 'Niederlande',
        ];
    }

    public function getStatisticsProperty()
    {
        $totalLocations = Location::count();
        $activeLocations = Location::where('is_active', true)->count();
        $locationsWithRentals = Location::whereHas('rentals')->count();
        $newLocations = Location::where('created_at', '>=', now()->subDays(30))->count();

        return [
            'total' => $totalLocations,
            'active' => $activeLocations,
            'with_rentals' => $locationsWithRentals,
            'new_this_month' => $newLocations,
        ];
    }

    public function render()
    {
        return view('livewire.admin.locations', [
            'locations' => $this->locations,
            'vendors' => $this->vendors,
            'countries' => $this->countries,
            'statistics' => $this->statistics,
        ]);
    }
}