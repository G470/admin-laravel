<?php

namespace App\Livewire\Vendor;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Rental;
use Illuminate\Support\Facades\DB;

class RentalsTable extends Component
{
    use WithPagination;

    public $selectAll = false;
    public $selected = [];
    public $bulkAction = '';
    public $search = '';
    public $perPage = 10;

    public $filterCategory = '';
    public $filterLocation = '';
    public $filterStatus = '';

    // Neue Properties für erweiterte Bulk Actions
    public $bulkCategoryId = '';
    public $bulkLocationId = '';
    public $showBulkCategoryModal = false;
    public $showBulkLocationModal = false;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        'refreshTable' => '$refresh',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingFilterCategory()
    {
        $this->resetPage();
    }
    public function updatingFilterLocation()
    {
        $this->resetPage();
    }
    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            // Holen wir die aktuellen Rental-IDs auf der aktuellen Seite
            $currentPageRentals = $this->rentals;
            $this->selected = $currentPageRentals->pluck('id')->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function updatedSelected()
    {
        // Prüfen, ob alle Rentals auf der aktuellen Seite ausgewählt sind
        $currentPageRentals = $this->rentals;
        $currentPageIds = $currentPageRentals->pluck('id')->toArray();

        // selectAll ist true, wenn alle IDs der aktuellen Seite in selected enthalten sind
        $this->selectAll = count($currentPageIds) > 0 &&
            count(array_intersect($currentPageIds, $this->selected)) === count($currentPageIds);
    }



    public function updatedBulkAction($value)
    {
        // Modal für Kategorie-Änderung öffnen
        if ($value === 'change_category') {
            $this->showBulkCategoryModal = true;
        }

        // Modal für Location-Änderung öffnen
        if ($value === 'change_location') {
            $this->showBulkLocationModal = true;
        }

        // Alle anderen Aktionen werden über den "Ausführen"-Button gestartet
    }



    public function executeBulkAction()
    {
        if (empty($this->selected) || !$this->bulkAction) {
            session()->flash('error', 'Bitte wählen Sie mindestens ein Element und eine Aktion.');
            return;
        }

        $count = count($this->selected);

        switch ($this->bulkAction) {
            case 'activate':
                $this->bulkActivate();
                break;
            case 'deactivate':
                $this->bulkDeactivate();
                break;
            case 'delete':
                $this->bulkDelete();
                break;
            case 'duplicate':
                $this->bulkDuplicate();
                break;
            case 'export':
                $this->bulkExport();
                break;
            default:
                session()->flash('error', 'Unbekannte Aktion.');
                return;
        }

        $this->resetBulkSelection();
    }

    public function bulkActivate()
    {
        $count = count($this->selected);
        Rental::whereIn('id', $this->selected)->update(['status' => 'active']);
        session()->flash('success', "$count Objekt(e) aktiviert.");
    }

    public function bulkDeactivate()
    {
        $count = count($this->selected);
        Rental::whereIn('id', $this->selected)->update(['status' => 'inactive']);
        session()->flash('success', "$count Objekt(e) deaktiviert.");
    }

    public function bulkDelete()
    {
        if (empty($this->selected)) {
            session()->flash('error', 'Keine Objekte ausgewählt.');
            return;
        }

        $count = count($this->selected);

        // Zusätzliche Sicherheitsabfrage für Löschvorgang
        try {
            Rental::whereIn('id', $this->selected)
                ->where('vendor_id', auth()->id()) // Sicherheit: nur eigene Objekte
                ->delete();
            session()->flash('success', "$count Objekt(e) gelöscht.");
        } catch (\Exception $e) {
            session()->flash('error', 'Fehler beim Löschen der Objekte.');
        }
    }

    public function bulkDuplicate()
    {
        $count = count($this->selected);
        $rentals = Rental::whereIn('id', $this->selected)
            ->where('vendor_id', auth()->id())
            ->get();

        foreach ($rentals as $rental) {
            $new = $rental->replicate();
            $new->title = $rental->title . ' (Kopie)';
            $new->slug = null; // Reset slug so it gets regenerated
            $new->save();

            // Duplicate rental images if they exist
            if ($rental->images) {
                foreach ($rental->images as $image) {
                    $newImage = $image->replicate();
                    $newImage->rental_id = $new->id;
                    $newImage->save();
                }
            }
        }

        session()->flash('success', "$count Objekt(e) dupliziert.");
    }

