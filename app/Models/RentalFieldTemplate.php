<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RentalFieldTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'sort_order',
        'settings'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
        'sort_order' => 'integer'
    ];

    /**
     * Relationship: Template has many fields
     */
    public function fields(): HasMany
    {
        return $this->hasMany(RentalField::class, 'template_id')->orderBy('sort_order');
    }

    /**
     * Relationship: Template belongs to many categories
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            Category::class,
            'rental_field_template_categories',
            'template_id',
            'category_id'
        )->withTimestamps();
    }

    /**
     * Scope: Only active templates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Ordered by sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Scope: Templates for specific category
     */
    public function scopeForCategory($query, $categoryId)
    {
        return $query->whereHas('categories', function ($q) use ($categoryId) {
            $q->where('category_id', $categoryId);
        });
    }

    /**
     * Get all field types used in this template
     */
    public function getFieldTypesAttribute(): array
    {
        return $this->fields->pluck('field_type')->unique()->values()->toArray();
    }

    /**
     * Get count of required fields
     */
    public function getRequiredFieldsCountAttribute(): int
    {
        return $this->fields->where('is_required', true)->count();
    }

    /**
     * Get count of filterable fields
     */
    public function getFilterableFieldsCountAttribute(): int
    {
        return $this->fields->where('is_filterable', true)->count();
    }

    /**
     * Check if template can be deleted (not used by any rentals)
     */
    public function canBeDeleted(): bool
    {
        return !RentalFieldValue::whereHas('field', function ($query) {
            $query->where('template_id', $this->id);
        })->exists();
    }

    /**
     * Get template usage statistics
     */
    public function getUsageStats(): array
    {
        $totalRentals = RentalFieldValue::whereHas('field', function ($query) {
            $query->where('template_id', $this->id);
        })->distinct('rental_id')->count();

        $categoryIds = $this->categories->pluck('id');
        $availableRentals = Rental::whereIn('category_id', $categoryIds)->count();

        return [
            'total_fields' => $this->fields->count(),
            'required_fields' => $this->required_fields_count,
            'filterable_fields' => $this->filterable_fields_count,
            'assigned_categories' => $this->categories->count(),
            'rentals_using_template' => $totalRentals,
            'available_rentals' => $availableRentals,
            'usage_percentage' => $availableRentals > 0 ? round(($totalRentals / $availableRentals) * 100, 2) : 0
        ];
    }

    /**
     * Duplicate template with all fields
     */
    public function duplicate(string $newName = null): self
    {
        $newTemplate = $this->replicate();
        $newTemplate->name = $newName ?? $this->name . ' (Copy)';
        $newTemplate->is_active = false; // Start inactive
        $newTemplate->save();

        // Copy all fields
        foreach ($this->fields as $field) {
            $newField = $field->replicate();
            $newField->template_id = $newTemplate->id;
            $newField->save();
        }

        // Copy category assignments
        $newTemplate->categories()->attach($this->categories->pluck('id'));

        return $newTemplate;
    }

    /**
     * Export template data for backup/import
     */
    public function exportData(): array
    {
        return [
            'template' => $this->only(['name', 'description', 'is_active', 'sort_order', 'settings']),
            'fields' => $this->fields->map(function ($field) {
                return $field->only([
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
                ]);
            })->toArray(),
            'categories' => $this->categories->pluck('id')->toArray()
        ];
    }

    /**
     * Import template from exported data
     */
    public static function importData(array $data): self
    {
        $template = static::create($data['template']);

        // Import fields
        foreach ($data['fields'] as $fieldData) {
            $template->fields()->create($fieldData);
        }

        // Import category assignments
        if (!empty($data['categories'])) {
            $template->categories()->attach($data['categories']);
        }

        return $template;
    }
}
