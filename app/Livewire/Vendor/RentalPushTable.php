<?php

namespace App\Livewire\Vendor;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\RentalPush;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Support\Facades\Auth;

class RentalPushTable extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
    public $locationFilter = '';
    public $statusFilter = '';
    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'locationFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'perPage' => ['except' => 10],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc']
    ];

    public function mount()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatingLocationFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
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
    }

    public function clearFilters()
    {
        $this->reset(['search', 'categoryFilter', 'locationFilter', 'statusFilter']);
        $this->resetPage();
    }

    public function toggleStatus($pushId)
    {
        $push = RentalPush::where('id', $pushId)
            ->where('vendor_id', Auth::id())
            ->firstOrFail();

        $newStatus = $push->status === 'active' ? 'paused' : 'active';
        $push->update(['status' => $newStatus]);

        $statusLabel = $newStatus === 'active' ? 'aktiviert' : 'pausiert';
        session()->flash('success', "Artikel-Push wurde erfolgreich {$statusLabel}!");
    }

    public function deletePush($pushId)
    {
        $push = RentalPush::where('id', $pushId)
            ->where('vendor_id', Auth::id())
            ->firstOrFail();

        $push->update([
            'status' => 'cancelled',
            'is_active' => false
        ]);

        session()->flash('success', 'Artikel-Push wurde erfolgreich abgebrochen!');
    }

    public function getRentalPushesProperty()
    {
        $query = RentalPush::where('vendor_id', Auth::id())
            ->with(['rental', 'category', 'location']);

        // Apply filters
        if ($this->search) {
            $query->whereHas('rental', function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->categoryFilter) {
            $query->where('category_id', $this->categoryFilter);
        }

        if ($this->locationFilter) {
            $query->where('location_id', $this->locationFilter);
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // Apply sorting
        switch ($this->sortField) {
            case 'rental':
                $query->join('rentals', 'rental_pushes.rental_id', '=', 'rentals.id')
                    ->orderBy('rentals.title', $this->sortDirection)
                    ->select('rental_pushes.*');
                break;
            case 'category':
                $query->join('categories', 'rental_pushes.category_id', '=', 'categories.id')
                    ->orderBy('categories.name', $this->sortDirection)
                    ->select('rental_pushes.*');
                break;
            case 'location':
                $query->join('locations', 'rental_pushes.location_id', '=', 'locations.id')
                    ->orderBy('locations.name', $this->sortDirection)
                    ->select('rental_pushes.*');
                break;
            default:
                $query->orderBy($this->sortField, $this->sortDirection);
                break;
        }

        return $query->paginate($this->perPage);
    }

    public function getCategoriesProperty()
    {
        return Category::orderBy('name')->get();
    }

    public function getLocationsProperty()
    {
        return Location::orderBy('name')->get();
    }

    public function getStatusOptionsProperty()
    {
        return RentalPush::getStatusOptions();
    }

    public function render()
    {
        return view('livewire.vendor.rental-push-table');
    }
}
