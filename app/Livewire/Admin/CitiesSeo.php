<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CitySeo;
use App\Models\Category;
use App\Models\MasterLocation;
use Illuminate\Support\Str;

class CitiesSeo extends Component
{
    use WithPagination;

    // Search and filtering
    public $search = '';
    public $statusFilter = '';
    public $categoryFilter = '';
    public $countryFilter = '';
    
    // Modal state
    public $showModal = false;
    public $editMode = false;
    public $cityId = null;
    
    // Form data
    public $name = '';
    public $slug = '';
    public $status = 'online';
    public $city = '';
    public $state = '';
    public $country = 'DE';
    public $meta_title = '';
    public $meta_description = '';
    public $meta_keywords = '';
    public $content = '';
    public $description = '';
    public $category_id = null;
    public $latitude = '';
    public $longitude = '';
    public $population = '';
    
    // Available options
    public $countries = [
        'DE' => 'Deutschland',
        'AT' => 'Österreich', 
        'CH' => 'Schweiz'
    ];
    
    public $statusOptions = [
        'online' => 'Online',
        'offline' => 'Offline'
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'countryFilter' => ['except' => '']
    ];

    protected $rules = [
        'name' => 'required|string|max:255',
        'city' => 'required|string|max:255',
        'state' => 'nullable|string|max:255',
        'country' => 'required|string|max:2',
        'meta_title' => 'nullable|string|max:255',
        'meta_description' => 'nullable|string|max:500',
        'meta_keywords' => 'nullable|string|max:255',
        'content' => 'nullable|string',
        'description' => 'nullable|string',
        'category_id' => 'nullable|exists:categories,id',
        'latitude' => 'nullable|numeric|between:-90,90',
        'longitude' => 'nullable|numeric|between:-180,180',
        'population' => 'nullable|integer|min:0',
        'status' => 'required|in:online,offline'
    ];

    public function mount()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatingCountryFilter()
    {
        $this->resetPage();
    }

    public function showCreateModal()
    {
        $this->resetForm();
        $this->editMode = false;
        $this->showModal = true;
    }

    public function showEditModal($cityId)
    {
        $city = CitySeo::findOrFail($cityId);
        
        $this->cityId = $city->id;
        $this->name = $city->name ?? '';
        $this->slug = $city->slug ?? '';
        $this->status = $city->status ?? 'online';
        $this->city = $city->city ?? '';
        $this->state = $city->state ?? '';
        $this->country = $city->country ?? 'DE';
        $this->meta_title = $city->meta_title ?? '';
        $this->meta_description = $city->meta_description ?? '';
        $this->meta_keywords = $city->meta_keywords ?? '';
        $this->content = $city->content ?? '';
        $this->description = $city->description ?? '';
        $this->category_id = $city->category_id ?? null;
        $this->latitude = $city->latitude ?? '';
        $this->longitude = $city->longitude ?? '';
        $this->population = $city->population ?? '';
        
        $this->editMode = true;
        $this->showModal = true;
    }

    public function saveCity()
    {
        $this->validate();

        // Generate slug if not provided
        if (empty($this->slug)) {
            $this->slug = Str::slug($this->city . '-' . $this->country);
        }

        $data = [
            'name' => $this->name ?: $this->city,
            'slug' => $this->slug,
            'status' => $this->status,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'content' => $this->content,
            'description' => $this->description,
            'category_id' => $this->category_id,
            'latitude' => $this->latitude ?: null,
            'longitude' => $this->longitude ?: null,
            'population' => $this->population ?: null,
        ];

        if ($this->editMode) {
            $city = CitySeo::findOrFail($this->cityId);
            $city->update($data);
            $this->dispatch('city-updated', ['message' => 'Stadt wurde erfolgreich aktualisiert!']);
        } else {
            CitySeo::create($data);
            $this->dispatch('city-created', ['message' => 'Stadt wurde erfolgreich erstellt!']);
        }

        $this->resetForm();
        $this->showModal = false;
    }

    public function confirmDelete($cityId)
    {
        $this->dispatch('confirm-delete', ['id' => $cityId]);
    }

    public function deleteCity($cityId)
    {
        $city = CitySeo::findOrFail($cityId);
        
        // Check if city has associated rentals
        if ($city->rentals()->exists()) {
            $this->dispatch('delete-error', ['message' => 'Diese Stadt kann nicht gelöscht werden, da sie noch Vermietungsobjekte enthält.']);
            return;
        }
        
        $city->delete();
        $this->dispatch('city-deleted', ['message' => 'Stadt wurde erfolgreich gelöscht!']);
    }

    public function toggleStatus($cityId)
    {
        $city = CitySeo::findOrFail($cityId);
        $city->update(['status' => $city->status === 'online' ? 'offline' : 'online']);
        
        $this->dispatch('status-updated', [
            'message' => 'Status wurde erfolgreich geändert!'
        ]);
    }

    public function updateSortOrder($orderedIds)
    {
        foreach ($orderedIds as $index => $id) {
            CitySeo::where('id', $id)->update(['sort_order' => $index + 1]);
        }
        
        $this->dispatch('sort-updated', ['message' => 'Reihenfolge wurde aktualisiert!']);
    }

    public function searchMasterLocations()
    {
        if (strlen($this->city) < 2) {
            return [];
        }

        return MasterLocation::forCountry($this->country)
            ->search($this->city)
            ->limit(10)
            ->get()
            ->map(function ($location) {
                return [
                    'id' => $location->id,
                    'display' => $location->display_name,
                    'city' => $location->city,
                    'postcode' => $location->postcode,
                    'state' => $location->state,
                    'lat' => $location->lat,
                    'lng' => $location->lng
                ];
            });
    }

    public function selectMasterLocation($locationData)
    {
        $this->city = $locationData['city'];
        $this->state = $locationData['state'] ?? '';
        $this->latitude = $locationData['lat'] ?? '';
        $this->longitude = $locationData['lng'] ?? '';
        
        // Auto-generate name and slug
        $this->name = $this->city . ($this->state ? ', ' . $this->state : '') . ', ' . $this->countries[$this->country];
        $this->slug = Str::slug($this->city . '-' . $this->country);
    }

    private function resetForm()
    {
        $this->cityId = null;
        $this->name = '';
        $this->slug = '';
        $this->status = 'online';
        $this->city = '';
        $this->state = '';
        $this->country = 'DE';
        $this->meta_title = '';
        $this->meta_description = '';
        $this->meta_keywords = '';
        $this->content = '';
        $this->description = '';
        $this->category_id = null;
        $this->latitude = '';
        $this->longitude = '';
        $this->population = '';
    }

    public function render()
    {
        $cities = CitySeo::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('city', 'like', '%' . $this->search . '%')
                      ->orWhere('slug', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->countryFilter, function ($query) {
                $query->where('country', $this->countryFilter);
            })
            ->when($this->categoryFilter, function ($query) {
                $query->where('category_id', $this->categoryFilter);
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        $categories = Category::online()->ordered()->get();

        return view('livewire.admin.cities-seo', [
            'cities' => $cities,
            'categories' => $categories
        ]);
    }
}
