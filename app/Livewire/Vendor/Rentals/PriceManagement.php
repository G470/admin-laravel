<?php

namespace App\Livewire\Vendor\Rentals;

use Livewire\Component;

class PriceManagement extends Component
{
    public $price_ranges_id = 2;
    public $price_range_hour = 0.00;
    public $price_range_day = 0.00;
    public $price_range_once = 0.00;
    public $service_fee = 0.00;
    public $currency = 'EUR';
    
    // Price type options
    public $priceTypes = [
        1 => 'Preis pro Stunde',
        2 => 'Preis pro Tag',
        3 => 'Preis pro Auftritt/Einmalig'
    ];
    
    public $currencies = [
        'EUR' => '€ Euro',
        'USD' => '$ US-Dollar',
        'CHF' => 'CHF Schweizer Franken'
    ];
    
    // Initial data from parent component
    public $initialData = [];

    public function mount($initialData = [])
    {
        $this->initialData = $initialData;
        
        // Set initial values
        $this->price_ranges_id = $initialData['price_ranges_id'] ?? 2;
        $this->price_range_hour = $initialData['price_range_hour'] ?? 0.00;
        $this->price_range_day = $initialData['price_range_day'] ?? 0.00;
        $this->price_range_once = $initialData['price_range_once'] ?? 0.00;
        $this->service_fee = $initialData['service_fee'] ?? 0.00;
        $this->currency = $initialData['currency'] ?? 'EUR';
    }

    public function updatedPriceRangesId()
    {
        // Reset other price fields when changing price type
        $this->price_range_hour = 0.00;
        $this->price_range_day = 0.00;
        $this->price_range_once = 0.00;
    }

    public function render()
    {
        return view('livewire.vendor.rentals.price-management');
    }
}
