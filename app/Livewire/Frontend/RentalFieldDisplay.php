<?php

namespace App\Livewire\Frontend;

use Livewire\Component;
use App\Models\Rental;
use App\Helpers\DynamicRentalFields;

class RentalFieldDisplay extends Component
{
    public $rental;
    public $fieldValues = [];
    public $templateGroups = [];
    public $showFields = false;

    public function mount(Rental $rental)
    {
        $this->rental = $rental;
        $this->loadFieldValues();
    }

    public function loadFieldValues()
    {

        if (!$this->rental->category_id) {
            return;
        }

        // Get field values for this rental
        $this->fieldValues = DynamicRentalFields::getFieldValuesForRental($this->rental->id);

        // Get templates and fields for display
        $templates = DynamicRentalFields::getActiveTemplatesForCategory($this->rental->category_id);

        $this->templateGroups = [];
        foreach ($templates as $template) {
            $templateFields = [];

            foreach ($template->fields as $field) {
                $fieldValue = $this->fieldValues[$field->field_name] ?? null;

                // Only show fields that have values
                if (!empty($fieldValue)) {
                    $templateFields[] = [
                        'field' => $field,
                        'value' => $fieldValue,
                        'formatted_value' => $this->formatFieldValue($field, $fieldValue)
                    ];
                }
            }

            // Only add template if it has fields with values
            if (!empty($templateFields)) {
                $this->templateGroups[] = [
                    'template' => $template,
                    'fields' => $templateFields
                ];
            }
        }

        $this->showFields = !empty($this->templateGroups);
    }

    private function formatFieldValue($field, $value)
    {
        switch ($field->field_type) {
            case 'checkbox':
                // Handle array values for checkboxes
                if (is_array($value)) {
                    $labels = [];
                    $options = $field->options ?? [];

                    foreach ($value as $val) {
                        $labels[] = $options[$val] ?? $val;
                    }

                    return implode(', ', $labels);
                }

                if (is_string($value) && strpos($value, ',') !== false) {
                    $values = explode(',', $value);
                    $labels = [];
                    $options = $field->options ?? [];

                    foreach ($values as $val) {
                        $val = trim($val);
                        $labels[] = $options[$val] ?? $val;
                    }

                    return implode(', ', $labels);
                }

                // Ensure value is scalar before using as array key
                if (is_scalar($value)) {
                    return $field->options[$value] ?? $value;
                }

                return $value;

            case 'select':
            case 'radio':
                // Handle array values (should not happen for select/radio, but safety check)
                if (is_array($value)) {
                    return implode(', ', $value);
                }

                // Ensure value is scalar before using as array key
                if (is_scalar($value)) {
                    return $field->options[$value] ?? $value;
                }

                return $value;

            case 'number':
            case 'range':
                $unit = $field->settings['unit'] ?? '';
                return $value . ($unit ? ' ' . $unit : '');

            case 'date':
                try {
                    return \Carbon\Carbon::parse($value)->format('d.m.Y');
                } catch (\Exception $e) {
                    return $value;
                }

            default:
                return $value;
        }
    }

    public function render()
    {
        return view('livewire.frontend.rental-field-display');
    }
}