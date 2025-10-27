<?php

namespace App\Livewire\Admin;

use App\Models\RentalFieldTemplate;
use App\Models\RentalField;
use App\Models\Category;
use App\Helpers\DynamicRentalFields;
use Livewire\Component;
use Livewire\WithPagination;

class RentalFieldTemplateManager extends Component
{
    use WithPagination;

    // Template properties
    public $template;
    public $templateName = '';
    public $templateDescription = '';
    public $templateIsActive = true;
    public $templateSortOrder = 0;
    public $templateCategories = [];
    public $templateSettings = [];

    // Field management
    public $fields = [];
    public $showFieldModal = false;
    public $editingFieldIndex = null;
    public $currentField = [];

    // Modal and UI state
    public $showDeleteModal = false;
    public $showImportModal = false;
    public $importData = '';
    public $fieldToDelete = null;

    // Available options
    public $availableCategories = [];
    public $fieldTypes = [];

    protected $rules = [
        'templateName' => 'required|string|max:255',
        'templateDescription' => 'nullable|string|max:1000',
        'templateIsActive' => 'boolean',
        'templateSortOrder' => 'integer|min:0',
        'templateCategories' => 'array',
        'templateCategories.*' => 'exists:categories,id',
    ];

    protected $fieldRules = [
        'currentField.field_name' => 'required|string|max:255',
        'currentField.field_label' => 'required|string|max:255',
        'currentField.field_type' => 'required|string',
        'currentField.field_description' => 'nullable|string|max:500',
        'currentField.is_required' => 'boolean',
        'currentField.is_filterable' => 'boolean',
        'currentField.is_searchable' => 'boolean',
        'currentField.sort_order' => 'integer|min:0',
        'currentField.options' => 'nullable|array',
        'currentField.validation_rules' => 'nullable|array',
    ];

    public function mount($template = null)
    {
        $this->fieldTypes = DynamicRentalFields::getAvailableFieldTypes();
        $this->availableCategories = Category::orderBy('name')->get();

        if ($template) {
            $this->template = $template;
            $this->loadTemplate();
        } else {
            $this->initializeNewTemplate();
        }
    }

    public function loadTemplate()
    {
        $this->templateName = $this->template->name;
        $this->templateDescription = $this->template->description;
        $this->templateIsActive = $this->template->is_active;
        $this->templateSortOrder = $this->template->sort_order;
        $this->templateCategories = $this->template->categories->pluck('id')->toArray();
        $this->templateSettings = $this->template->settings ?? [];

        $this->fields = $this->template->fields->map(function ($field) {
            return [
                'id' => $field->id,
                'field_name' => $field->field_name,
                'field_label' => $field->field_label,
                'field_type' => $field->field_type,
                'field_description' => $field->field_description,
                'options' => $field->options,
                'validation_rules' => $field->validation_rules,
                'dependencies' => $field->dependencies,
                'seo_settings' => $field->seo_settings,
                'is_required' => $field->is_required,
                'is_filterable' => $field->is_filterable,
                'is_searchable' => $field->is_searchable,
                'sort_order' => $field->sort_order,
            ];
        })->toArray();
    }

    public function initializeNewTemplate()
    {
        $this->template = null;
        $this->templateName = '';
        $this->templateDescription = '';
        $this->templateIsActive = true;
        $this->templateSortOrder = 0;
        $this->templateCategories = [];
        $this->templateSettings = [];
        $this->fields = [];
    }

    public function addField()
    {
        $this->resetFieldModal();
        $this->editingFieldIndex = null;
        $this->showFieldModal = true;
    }

    public function editField($index)
    {
        if (isset($this->fields[$index])) {
            $this->currentField = $this->fields[$index];
            $this->editingFieldIndex = $index;
            $this->showFieldModal = true;
        }
    }

    public function resetFieldModal()
    {
        $this->currentField = [
            'field_name' => '',
            'field_label' => '',
            'field_type' => 'text',
            'field_description' => '',
            'options' => [],
            'validation_rules' => [],
            'dependencies' => [],
            'seo_settings' => [],
            'is_required' => false,
            'is_filterable' => true,
            'is_searchable' => true,
            'sort_order' => count($this->fields),
        ];
    }

