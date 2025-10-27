<?php

namespace App\Helpers;

use App\Models\RentalField;
use App\Models\RentalFieldTemplate;
use App\Models\RentalFieldValue;
use App\Models\Rental;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class DynamicRentalFields
{
    /**
     * Get all template fields for a specific category
     */
    public static function getTemplateFieldsForCategory(int $categoryId): Collection
    {
        return RentalField::whereHas('template', function ($query) use ($categoryId) {
            $query->active()
                ->whereHas('categories', function ($q) use ($categoryId) {
                    $q->where('category_id', $categoryId);
                });
        })->with('template')->ordered()->get();
    }

    /**
     * Get all active templates for a category
     */
    public static function getActiveTemplatesForCategory(int $categoryId): Collection
    {
        return RentalFieldTemplate::active()
            ->forCategory($categoryId)
            ->with([
                'fields' => function ($query) {
                    $query->ordered();
                }
            ])
            ->ordered()
            ->get();
    }

    /**
     * Save field values for a rental
     */
    public static function saveFieldValues(int $rentalId, array $fieldValues): void
    {
        // Get all fields for the rental's category to handle empty values
        $rental = Rental::find($rentalId);
        if (!$rental) {
            return;
        }

        $allFields = self::getTemplateFieldsForCategory($rental->category_id);
        $fieldIds = $allFields->pluck('id')->toArray();

        // Delete existing values for fields that are not in the current form
        RentalFieldValue::where('rental_id', $rentalId)
            ->whereIn('field_id', $fieldIds)
            ->delete();

        foreach ($fieldValues as $fieldName => $value) {
            $field = RentalField::where('field_name', $fieldName)->first();

            if ($field) {
                // Process the value based on field type
                $processedValue = self::processFieldValue($field, $value);

                // Only save if value is not empty (for non-checkbox fields)
                // For checkbox fields, save even if empty array (to clear selections)
                if ($field->field_type === 'checkbox' || !empty($processedValue)) {
                    RentalFieldValue::updateOrCreateForRental($rentalId, $field->id, $processedValue);
                }
            }
        }
    }

    /**
     * Process field value based on field type
     */
    private static function processFieldValue($field, $value)
    {
        if (is_array($value)) {
            switch ($field->field_type) {
                case 'checkbox':
                    // For checkboxes, join with commas as expected by the model
                    return implode(',', array_filter($value));
                default:
                    // For other types, convert to JSON
                    return json_encode($value);
            }
        }

        return $value;
    }

    /**
     * Get all field values for a rental as array
     */
    public static function getFieldValuesForRental(int $rentalId): array
    {
        return RentalFieldValue::getValuesForRental($rentalId);
    }

    /**
     * Get formatted field values for display
     */
    public static function getFormattedFieldValuesForRental(int $rentalId): Collection
    {
        return RentalFieldValue::with(['field', 'field.template'])
            ->where('rental_id', $rentalId)
            ->get()
            ->map(function ($value) {
                return [
                    'field_name' => $value->field->field_name,
                    'field_label' => $value->field->field_label,
                    'field_type' => $value->field->field_type,
                    'value' => $value->field_value,
                    'formatted_value' => $value->formatted_value,
                    'template_name' => $value->field->template->name,
                ];
            })
            ->sortBy('field_label');
    }

    /**
     * Apply filters to rental query based on dynamic fields
     */
    public static function applyFilters(Builder $query, array $filters): Builder
    {
        foreach ($filters as $fieldName => $filterValue) {
            if (empty($filterValue)) {
                continue;
            }

            $field = RentalField::where('field_name', $fieldName)->first();
            if (!$field || !$field->is_filterable) {
                continue;
            }

            $query->whereHas('fieldValues', function ($q) use ($field, $filterValue) {
                $q->where('field_id', $field->id);

                // Apply type-specific filtering
                switch ($field->field_type) {
                    case 'number':
                    case 'range':
                        if (is_array($filterValue)) {
                            if (isset($filterValue['min'])) {
                                $q->where('field_value', '>=', $filterValue['min']);
                            }
                            if (isset($filterValue['max'])) {
                                $q->where('field_value', '<=', $filterValue['max']);
                            }
                        } else {
                            $q->where('field_value', $filterValue);
                        }
                        break;

                    case 'select':
                    case 'radio':
                        if (is_array($filterValue)) {
                            $q->whereIn('field_value', $filterValue);
                        } else {
                            $q->where('field_value', $filterValue);
                        }
                        break;

                    case 'checkbox':
                        // For checkbox fields, check if any of the selected values match
                        if (is_array($filterValue)) {
                            $q->where(function ($subQuery) use ($filterValue) {
                                foreach ($filterValue as $value) {
                                    $subQuery->orWhere('field_value', 'LIKE', "%{$value}%");
                                }
                            });
                        } else {
                            $q->where('field_value', 'LIKE', "%{$filterValue}%");
                        }
                        break;

                    case 'date':
                        if (is_array($filterValue)) {
                            if (isset($filterValue['from'])) {
                                $q->where('field_value', '>=', $filterValue['from']);
                            }
                            if (isset($filterValue['to'])) {
                                $q->where('field_value', '<=', $filterValue['to']);
                            }
                        } else {
                            $q->where('field_value', $filterValue);
                        }
                        break;

                    default:
                        // Text search for other field types
                        $q->where('field_value', 'LIKE', "%{$filterValue}%");
                }
            });
        }

        return $query;
    }

    /**
     * Add relationship to Rental model for field values
     */
    public static function addRentalRelationships(): void
    {
        // This method is deprecated - use the relationship directly in the Rental model
        // The fieldValues relationship should be defined in the Rental model itself
    }

    /**
     * Get validation rules for a set of fields
     */
    public static function getValidationRulesForFields(Collection $fields): array
    {
        $rules = [];

        foreach ($fields as $field) {
            $fieldRules = $field->getLaravelValidationRules();
            $rules = array_merge($rules, $fieldRules);
        }

        return $rules;
    }

    /**
     * Get searchable field values for full-text search
     */
    public static function getSearchableValues(int $rentalId): string
    {
        return RentalFieldValue::with('field')
            ->where('rental_id', $rentalId)
            ->whereHas('field', function ($query) {
                $query->where('is_searchable', true);
            })
            ->pluck('field_value')
            ->filter()
            ->implode(' ');
    }

    /**
     * Generate form HTML for dynamic fields
     */
    public static function generateFormHTML(Collection $fields, array $values = []): string
    {
        $html = '';

        foreach ($fields as $field) {
            $value = $values[$field->field_name] ?? '';
            $attributes = $field->getInputAttributes();

            $html .= view('components.dynamic-fields.' . $field->field_type, [
                'field' => $field,
                'value' => $value,
                'attributes' => $attributes
            ])->render();
        }

        return $html;
    }

    /**
     * Get field usage statistics
     */
    public static function getFieldUsageStats(int $fieldId): array
    {
        $field = RentalField::find($fieldId);
        if (!$field) {
            return [];
        }

        return $field->getUsageStats();
    }

    /**
     * Get template usage statistics
     */
    public static function getTemplateUsageStats(int $templateId): array
    {
        $template = RentalFieldTemplate::find($templateId);
        if (!$template) {
            return [];
        }

        return $template->getUsageStats();
    }

    /**
     * Clone template fields to another template
     */
    public static function cloneFieldsToTemplate(int $sourceTemplateId, int $targetTemplateId): bool
    {
        $sourceFields = RentalField::where('template_id', $sourceTemplateId)->get();

        foreach ($sourceFields as $field) {
            $newField = $field->replicate();
            $newField->template_id = $targetTemplateId;
            $newField->save();
        }

        return true;
    }

    /**
     * Get available field types with labels
     */
    public static function getAvailableFieldTypes(): array
    {
        return RentalField::FIELD_TYPES;
    }

    /**
     * Validate field configuration
     */
    public static function validateFieldConfiguration(array $fieldData): array
    {
        $errors = [];

        // Required fields
        if (empty($fieldData['field_name'])) {
            $errors[] = 'Field name is required';
        }

        if (empty($fieldData['field_label'])) {
            $errors[] = 'Field label is required';
        }

        if (empty($fieldData['field_type'])) {
            $errors[] = 'Field type is required';
        }

        // Validate field type
        if (!array_key_exists($fieldData['field_type'] ?? '', static::getAvailableFieldTypes())) {
            $errors[] = 'Invalid field type';
        }

        // Type-specific validation
        if (in_array($fieldData['field_type'] ?? '', ['select', 'checkbox', 'radio'])) {
            if (empty($fieldData['options'])) {
                $errors[] = 'Options are required for ' . $fieldData['field_type'] . ' fields';
            }
        }

        return $errors;
    }

    /**
     * Export template data for backup/migration
     */
    public static function exportTemplateData(int $templateId): array
    {
        $template = RentalFieldTemplate::with(['fields', 'categories'])->find($templateId);
        if (!$template) {
            return [];
        }

        return $template->exportData();
    }

    /**
     * Import template data from export
     */
    public static function importTemplateData(array $templateData): ?RentalFieldTemplate
    {
        try {
            return RentalFieldTemplate::importData($templateData);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Auto-save pending field values when a rental is created
     */
    public static function autoSavePendingValues(int $rentalId, int $categoryId): void
    {
        $sessionKey = "pending_field_values_category_{$categoryId}";
        $pendingValues = session($sessionKey, []);

        if (!empty($pendingValues)) {
            // Save pending values to the new rental
            self::saveFieldValues($rentalId, $pendingValues);

            // Clear session
            session()->forget($sessionKey);
        }
    }

    /**
     * Get pending field values for a category
     */
    public static function getPendingValues(int $categoryId): array
    {
        $sessionKey = "pending_field_values_category_{$categoryId}";
        return session($sessionKey, []);
    }

    /**
     * Clear pending field values for a category
     */
    public static function clearPendingValues(int $categoryId): void
    {
        $sessionKey = "pending_field_values_category_{$categoryId}";
        session()->forget($sessionKey);
    }
}