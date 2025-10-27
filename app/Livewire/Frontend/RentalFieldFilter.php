<?php

namespace App\Livewire\Frontend;

use App\Models\RentalField;
use App\Models\Category;
use App\Helpers\DynamicRentalFields;
use Livewire\Component;
use Illuminate\Support\Collection;

class RentalFieldFilter extends Component
{
    // Filter state
    public $categoryId = null;
    public $filterableFields = [];
    public $filters = [];
    public $activeFilters = [];

    // UI state
    public $showFilters = true;
    public $isCollapsed = false;

    // Search integration
    public $searchQuery = '';

    protected $listeners = [
        'categoryChanged' => 'loadFieldsForCategory',
        'resetFilters' => 'clearAllFilters',
        'applySearch' => 'handleSearch'
    ];

    public function mount($categoryId = null, $searchQuery = '')
    {
        $this->categoryId = $categoryId;
        $this->searchQuery = $searchQuery;

        if ($this->categoryId) {
            $this->loadFieldsForCategory($this->categoryId);
        }

        // Load filters from session or request
        $this->loadSavedFilters();
    }

    public function loadFieldsForCategory($categoryId)
    {
        $this->categoryId = $categoryId;
        $this->filterableFields = [];
        $this->filters = [];

        if (!$categoryId) {
            return;
        }

        // Get filterable fields for the category
        $fields = DynamicRentalFields::getTemplateFieldsForCategory($categoryId);

        if ($fields->isNotEmpty()) {
            $this->filterableFields = $fields->filter(function ($field) {
                return $field->is_filterable;
            })->map(function ($field) {
                return [
                    'id' => $field->id,
                    'field_name' => $field->field_name,
                    'field_label' => $field->field_label,
                    'field_type' => $field->field_type,
                    'options' => $field->options ?? [],
                    'template_name' => $field->template->name,
                    'validation_rules' => $field->validation_rules ?? [],
                ];
            })->groupBy('template_name')->toArray();

            // Initialize filter values
            $this->initializeFilters();
        }

        $this->dispatch('filtersLoaded', ['fieldCount' => count($this->filterableFields)]);
    }

    protected function initializeFilters()
    {
        foreach ($this->filterableFields as $templateName => $fields) {
            foreach ($fields as $field) {
                if (!isset($this->filters[$field['field_name']])) {
                    $this->filters[$field['field_name']] = $this->getDefaultFilterValue($field);
                }
            }
        }
    }

    protected function getDefaultFilterValue($field): array
    {
        switch ($field['field_type']) {
            case 'number':
            case 'range':
                return ['min' => '', 'max' => ''];
            case 'date':
                return ['from' => '', 'to' => ''];
            case 'select':
            case 'radio':
                return ['value' => ''];
            case 'checkbox':
                return ['values' => []];
            default:
                return ['search' => ''];
        }
    }

    public function updatedFilters($value, $key)
    {
        // Update active filters
        $this->updateActiveFilters();

        // Emit filter change event
        $this->dispatch('filtersChanged', [
            'filters' => $this->getCleanFilters(),
            'activeFiltersCount' => count($this->activeFilters)
        ]);

        // Dispatch to parent component for rental list update
        $this->dispatch('rentalFiltersUpdated', $this->getCleanFilters());

        // Save to session
        $this->saveFiltersToSession();
    }

    protected function updateActiveFilters()
    {
        $this->activeFilters = [];

        foreach ($this->filters as $fieldName => $filterValue) {
            if ($this->isFilterActive($filterValue)) {
                $field = $this->findFieldByName($fieldName);
                if ($field) {
                    $this->activeFilters[] = [
                        'field_name' => $fieldName,
                        'field_label' => $field['field_label'],
                        'value' => $this->formatFilterValueForDisplay($field, $filterValue),
                        'type' => $field['field_type']
                    ];
                }
            }
        }
    }

