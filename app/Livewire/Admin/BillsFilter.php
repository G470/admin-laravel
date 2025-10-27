<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class BillsFilter extends Component
{
    public $search = '';
    public $status = '';
    public $dateFrom = '';
    public $dateTo = '';

    protected $listeners = ['resetFilters' => 'resetFilters'];

    public function updatedSearch()
    {
        $this->emit('searchUpdated', $this->search);
    }

    public function updatedStatus()
    {
        $this->emit('statusUpdated', $this->status);
    }

    public function updatedDateFrom()
    {
        $this->emit('dateFromUpdated', $this->dateFrom);
    }

    public function updatedDateTo()
    {
        $this->emit('dateToUpdated', $this->dateTo);
    }

    public function resetFilters()
    {
        $this->reset(['search', 'status', 'dateFrom', 'dateTo']);
        $this->emit('filtersReset');
    }

    public function render()
    {
        return view('livewire.admin.bills-filter');
    }
}