<?php

namespace App\Livewire;

use App\Models\RentalField;
use App\Models\RentalFieldValue;
use App\Helpers\DynamicRentalFields;
use Livewire\Component;

class DynamicRentalFieldsForm extends Component
{
    public $categoryId;
    public $rentalId;
    public $fields = [];
    public $fieldValues = [];
    public $showFields = false;

    protected $listeners = ['categorySelected' => 'loadFields'];

    public function mount($categoryId = null, $rentalId = null)
    {
        $this->categoryId = $categoryId;
        $this->rentalId = $rentalId;

        if ($this->categoryId) {
            $this->loadFields($this->categoryId);
        }

        if ($this->rentalId) {
            $this->loadExistingValues();
        }
    }

    public function loadFields($categoryId)
    {
        $this->categoryId = $categoryId;
        $this->fields = DynamicRentalFields::getTemplateFieldsForCategory($categoryId);
        $this->showFields = $this->fields->count() > 0;

        // Initialize field values with defaults
        foreach ($this->fields as $field) {
            if (!isset($this->fieldValues[$field->id])) {
                $this->fieldValues[$field->id] = $field->default_value ?? '';
            }
        }
    }

    public function loadExistingValues()
    {
        if ($this->rentalId) {
            $existingValues = DynamicRentalFields::getFieldValuesForRental($this->rentalId);
            foreach ($existingValues as $fieldId => $fieldValue) {
                $this->fieldValues[$fieldId] = $fieldValue->value;
            }
        }
    }

    public function updatedFieldValues($value, $key)
    {
        // Emit event for parent component
        $this->dispatch('dynamicFieldUpdated', [
            'fieldId' => $key,
            'value' => $value
        ]);
    }

    public function getValidationRules()
    {
        if (!$this->categoryId) {
            return [];
        }

        $rules = [];
        foreach ($this->fields as $field) {
            if ($field->is_required) {
                $rules["fieldValues.{$field->id}"] = 'required';
            }
        }

        return $rules;
    }

    public function getValidationMessages()
    {
        $messages = [];
        foreach ($this->fields as $field) {
            if ($field->is_required) {
                $messages["fieldValues.{$field->id}.required"] = "Das Feld '{$field->label}' ist erforderlich.";
            }
        }

        return $messages;
    }

    public function render()
    {
        return view('livewire.dynamic-rental-fields-form');
    }
}
