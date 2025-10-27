<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RentalField extends Model
{
    use HasFactory;

    // Supported field types
    const FIELD_TYPES = [
        'text' => 'Text Input',
        'textarea' => 'Text Area',
        'number' => 'Number Input',
        'select' => 'Dropdown Select',
        'checkbox' => 'Multiple Choice',
        'radio' => 'Single Choice',
        'date' => 'Date Picker',
        'range' => 'Range Slider',
        'email' => 'Email Input',
        'url' => 'URL Input'
    ];

    protected $fillable = [
        'template_id',
        'field_type',
        'field_name',
        'field_label',
        'field_description',
        'options',
        'validation_rules',
        'dependencies',
        'seo_settings',
        'is_required',
        'is_filterable',
        'is_searchable',
        'sort_order'
    ];

    protected $casts = [
        'options' => 'array',
        'validation_rules' => 'array',
        'dependencies' => 'array',
        'seo_settings' => 'array',
        'is_required' => 'boolean',
        'is_filterable' => 'boolean',
        'is_searchable' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Relationship: Field belongs to template
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(RentalFieldTemplate::class, 'template_id');
    }

    /**
     * Relationship: Field has many values
     */
    public function values(): HasMany
    {
        return $this->hasMany(RentalFieldValue::class, 'field_id');
    }

    /**
     * Scope: Ordered by sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('field_label');
    }

    /**
     * Scope: Required fields only
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    /**
     * Scope: Filterable fields only
     */
    public function scopeFilterable($query)
    {
        return $query->where('is_filterable', true);
    }

    /**
     * Scope: Searchable fields only
     */
    public function scopeSearchable($query)
    {
        return $query->where('is_searchable', true);
    }

    /**
     * Get field type label
     */
    public function getFieldTypeLabelAttribute(): string
    {
        return self::FIELD_TYPES[$this->field_type] ?? ucfirst($this->field_type);
    }

    /**
     * Get validation rules as Laravel validation array
     */
    public function getLaravelValidationRules(): array
    {
        $rules = [];

        if ($this->is_required) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        // Type-specific validation
        switch ($this->field_type) {
            case 'email':
                $rules[] = 'email';
                break;
            case 'url':
                $rules[] = 'url';
                break;
            case 'number':
                $rules[] = 'numeric';
                break;
            case 'date':
                $rules[] = 'date';
                break;
        }

        // Custom validation rules
        if ($this->validation_rules) {
            if (isset($this->validation_rules['min_length'])) {
                $rules[] = 'min:' . $this->validation_rules['min_length'];
            }
            if (isset($this->validation_rules['max_length'])) {
                $rules[] = 'max:' . $this->validation_rules['max_length'];
            }
            if (isset($this->validation_rules['pattern'])) {
                $rules[] = 'regex:' . $this->validation_rules['pattern'];
            }
            if (isset($this->validation_rules['min_value'])) {
                $rules[] = 'min:' . $this->validation_rules['min_value'];
            }
            if (isset($this->validation_rules['max_value'])) {
                $rules[] = 'max:' . $this->validation_rules['max_value'];
            }
        }

        return [$this->field_name => $rules];
    }

    /**
     * Get options for select/checkbox/radio fields
     */
    public function getFieldOptions(): array
    {
        if (!in_array($this->field_type, ['select', 'checkbox', 'radio'])) {
            return [];
        }

        return $this->options ?? [];
    }

    /**
     * Check if field has dependencies
     */
    public function hasDependencies(): bool
    {
        return !empty($this->dependencies);
    }

    /**
     * Get field HTML input attributes
     */
    public function getInputAttributes(): array
    {
        $attributes = [
            'name' => $this->field_name,
            'id' => $this->field_name,
            'class' => 'form-control'
        ];

        if ($this->is_required) {
            $attributes['required'] = true;
        }

        if ($this->field_description) {
            $attributes['title'] = $this->field_description;
        }

        // Type-specific attributes
        switch ($this->field_type) {
            case 'number':
            case 'range':
                if (isset($this->validation_rules['min_value'])) {
                    $attributes['min'] = $this->validation_rules['min_value'];
                }
                if (isset($this->validation_rules['max_value'])) {
                    $attributes['max'] = $this->validation_rules['max_value'];
                }
                break;
            case 'text':
            case 'email':
            case 'url':
                if (isset($this->validation_rules['max_length'])) {
                    $attributes['maxlength'] = $this->validation_rules['max_length'];
                }
                if (isset($this->validation_rules['pattern'])) {
                    $attributes['pattern'] = $this->validation_rules['pattern'];
                }
                break;
        }

        return $attributes;
    }

    /**
     * Format value for display
     */
    public function formatValue($value): string
    {
        if (empty($value)) {
            return '';
        }

        switch ($this->field_type) {
            case 'checkbox':
                if (is_array($value)) {
                    return implode(', ', $value);
                }
                break;
            case 'select':
            case 'radio':
                $options = $this->getFieldOptions();
                return $options[$value] ?? $value;
            case 'date':
                try {
                    return \Carbon\Carbon::parse($value)->format('d.m.Y');
                } catch (\Exception $e) {
                    return $value;
                }
        }

        return (string) $value;
    }

    /**
     * Get usage statistics for this field
     */
    public function getUsageStats(): array
    {
        $totalValues = $this->values()->count();
        $uniqueValues = $this->values()->distinct('field_value')->count();
        $emptyValues = $this->values()->whereNull('field_value')->orWhere('field_value', '')->count();

        return [
            'total_values' => $totalValues,
            'unique_values' => $uniqueValues,
            'empty_values' => $emptyValues,
            'completion_rate' => $totalValues > 0 ? round((($totalValues - $emptyValues) / $totalValues) * 100, 2) : 0
        ];
    }

    /**
     * Get most common values for this field
     */
    public function getMostCommonValues(int $limit = 10): array
    {
        return $this->values()
            ->whereNotNull('field_value')
            ->where('field_value', '!=', '')
            ->selectRaw('field_value, COUNT(*) as count')
            ->groupBy('field_value')
            ->orderByDesc('count')
            ->limit($limit)
            ->pluck('count', 'field_value')
            ->toArray();
    }
}
