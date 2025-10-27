<?php

namespace App\Livewire;

use App\Models\Rental;
use App\Models\RentalFieldValue;
use App\Helpers\DynamicRentalFields;
use Livewire\Component;

class RentalFieldDisplay extends Component
{
    public $rental;
    public $fieldValues = [];
    public $showFields = false;

    public function mount($rental)
    {
        $this->rental = $rental;
        $this->loadFieldValues();
    }

    public function loadFieldValues()
    {
        if ($this->rental) {
            $this->fieldValues = DynamicRentalFields::getFieldValuesForRental($this->rental->id);
            $this->showFields = count($this->fieldValues) > 0;
        }
    }

    public function getFieldDisplayValue($fieldValue)
    {
        $field = $fieldValue->field;
        $value = $fieldValue->value;

        switch ($field->type) {
            case 'checkbox':
                return $value ? 'Ja' : 'Nein';

            case 'select':
            case 'radio':
                return $value;

            case 'number':
                return is_numeric($value) ? number_format($value, 0, ',', '.') : $value;

            default:
                return $value;
        }
    }

    public function render()
    {
        return view('livewire.rental-field-display');
    }
}