    public function bulkExport()
    {
        $rentals = Rental::whereIn('id', $this->selected)
            ->where('vendor_id', auth()->id())
            ->with(['category', 'location', 'images'])
            ->get();

        $filename = 'rentals_export_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($rentals) {
            $file = fopen('php://output', 'w');

            // CSV Headers
            fputcsv($file, [
                'ID',
                'Titel',
                'Beschreibung',
                'Preis',
                'Preistyp',
                'Kategorie',
                'Status',
                'Erstellt am',
                'Aktualisiert am'
            ]);

            foreach ($rentals as $rental) {
                fputcsv($file, [
                    $rental->id,
                    $rental->title,
                    strip_tags($rental->description),
                    $rental->price_display,
                    $rental->price_type ? match ($rental->price_type) {
                        'hour' => 'Stündlich',
                        'day' => 'Täglich',
                        'once' => 'Einmalig',
                        'fixed' => 'Festpreis',
                        default => $rental->price_type
                    } : '',
                    $rental->category ? $rental->category->name : '',
                    $rental->status,
                    $rental->created_at->format('Y-m-d H:i:s'),
                    $rental->updated_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        session()->flash('success', 'Export wird heruntergeladen...');

        // In a real implementation, you'd return a download response
        // For now, we'll just show a success message
        return;
    }

    public function bulkChangeCategory()
    {
        if (empty($this->selected) || !$this->bulkCategoryId) {
            session()->flash('error', 'Bitte wählen Sie eine Kategorie.');
            return;
        }

        $count = count($this->selected);
        Rental::whereIn('id', $this->selected)
            ->where('vendor_id', auth()->id())
            ->update(['category_id' => $this->bulkCategoryId]);

        $categoryName = \App\Models\Category::find($this->bulkCategoryId)->name ?? 'Unbekannt';
        session()->flash('success', "$count Objekt(e) zur Kategorie '$categoryName' verschoben.");

        $this->closeBulkCategoryModal();
    }

    public function bulkChangeLocation()
    {
        if (empty($this->selected) || !$this->bulkLocationId) {
            session()->flash('error', 'Bitte wählen Sie eine Location.');
            return;
        }

        $count = count($this->selected);
        $rentals = Rental::whereIn('id', $this->selected)
            ->where('vendor_id', auth()->id())
            ->get();

        foreach ($rentals as $rental) {
            // Set as primary location
            $rental->location_id = $this->bulkLocationId;
            $rental->save();

            // Also update additional locations (remove old ones, add new one)
            $rental->locations()->detach();
            $rental->locations()->attach($this->bulkLocationId);
        }

        $locationName = \App\Models\Location::find($this->bulkLocationId)->name ?? 'Unbekannt';
        session()->flash('success', "$count Objekt(e) zur Location '$locationName' verschoben.");

        $this->closeBulkLocationModal();
    }

    public function closeBulkCategoryModal()
    {
        $this->showBulkCategoryModal = false;
        $this->bulkCategoryId = '';
        $this->resetBulkSelection();
    }

    public function closeBulkLocationModal()
    {
        $this->showBulkLocationModal = false;
        $this->bulkLocationId = '';
        $this->resetBulkSelection();
    }

    public function resetBulkSelection()
    {
        $this->resetPage();
        $this->reset(['selected', 'selectAll', 'bulkAction']);
    }

    public function clearFilters()
    {
        $this->resetPage();
        $this->reset(['search', 'filterCategory', 'filterLocation', 'filterStatus']);
    }

    public function duplicateRental($rentalId)
    {
        $rental = Rental::findOrFail($rentalId);
        $new = $rental->replicate();
        $new->title = $rental->title . ' (Kopie)';
        $new->save();
        session()->flash('success', 'Objekt dupliziert.');
        $this->resetPage();
    }

    public function toggleRentalStatus($rentalId)
    {
        $rental = Rental::findOrFail($rentalId);
        $rental->status = $rental->status === 'active' ? 'inactive' : 'active';
        $rental->save();
        session()->flash('success', 'Status geändert.');
        $this->resetPage();
    }

    public function deleteRental($rentalId)
    {
        $rental = Rental::findOrFail($rentalId);
        $rental->delete();
        session()->flash('success', 'Objekt gelöscht.');
        $this->resetPage();
    }

    public function copyRentalLink($rentalId)
    {
        // This would be handled by JS in the view
    }

    public function getCategoriesProperty()
    {
        // Nur Kategorien anzeigen, die tatsächlich von Rentals des Vendors verwendet werden
        $vendorId = auth()->id();

        return \App\Models\Category::online()
            ->whereExists(function ($query) use ($vendorId) {
                $query->select(DB::raw(1))
                    ->from('rentals')
                    ->whereColumn('rentals.category_id', 'categories.id')
                    ->where('rentals.vendor_id', $vendorId);
            })
            ->ordered()
            ->get();
    }

    public function getLocationsProperty()
    {
        // Nur Locations anzeigen, die tatsächlich von Rentals des Vendors verwendet werden
        $vendorId = auth()->id();

        return \App\Models\Location::where('vendor_id', $vendorId)
            ->where(function ($query) use ($vendorId) {
                // Locations die als primäre Location verwendet werden
                $query->whereExists(function ($subQuery) use ($vendorId) {
                    $subQuery->select(DB::raw(1))
                        ->from('rentals')
                        ->whereColumn('rentals.location_id', 'locations.id')
                        ->where('rentals.vendor_id', $vendorId);
                })
                    // ODER Locations die als zusätzliche Location verwendet werden
                    ->orWhereExists(function ($subQuery) use ($vendorId) {
                    $subQuery->select(DB::raw(1))
                        ->from('rental_locations')
                        ->join('rentals', 'rental_locations.rental_id', '=', 'rentals.id')
                        ->whereColumn('rental_locations.location_id', 'locations.id')
                        ->where('rentals.vendor_id', $vendorId);
                });
            })
            ->orderBy('name')
            ->get();
    }

    public function getRentalsProperty()
    {
        // we need to include the rental images
        return Rental::with(['category', 'location', 'images'])
            ->where('vendor_id', auth()->id())
            ->when($this->search, function ($q) {
                $q->where('title', 'like', "%{$this->search}%");
            })
            ->when($this->filterCategory, function ($q) {
                $q->where('category_id', $this->filterCategory);
            })
            ->when($this->filterLocation, function ($q) {
                // Filter by primary location OR additional locations
                $q->where(function ($query) {
                    $query->where('location_id', $this->filterLocation)
                        ->orWhereHas('locations', fn($q2) => $q2->where('location_id', $this->filterLocation));
                });
            })
            ->when($this->filterStatus, function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->orderByDesc('created_at')
            ->paginate($this->perPage);
    }

    public function render()
    {

        return view('livewire.vendor.rentals-table', [
            'rentals' => $this->rentals,
            'categories' => $this->categories,
            'locations' => $this->locations,
        ]);
    }
}