    protected function isFilterActive($filterValue): bool
    {
        if (is_array($filterValue)) {
            foreach ($filterValue as $value) {
                if (!empty($value) && $value !== '' && $value !== []) {
                    return true;
                }
            }
            return false;
        }

        return !empty($filterValue) && $filterValue !== '';
    }

    protected function formatFilterValueForDisplay($field, $filterValue): string
    {
        switch ($field['field_type']) {
            case 'number':
            case 'range':
                $parts = [];
                if (!empty($filterValue['min'])) {
                    $parts[] = 'ab ' . $filterValue['min'];
                }
                if (!empty($filterValue['max'])) {
                    $parts[] = 'bis ' . $filterValue['max'];
                }
                return implode(', ', $parts);

            case 'date':
                $parts = [];
                if (!empty($filterValue['from'])) {
                    $parts[] = 'ab ' . date('d.m.Y', strtotime($filterValue['from']));
                }
                if (!empty($filterValue['to'])) {
                    $parts[] = 'bis ' . date('d.m.Y', strtotime($filterValue['to']));
                }
                return implode(', ', $parts);

            case 'select':
            case 'radio':
                $value = $filterValue['value'] ?? '';
                if (isset($field['options'][$value])) {
                    return $field['options'][$value];
                }
                return $value;

            case 'checkbox':
                $values = $filterValue['values'] ?? [];
                $labels = [];
                foreach ($values as $value) {
                    if (isset($field['options'][$value])) {
                        $labels[] = $field['options'][$value];
                    } else {
                        $labels[] = $value;
                    }
                }
                return implode(', ', $labels);

            default:
                return $filterValue['search'] ?? '';
        }
    }

    protected function findFieldByName($fieldName)
    {
        foreach ($this->filterableFields as $fields) {
            foreach ($fields as $field) {
                if ($field['field_name'] === $fieldName) {
                    return $field;
                }
            }
        }
        return null;
    }

    public function removeFilter($fieldName)
    {
        if (isset($this->filters[$fieldName])) {
            $field = $this->findFieldByName($fieldName);
            if ($field) {
                $this->filters[$fieldName] = $this->getDefaultFilterValue($field);
                $this->updatedFilters(null, $fieldName);
            }
        }
    }

    public function clearAllFilters()
    {
        $this->filters = [];
        $this->activeFilters = [];
        $this->initializeFilters();

        $this->dispatch('filtersCleared');
        $this->saveFiltersToSession();
    }

    public function toggleFilters()
    {
        $this->isCollapsed = !$this->isCollapsed;
    }

    protected function getCleanFilters(): array
    {
        $cleanFilters = [];

        foreach ($this->filters as $fieldName => $filterValue) {
            if ($this->isFilterActive($filterValue)) {
                $cleanFilters[$fieldName] = $filterValue;
            }
        }

        return $cleanFilters;
    }

    protected function saveFiltersToSession()
    {
        session(['rental_field_filters' => $this->getCleanFilters()]);
    }

    protected function loadSavedFilters()
    {
        $savedFilters = session('rental_field_filters', []);

        foreach ($savedFilters as $fieldName => $filterValue) {
            if (isset($this->filters[$fieldName])) {
                $this->filters[$fieldName] = $filterValue;
            }
        }

        $this->updateActiveFilters();
    }

    public function getFilterQuery(): array
    {
        return $this->getCleanFilters();
    }

    public function hasActiveFilters(): bool
    {
        return count($this->activeFilters) > 0;
    }

    public function getActiveFiltersCount(): int
    {
        return count($this->activeFilters);
    }

    public function handleSearch($query)
    {
        $this->searchQuery = $query;

        $this->dispatch('searchWithFilters', [
            'query' => $query,
            'filters' => $this->getCleanFilters()
        ]);
    }

    public function render()
    {
        return view('livewire.frontend.rental-field-filter', [
            'hasFields' => !empty($this->filterableFields),
            'activeFiltersCount' => $this->getActiveFiltersCount(),
            'templateGroups' => $this->filterableFields
        ]);
    }
}
