<?php

namespace App\Livewire\Vendor;

use App\Models\RentalField;
use App\Models\RentalFieldTemplate;
use App\Models\RentalFieldValue;
use App\Models\Rental;
use App\Models\Category;
use App\Helpers\DynamicRentalFields;
use Livewire\Component;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class DynamicRentalForm extends Component
{
    // Component properties
    public $rentalId = null;
    public $vendorId = null;
    public $categoryId = null;
    public $fields = [];
    public $fieldValues = [];
    public $templates = [];

    // State management
    public $isEditing = false;
    public $showPreview = false;
    public $validationErrors = [];

    // Dynamic validation rules
    public $rules = [];
    public $messages = [];

    protected $listeners = [
        'categoryChanged' => 'loadFieldsForCategory',
        'saveFieldValues' => 'saveValues',
        'refreshFields' => 'refreshFieldsForCategory'
    ];

    public function mount($rentalId = null, $vendorId = null, $categoryId = null, $initialData = [])
    {
        $this->rentalId = $rentalId;
        $this->vendorId = $vendorId;

        // Get categoryId from various sources
        $this->categoryId = $categoryId ??
            $rentalId?->category_id ??
            $initialData['category_id'] ??
            null;

        $this->isEditing = $rentalId !== null;

        if ($this->categoryId) {
            $this->loadFieldsForCategory($this->categoryId);
        }
        if ($this->rentalId) {

            $this->loadExistingValues();
        } else {
            // Load pending values from session if no rental exists
            $this->loadPendingValues();
        }
    }

    /**
     * Listen for category selection events from the categories component
     */
    #[On('categorySelected')]
    public function onCategorySelected($categoryData)
    {
        if (is_array($categoryData) && isset($categoryData['id'])) {
            $this->updateCategory($categoryData['id']);
        }
    }

    /**
     * Listen for category removal events
     */
    #[On('categoryRemoved')]
    public function onCategoryRemoved()
    {
        $this->clearCategory();
    }

    /**
     * Update the category and reload fields
     */
    public function updateCategory($categoryId)
    {
        $this->categoryId = $categoryId;

        // Clear existing field values when category changes
        $this->fieldValues = [];
        $this->validationErrors = [];

        // Load fields for the new category
        $this->loadFieldsForCategory($categoryId);

        // Load pending values for the new category
        $this->loadPendingValues();

        // Setup validation rules for the new fields
        $this->setupValidationRules();

        $this->dispatch('categoryUpdated', [
            'category_id' => $categoryId,
            'field_count' => count($this->fields)
        ]);
    }

    /**
     * Clear the category and reset the form
     */
    public function clearCategory()
    {
        $this->categoryId = null;
        $this->fields = [];
        $this->fieldValues = [];
        $this->validationErrors = [];
        $this->templateGroups = [];

        $this->dispatch('categoryCleared');
    }

    public function loadFieldsForCategory($categoryId)
    {
        $this->categoryId = $categoryId;
        $this->fields = [];
        $this->templates = [];
        $this->rules = [];
        $this->messages = [];

        if (!$categoryId) {
            return;
        }

        // Load templates for category
        $this->templates = DynamicRentalFields::getActiveTemplatesForCategory($categoryId);

        // Load all fields for category
        $allFields = DynamicRentalFields::getTemplateFieldsForCategory($categoryId);

        if ($allFields->isNotEmpty()) {
            $this->fields = $allFields->map(function ($field) {
                return [
                    'id' => $field->id,
                    'template_id' => $field->template_id,
                    'template_name' => $field->template->name,
                    'field_type' => $field->field_type,
                    'field_name' => $field->field_name,
                    'field_label' => $field->field_label,
                    'field_description' => $field->field_description,
                    'options' => $field->options ?? [],
                    'validation_rules' => $field->validation_rules ?? [],
                    'dependencies' => $field->dependencies ?? [],
                    'is_required' => $field->is_required,
                    'is_filterable' => $field->is_filterable,
                    'is_searchable' => $field->is_searchable,
                    'sort_order' => $field->sort_order,
                    'input_attributes' => $field->getInputAttributes(),
                ];
            })->toArray();

            // Set up validation rules
            $this->setupValidationRules();

            // Initialize field values if not set
            $this->initializeFieldValues();
        }

        $this->dispatch('fieldsLoaded', ['fieldCount' => count($this->fields)]);
    }

    protected function setupValidationRules()
    {
        foreach ($this->fields as $field) {
            $fieldName = 'fieldValues.' . $field['field_name'];
            $rules = [];
            $messages = [];

            if ($field['is_required']) {
                $rules[] = 'required';
                $messages[$fieldName . '.required'] = "Das Feld '{$field['field_label']}' ist erforderlich.";
            } else {
                $rules[] = 'nullable';
            }

            // Type-specific validation
            switch ($field['field_type']) {
                case 'email':
                    $rules[] = 'email';
                    $messages[$fieldName . '.email'] = "Das Feld '{$field['field_label']}' muss eine gültige E-Mail-Adresse sein.";
                    break;
                case 'url':
                    $rules[] = 'url';
                    $messages[$fieldName . '.url'] = "Das Feld '{$field['field_label']}' muss eine gültige URL sein.";
                    break;
                case 'number':
                case 'range':
                    $rules[] = 'numeric';
                    $messages[$fieldName . '.numeric'] = "Das Feld '{$field['field_label']}' muss eine Zahl sein.";
                    break;
                case 'date':
                    $rules[] = 'date';
                    $messages[$fieldName . '.date'] = "Das Feld '{$field['field_label']}' muss ein gültiges Datum sein.";
                    break;
                case 'select':
                case 'radio':
                    if (!empty($field['options'])) {
                        $rules[] = 'in:' . implode(',', array_keys($field['options']));
                        $messages[$fieldName . '.in'] = "Das Feld '{$field['field_label']}' hat einen ungültigen Wert.";
                    }
                    break;
            }

            // Custom validation rules
            if (!empty($field['validation_rules'])) {
                $customRules = $field['validation_rules'];

                if (isset($customRules['min_length'])) {
                    $rules[] = 'min:' . $customRules['min_length'];
                    $messages[$fieldName . '.min'] = "Das Feld '{$field['field_label']}' muss mindestens {$customRules['min_length']} Zeichen haben.";
                }

                if (isset($customRules['max_length'])) {
                    $rules[] = 'max:' . $customRules['max_length'];
                    $messages[$fieldName . '.max'] = "Das Feld '{$field['field_label']}' darf höchstens {$customRules['max_length']} Zeichen haben.";
                }

                if (isset($customRules['pattern'])) {
                    $rules[] = 'regex:' . $customRules['pattern'];
                    $messages[$fieldName . '.regex'] = "Das Feld '{$field['field_label']}' hat ein ungültiges Format.";
                }
            }

            $this->rules[$fieldName] = $rules;
            $this->messages = array_merge($this->messages, $messages);
        }
    }

    protected function initializeFieldValues()
    {
        foreach ($this->fields as $field) {
            if (!isset($this->fieldValues[$field['field_name']])) {
                $this->fieldValues[$field['field_name']] = $this->getDefaultValue($field);
            }
        }
    }

    protected function getDefaultValue($field)
    {
        switch ($field['field_type']) {
            case 'checkbox':
                return [];
            case 'number':
            case 'range':
                return null;
            case 'select':
            case 'radio':
                return '';
            default:
                return '';
        }
    }

    public function loadExistingValues()
    {
        if (!$this->rentalId) {
            return;
        }

        $existingValues = DynamicRentalFields::getFieldValuesForRental($this->rentalId);
        foreach ($existingValues as $fieldName => $value) {
            $this->fieldValues[$fieldName] = $value;
        }
    }

    public function loadPendingValues()
    {
        if (!$this->categoryId) {
            return;
        }

        $sessionKey = "pending_field_values_category_{$this->categoryId}";
        $pendingValues = session($sessionKey, []);

        foreach ($pendingValues as $fieldName => $value) {
            $this->fieldValues[$fieldName] = $value;
        }
    }

    public function saveValues()
    {
        try {
            $this->validate($this->rules, $this->messages);

            if ($this->rentalId) {
                // Save values to database
                DynamicRentalFields::saveFieldValues($this->rentalId, $this->fieldValues);

                $this->dispatch('fieldValuesSaved', [
                    'message' => 'Dynamische Felder erfolgreich gespeichert!',
                    'rental_id' => $this->rentalId
                ]);

                session()->flash('success', 'Dynamische Felder erfolgreich gespeichert!');
            } else {
                // Store in session for new rental with category context
                $sessionKey = "pending_field_values_category_{$this->categoryId}";
                session([$sessionKey => $this->fieldValues]);

                $this->dispatch('fieldValuesStored', [
                    'message' => 'Felddaten zwischengespeichert',
                    'values' => $this->fieldValues,
                    'category_id' => $this->categoryId
                ]);
            }

        } catch (ValidationException $e) {
            $this->validationErrors = $e->errors();
            $this->dispatch('validationFailed', ['errors' => $this->validationErrors]);
        }
    }

    /**
     * Auto-save field values when rental is created
     */
    public function autoSaveForRental($rentalId)
    {
        if (!$this->rentalId && $this->categoryId) {
            $sessionKey = "pending_field_values_category_{$this->categoryId}";
            $pendingValues = session($sessionKey, []);

            if (!empty($pendingValues)) {
                // Save pending values to the new rental
                DynamicRentalFields::saveFieldValues($rentalId, $pendingValues);

                // Clear session
                session()->forget($sessionKey);

                $this->dispatch('fieldValuesAutoSaved', [
                    'message' => 'Felddaten automatisch gespeichert',
                    'rental_id' => $rentalId
                ]);
            }
        }
    }

    public function clearFieldValues()
    {
        $this->fieldValues = [];
        $this->initializeFieldValues();
        $this->validationErrors = [];

        // Clear session values if no rental exists
        if (!$this->rentalId && $this->categoryId) {
            $sessionKey = "pending_field_values_category_{$this->categoryId}";
            session()->forget($sessionKey);
        }

        $this->dispatch('fieldValuesCleared');
    }

    public function togglePreview()
    {
        $this->showPreview = !$this->showPreview;
    }

    public function updatedFieldValues($value, $key)
    {
        // Handle dependent fields
        $this->handleFieldDependencies($key, $value);

        // Clear validation error for this field
        if (isset($this->validationErrors["fieldValues.{$key}"])) {
            unset($this->validationErrors["fieldValues.{$key}"]);
        }
    }

    protected function handleFieldDependencies($changedFieldName, $value)
    {
        foreach ($this->fields as $field) {
            if (!empty($field['dependencies'])) {
                $dependencies = $field['dependencies'];

                if (isset($dependencies['show_when'])) {
                    $condition = $dependencies['show_when'];

                    if ($condition['field'] === $changedFieldName) {
                        $shouldShow = $this->evaluateCondition($condition, $value);

                        if (!$shouldShow) {
                            // Clear value if field should be hidden
                            $this->fieldValues[$field['field_name']] = $this->getDefaultValue($field);
                        }
                    }
                }
            }
        }
    }

    protected function evaluateCondition($condition, $value): bool
    {
        $operator = $condition['operator'] ?? 'equals';
        $expectedValue = $condition['value'] ?? '';

        switch ($operator) {
            case 'equals':
                return $value == $expectedValue;
            case 'not_equals':
                return $value != $expectedValue;
            case 'in':
                return in_array($value, (array) $expectedValue);
            case 'not_in':
                return !in_array($value, (array) $expectedValue);
            case 'greater_than':
                return $value > $expectedValue;
            case 'less_than':
                return $value < $expectedValue;
            default:
                return true;
        }
    }

    public function getFieldValue($fieldName)
    {
        return $this->fieldValues[$fieldName] ?? '';
    }

    public function shouldShowField($field): bool
    {
        if (empty($field['dependencies'])) {
            return true;
        }

        $dependencies = $field['dependencies'];

        if (isset($dependencies['show_when'])) {
            $condition = $dependencies['show_when'];
            $dependentFieldValue = $this->fieldValues[$condition['field']] ?? '';

            return $this->evaluateCondition($condition, $dependentFieldValue);
        }

        return true;
    }

    public function getTemplateGroups(): array
    {
        $groups = [];

        foreach ($this->fields as $field) {
            $templateName = $field['template_name'];

            if (!isset($groups[$templateName])) {
                $groups[$templateName] = [];
            }

            $groups[$templateName][] = $field;
        }

        return $groups;
    }

    public function render()
    {
        return view('livewire.vendor.dynamic-rental-form', [
            'templateGroups' => $this->getTemplateGroups(),
            'hasFields' => !empty($this->fields),
            'fieldCount' => count($this->fields),
            'rentalId' => $this->rentalId,
            'vendorId' => $this->vendorId,
            'categoryId' => $this->categoryId,
            'isEditing' => $this->isEditing,
            'showPreview' => $this->showPreview,
            'validationErrors' => $this->validationErrors,
            'rules' => $this->rules,
            'messages' => $this->messages,
            'fields' => $this->fields,
            'fieldValues' => $this->fieldValues,
        ]);
    }
}