    public function saveField()
    {
        $this->validate($this->fieldRules);

        // Validate field configuration
        $errors = DynamicRentalFields::validateFieldConfiguration($this->currentField);
        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->addError('currentField', $error);
            }
            return;
        }

        if ($this->editingFieldIndex !== null) {
            // Update existing field
            $this->fields[$this->editingFieldIndex] = $this->currentField;
        } else {
            // Add new field
            $this->fields[] = $this->currentField;
        }

        $this->closeFieldModal();
        $this->dispatch('field-saved');
    }

    public function closeFieldModal()
    {
        $this->showFieldModal = false;
        $this->editingFieldIndex = null;
        $this->resetFieldModal();
        $this->resetValidation();
    }

    public function confirmDeleteField($index)
    {
        $this->fieldToDelete = $index;
        $this->showDeleteModal = true;
    }

    public function deleteField()
    {
        if ($this->fieldToDelete !== null && isset($this->fields[$this->fieldToDelete])) {
            unset($this->fields[$this->fieldToDelete]);
            $this->fields = array_values($this->fields);

            // Update sort orders
            foreach ($this->fields as $index => $field) {
                $this->fields[$index]['sort_order'] = $index;
            }
        }

        $this->showDeleteModal = false;
        $this->fieldToDelete = null;
    }

    public function moveFieldUp($index)
    {
        if ($index > 0 && isset($this->fields[$index]) && isset($this->fields[$index - 1])) {
            $temp = $this->fields[$index];
            $this->fields[$index] = $this->fields[$index - 1];
            $this->fields[$index - 1] = $temp;

            // Update sort orders
            $this->fields[$index]['sort_order'] = $index;
            $this->fields[$index - 1]['sort_order'] = $index - 1;
        }
    }

    public function moveFieldDown($index)
    {
        if ($index < count($this->fields) - 1 && isset($this->fields[$index]) && isset($this->fields[$index + 1])) {
            $temp = $this->fields[$index];
            $this->fields[$index] = $this->fields[$index + 1];
            $this->fields[$index + 1] = $temp;

            // Update sort orders
            $this->fields[$index]['sort_order'] = $index;
            $this->fields[$index + 1]['sort_order'] = $index + 1;
        }
    }

    public function addFieldOption()
    {
        if (!isset($this->currentField['options'])) {
            $this->currentField['options'] = [];
        }
        $this->currentField['options'][] = '';
    }

    public function removeFieldOption($index)
    {
        if (isset($this->currentField['options'][$index])) {
            unset($this->currentField['options'][$index]);
            $this->currentField['options'] = array_values($this->currentField['options']);
        }
    }

    public function saveTemplate()
    {
        $this->validate();

        try {
            if ($this->template) {
                // Update existing template
                $this->template->update([
                    'name' => $this->templateName,
                    'description' => $this->templateDescription,
                    'is_active' => $this->templateIsActive,
                    'sort_order' => $this->templateSortOrder,
                    'settings' => $this->templateSettings,
                ]);
            } else {
                // Create new template
                $this->template = RentalFieldTemplate::create([
                    'name' => $this->templateName,
                    'description' => $this->templateDescription,
                    'is_active' => $this->templateIsActive,
                    'sort_order' => $this->templateSortOrder,
                    'settings' => $this->templateSettings,
                ]);
            }

            // Sync categories
            $this->template->categories()->sync($this->templateCategories);

            // Save fields
            $this->template->fields()->delete();
            foreach ($this->fields as $fieldData) {
                $this->template->fields()->create($fieldData);
            }

            $this->dispatch('template-saved', ['templateId' => $this->template->id]);
            session()->flash('success', 'Template erfolgreich gespeichert!');

            return redirect()->route('admin.rental-field-templates.show', $this->template);

        } catch (\Exception $e) {
            $this->addError('template', 'Fehler beim Speichern: ' . $e->getMessage());
        }
    }

    public function duplicateTemplate()
    {
        if ($this->template) {
            $newTemplate = $this->template->duplicate();
            return redirect()->route('admin.rental-field-templates.edit', $newTemplate);
        }
    }

    public function exportTemplate()
    {
        if ($this->template) {
            $data = DynamicRentalFields::exportTemplateData($this->template->id);
            $this->dispatch('download-json', [
                'data' => $data,
                'filename' => 'template-' . $this->template->id . '.json'
            ]);
        }
    }

    public function showImport()
    {
        $this->showImportModal = true;
        $this->importData = '';
    }

    public function importTemplate()
    {
        $this->validate(['importData' => 'required|json']);

        try {
            $templateData = json_decode($this->importData, true);
            $template = DynamicRentalFields::importTemplateData($templateData);

            if ($template) {
                $this->showImportModal = false;
                session()->flash('success', 'Template erfolgreich importiert!');
                return redirect()->route('admin.rental-field-templates.show', $template);
            } else {
                $this->addError('importData', 'Import fehlgeschlagen. Bitte überprüfen Sie die Daten.');
            }
        } catch (\Exception $e) {
            $this->addError('importData', 'Import fehlgeschlagen: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.rental-field-template-manager');
    }
}
