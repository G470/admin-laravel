<?php

namespace App\Livewire\Admin;

use App\Models\Country;
use Livewire\Component;
use Livewire\WithPagination;

class Countries extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';

    // Modal state
    public $showModal = false;
    public $showImportModal = false;
    public $showDataModal = false;
    public $editMode = false;
    public $selectedCountry = null;

    // Form data
    public $name = '';
    public $code = '';
    public $phone_code = '';
    public $is_active = true;

    // Import data
    public $importStats = [];
    public $countryData = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    protected $rules = [
        'name' => 'required|string|max:255',
        'code' => 'required|string|size:2',
        'phone_code' => 'nullable|string|max:5',
        'is_active' => 'boolean',
    ];

    protected $messages = [
        'name.required' => 'Ländername ist erforderlich.',
        'name.max' => 'Ländername darf maximal 255 Zeichen lang sein.',
        'code.required' => 'Ländercode ist erforderlich.',
        'code.size' => 'Ländercode muss genau 2 Zeichen lang sein.',
        'phone_code.max' => 'Telefonvorwahl darf maximal 5 Zeichen lang sein.',
    ];

    public function mount()
    {
        // Initialize component
    }

    public function updatingSearch()
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
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->editMode = false;
        $this->showModal = true;
    }

    public function openEditModal($countryId)
    {
        $country = Country::findOrFail($countryId);

        $this->selectedCountry = $country;
        $this->name = $country->name;
        $this->code = $country->code;
        $this->phone_code = $country->phone_code;
        $this->is_active = $country->is_active;

        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        // Dynamic validation rules based on edit mode
        $rules = $this->rules;
        if ($this->editMode && $this->selectedCountry) {
            $rules['code'] = 'required|string|size:2|unique:countries,code,' . $this->selectedCountry->id;
        } else {
            $rules['code'] = 'required|string|size:2|unique:countries,code';
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'code' => strtoupper($this->code),
            'phone_code' => $this->phone_code,
            'is_active' => $this->is_active,
        ];

        if ($this->editMode && $this->selectedCountry) {
            $this->selectedCountry->update($data);
            $message = 'Land wurde erfolgreich aktualisiert.';
        } else {
            Country::create($data);
            $message = 'Land wurde erfolgreich erstellt.';
        }

        $this->closeModal();
        $this->dispatch('success', message: $message);
    }

    public function delete($countryId)
    {
        $country = Country::findOrFail($countryId);

        // Check if country is in use
        $locationsCount = $country->locations()->count();

        if ($locationsCount > 0) {
            $this->dispatch('error', message: "Land kann nicht gelöscht werden. Es wird von {$locationsCount} Standort(en) verwendet.");
            return;
        }

        $country->delete();
        $this->dispatch('success', message: 'Land wurde erfolgreich gelöscht.');
    }

    public function toggleStatus($countryId)
    {
        $country = Country::findOrFail($countryId);
        $country->update(['is_active' => !$country->is_active]);

        $status = $country->is_active ? 'aktiviert' : 'deaktiviert';
        $this->dispatch('success', message: "Land wurde {$status}.");
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function closeImportModal()
    {
        $this->showImportModal = false;
        $this->selectedCountry = null;
        $this->importStats = [];
    }

    public function closeDataModal()
    {
        $this->showDataModal = false;
        $this->selectedCountry = null;
        $this->countryData = [];
    }

    private function resetForm()
    {
        $this->name = '';
        $this->code = '';
        $this->phone_code = '';
        $this->is_active = true;
        $this->selectedCountry = null;
        $this->resetErrorBag();
    }

    public function openImportModal($countryId)
    {
        $country = Country::findOrFail($countryId);
        $this->selectedCountry = $country;
        
        // Load import statistics
        $importService = new \App\Services\CountryDataImportService();
        $this->importStats = $importService->getImportStats($country);
        
        $this->showImportModal = true;
    }

    public function viewCountryData($countryId)
    {
        $country = Country::findOrFail($countryId);
        $this->selectedCountry = $country;
        
        // Load sample data
        $importService = new \App\Services\CountryDataImportService();
        $stats = $importService->getImportStats($country);
        
        if ($stats['table_exists']) {
            // Get top 10 records as preview
            $data = \App\Models\CountryPostalCode::getForCountry($country->code)
                ->orderBy('population', 'desc')
                ->limit(10)
                ->get();
            
            $this->countryData = $data->toArray();
        }
        
        $this->importStats = $stats;
        $this->showDataModal = true;
    }

    public function exportCountryData($countryId)
    {
        $country = Country::findOrFail($countryId);
        
        // Redirect to export route
        return redirect()->route('admin.countries.data.export', $country);
    }

    public function clearCountryData($countryId)
    {
        $country = Country::findOrFail($countryId);
        
        $importService = new \App\Services\CountryDataImportService();
        $result = $importService->clearCountryData($country);
        
        if ($result) {
            $this->dispatch('success', message: "Alle Daten für {$country->name} wurden gelöscht.");
        } else {
            $this->dispatch('error', message: "Keine Daten zum Löschen vorhanden oder Tabelle existiert nicht.");
        }
    }

    public function render()
    {
        $countries = Country::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter !== '', function ($query) {
                $query->where('is_active', $this->statusFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        return view('livewire.admin.countries', [
            'countries' => $countries,
        ]);
    }
}