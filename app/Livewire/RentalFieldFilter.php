<?php

namespace App\Livewire;

use App\Models\RentalField;
use App\Models\Rental;
use App\Models\RentalFieldValue;
use Livewire\Component;
use Livewire\WithPagination;

class RentalFieldFilter extends Component
{
    use WithPagination;

    public $categoryId;
    public $filters = [];
    public $availableFields = [];
    public $filterValues = [];

    protected $queryString = ['filters'];

    public function mount($categoryId = null)
    {
        $this->categoryId = $categoryId;
        $this->loadAvailableFields($categoryId);
        $this->loadFilterValues();
    }

    public function loadAvailableFields($categoryId = null)
    {
        if ($categoryId) {
            $this->availableFields = RentalField::whereHas('template.categories', function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
                ->where('is_filterable', true)
                ->with('template')
                ->get();
        } else {
            /* $this->availableFields = RentalField::where('is_filterable', true)
                  ->with('template')
                  ->get();
                  */
        }
    }

    public function loadFilterValues()
    {
        foreach ($this->availableFields as $field) {
            $values = RentalFieldValue::where('field_id', $field->id)
                ->distinct()
                ->pluck('field_value')
                ->filter()
                ->values();

            if ($field->type === 'select' || $field->type === 'radio') {
                $this->filterValues[$field->id] = $field->options ?? [];
            } else {
                $this->filterValues[$field->id] = $values->toArray();
            }
        }
    }

    public function applyFilter($fieldId, $value)
    {
        if ($value) {
            $this->filters[$fieldId] = $value;
        } else {
            unset($this->filters[$fieldId]);
        }

        $this->resetPage();
        $this->dispatch('filters-updated', filters: $this->filters);
    }

    public function clearFilters()
    {
        $this->filters = [];
        $this->resetPage();
        $this->dispatch('filters-updated', filters: []);
    }

    public function getFilteredRentals()
    {
        $query = Rental::query();

        if ($this->categoryId) {
            $query->where('category_id', $this->categoryId);
        }

        foreach ($this->filters as $fieldId => $value) {
            $query->whereHas('fieldValues', function ($q) use ($fieldId, $value) {
                $q->where('field_id', $fieldId)
                    ->where('field_value', 'LIKE', '%' . $value . '%');
            });
        }

        return $query->paginate(12);
    }

    public function render()
    {
        return view('livewire.rental-field-filter', ['categoryId' => $this->categoryId]);
    }
}
