<?php

namespace App\Livewire;

use App\Models\Country;
use Livewire\Component;

class SearchForm extends Component
{
    public $query = '';
    public $location = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $dateRange = '';
    public $countryCode = '';

    public function mount()
    {
        $this->query = request()->query('query', '');
        $this->location = request()->query('location', '');
        $this->dateFrom = request()->query('dateFrom', '');
        $this->dateTo = request()->query('dateTo', '');
        $this->countryCode = request()->query('countryCode', 'DE');
        $this->dateRange = $this->dateFrom && $this->dateTo
            ? date('d.m.Y', strtotime($this->dateFrom)) . ' - ' . date('d.m.Y', strtotime($this->dateTo))
            : '';
    }

    public function getActiveCountriesProperty()
    {
        return Country::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.search-form');
    }
}
