<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Rental;
use App\Models\User;
use App\Models\Category;

class RentalTable extends Component
{
    use WithPagination;

    public $search = '';
    public $vendorFilter = '';
    public $categoryFilter = '';
    public $statusFilter = '';
    public $perPage = 10;
    public $sortField = 'title';
    public $sortDirection = 'asc';

    // Inline-Bearbeitung Eigenschaften
    public $editingRentalId = null;
    public $editingField = null;
    public $editingValue = '';
    public $showEditModal = false;
    public $editRental = null;

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        //
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingVendorFilter()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
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
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->vendorFilter = '';
        $this->categoryFilter = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    // Inline-Bearbeitung Funktionen
    public function startEditing($rentalId, $field, $value = '')
    {
        $this->editingRentalId = $rentalId;
        $this->editingField = $field;
        $this->editingValue = $value;
    }

    public function cancelEditing()
    {
        $this->editingRentalId = null;
        $this->editingField = null;
        $this->editingValue = '';
    }

    public function saveEdit()
    {
        try {
            $rental = Rental::find($this->editingRentalId);

            if (!$rental) {
                session()->flash('error', 'Vermietungsobjekt nicht gefunden.');
                $this->cancelEditing();
                return;
            }

            // Validierung basierend auf Feld
            $validationRules = [];
            switch ($this->editingField) {
                case 'name':
                    $validationRules = ['editingValue' => 'required|string|max:255'];
                    $this->editingField = 'title'; // Map to actual database field
                    break;
                case 'price':
                    $validationRules = ['editingValue' => 'required|numeric|min:0'];
                    $this->editingField = 'price_range_hour'; // Map to actual database field
                    break;
                case 'vendor_id':
                    $validationRules = ['editingValue' => 'required|exists:users,id'];
                    break;
                case 'category_id':
                    $validationRules = ['editingValue' => 'required|exists:categories,id'];
                    break;
                case 'status':
                    $validationRules = ['editingValue' => 'required|in:active,inactive,pending,rejected'];
                    break;
            }

            $this->validate($validationRules);

            // Speichern
            $rental->update([$this->editingField => $this->editingValue]);

            session()->flash('success', 'Vermietungsobjekt wurde erfolgreich aktualisiert.');
            $this->cancelEditing();
        } catch (\Exception $e) {
            session()->flash('error', 'Fehler beim Aktualisieren: ' . $e->getMessage());
        }
    }

    public function openEditModal($rentalId)
    {
        $this->editRental = Rental::with(['vendor', 'category', 'city'])->find($rentalId);
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editRental = null;
    }

    public function updateRental()
    {
        try {
            $this->validate([
                'editRental.title' => 'required|string|max:255',
                'editRental.price_range_hour' => 'required|numeric|min:0',
                'editRental.vendor_id' => 'required|exists:users,id',
                'editRental.category_id' => 'required|exists:categories,id',
                'editRental.status' => 'required|in:active,inactive,pending,rejected',
                'editRental.address' => 'nullable|string|max:500',
                'editRental.description' => 'nullable|string',
            ]);

            $this->editRental->save();
            session()->flash('success', 'Vermietungsobjekt wurde erfolgreich aktualisiert.');
            $this->closeEditModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Fehler beim Aktualisieren: ' . $e->getMessage());
        }
    }

    public function deleteRental($rentalId)
    {
        try {
            $rental = Rental::find($rentalId);

            if (!$rental) {
                session()->flash('error', 'Vermietungsobjekt nicht gefunden.');
                return;
            }

            if ($rental->bookings()->exists()) {
                session()->flash('error', 'Dieses Vermietungsobjekt kann nicht gelöscht werden, da es noch Buchungen enthält.');
                return;
            }

            $rental->delete();
            session()->flash('success', 'Vermietungsobjekt wurde erfolgreich gelöscht.');
        } catch (\Exception $e) {
            session()->flash('error', 'Fehler beim Löschen des Vermietungsobjekts: ' . $e->getMessage());
        }
    }

    public function toggleStatus($rentalId)
    {
        try {
            $rental = Rental::find($rentalId);

            if (!$rental) {
                session()->flash('error', 'Vermietungsobjekt nicht gefunden.');
                return;
            }

            $newStatus = $rental->status === 'active' ? 'inactive' : 'active';
            $rental->update(['status' => $newStatus]);
            session()->flash('success', 'Status wurde erfolgreich aktualisiert.');
        } catch (\Exception $e) {
            session()->flash('error', 'Fehler beim Ändern des Status: ' . $e->getMessage());
        }
    }

    public function toggleFeatured($rentalId)
    {
        try {
            $rental = Rental::find($rentalId);

            if (!$rental) {
                session()->flash('error', 'Vermietungsobjekt nicht gefunden.');
                return;
            }

            $rental->update(['featured' => !$rental->featured]);
            session()->flash('success', 'Featured-Status wurde erfolgreich aktualisiert.');
        } catch (\Exception $e) {
            session()->flash('error', 'Fehler beim Ändern des Featured-Status: ' . $e->getMessage());
        }
    }

    public function getRentalsProperty()
    {
        $query = Rental::with(['vendor', 'category', 'city']);

        // Apply filters
        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }

        if ($this->vendorFilter) {
            $query->whereHas('vendor', function ($q) {
                // TODO: Change to name
                $q->where('name', 'like', '%' . $this->vendorFilter . '%');
            });
        }

        if ($this->categoryFilter) {
            $query->whereHas('category', function ($q) {
                $q->where('name', 'like', '%' . $this->categoryFilter . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // Apply sorting with proper relationship handling
        switch ($this->sortField) {
            case 'vendor_id':
                $query->join('users', 'rentals.vendor_id', '=', 'users.id')
                    ->orderBy('users.id', $this->sortDirection)
                    ->select('rentals.*');
                break;
            case 'category_id':
                $query->join('categories', 'rentals.category_id', '=', 'categories.id')
                    ->orderBy('categories.name', $this->sortDirection)
                    ->select('rentals.*');
                break;
            case 'name':
                $query->orderBy('title', $this->sortDirection);
                break;
            case 'price':
                $query->orderBy('price_range_hour', $this->sortDirection);
                break;
            default:
                $query->orderBy($this->sortField, $this->sortDirection);
                break;
        }

        return $query->paginate($this->perPage);
    }

    public function getVendorsProperty()
    {
        return User::where('is_vendor', true)->orderBy('id')->get();
    }

    public function getCategoriesProperty()
    {
        return Category::orderBy('name')->get();
    }

    public function getStatusOptionsProperty()
    {
        return [
            'active' => 'Aktiv',
            'inactive' => 'Inaktiv',
            'pending' => 'Prüfung ausstehend',
            'rejected' => 'Abgelehnt'
        ];
    }

    public function render()
    {
        return view('livewire.admin.rental-table', [
            'rentals' => $this->rentals,
            'vendors' => $this->vendors,
            'categories' => $this->categories,
            'statusOptions' => $this->statusOptions,
        ]);
    }
}
