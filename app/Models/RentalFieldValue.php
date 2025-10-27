<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalFieldValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'rental_id',
        'field_id',
        'field_value',
        'additional_data'
    ];

    protected $casts = [
        'additional_data' => 'array'
    ];

    /**
     * Relationship: Value belongs to rental
     */
    public function rental(): BelongsTo
    {
        return $this->belongsTo(Rental::class);
    }

    /**
     * Relationship: Value belongs to field
     */
    public function field(): BelongsTo
    {
        return $this->belongsTo(RentalField::class, 'field_id');
    }

    /**
     * Scope: Values for specific rental
     */
    public function scopeForRental($query, $rentalId)
    {
        return $query->where('rental_id', $rentalId);
    }

    /**
     * Scope: Values for specific field
     */
    public function scopeForField($query, $fieldId)
    {
        return $query->where('field_id', $fieldId);
    }

    /**
     * Scope: Non-empty values
     */
    public function scopeNonEmpty($query)
    {
        return $query->whereNotNull('field_value')->where('field_value', '!=', '');
    }

    /**
     * Get formatted value for display
     */
    public function getFormattedValueAttribute(): string
    {
        return $this->field ? $this->field->formatValue($this->field_value) : $this->field_value;
    }

    /**
     * Get typed value (cast to appropriate type)
     */
    public function getTypedValueAttribute()
    {
        if (!$this->field) {
            return $this->field_value;
        }

        switch ($this->field->field_type) {
            case 'number':
            case 'range':
                return is_numeric($this->field_value) ? (float) $this->field_value : null;
            case 'checkbox':
                if (empty($this->field_value)) {
                    return [];
                }
                return is_string($this->field_value) ? explode(',', $this->field_value) : $this->field_value;
            case 'date':
                try {
                    return \Carbon\Carbon::parse($this->field_value);
                } catch (\Exception $e) {
                    return $this->field_value;
                }
            default:
                return $this->field_value;
        }
    }

    /**
     * Set value with automatic type conversion
     */
    public function setTypedValue($value): void
    {
        if (!$this->field) {
            $this->field_value = $value;
            return;
        }

        switch ($this->field->field_type) {
            case 'checkbox':
                if (is_array($value)) {
                    $this->field_value = implode(',', array_filter($value));
                } else {
                    $this->field_value = $value;
                }
                break;
            case 'date':
                if ($value instanceof \Carbon\Carbon) {
                    $this->field_value = $value->format('Y-m-d');
                } elseif (is_string($value)) {
                    try {
                        $this->field_value = \Carbon\Carbon::parse($value)->format('Y-m-d');
                    } catch (\Exception $e) {
                        $this->field_value = $value;
                    }
                } else {
                    $this->field_value = $value;
                }
                break;
            default:
                $this->field_value = $value;
        }
    }

    /**
     * Check if value is empty
     */
    public function isEmpty(): bool
    {
        return empty($this->field_value) || $this->field_value === '';
    }

    /**
     * Check if value matches filter criteria
     */
    public function matchesFilter(array $filter): bool
    {
        if (!$this->field) {
            return false;
        }

        $value = $this->typed_value;

        switch ($this->field->field_type) {
            case 'number':
            case 'range':
                if (isset($filter['min']) && $value < $filter['min']) {
                    return false;
                }
                if (isset($filter['max']) && $value > $filter['max']) {
                    return false;
                }
                return true;

            case 'select':
            case 'radio':
                return in_array($this->field_value, (array) $filter['values']);

            case 'checkbox':
                $fieldValues = is_array($value) ? $value : explode(',', $value);
                return !empty(array_intersect($fieldValues, (array) $filter['values']));

            case 'date':
                if (isset($filter['from'])) {
                    $from = \Carbon\Carbon::parse($filter['from']);
                    if ($value < $from) {
                        return false;
                    }
                }
                if (isset($filter['to'])) {
                    $to = \Carbon\Carbon::parse($filter['to']);
                    if ($value > $to) {
                        return false;
                    }
                }
                return true;

            default:
                if (isset($filter['search'])) {
                    return stripos($this->field_value, $filter['search']) !== false;
                }
                return true;
        }
    }

    /**
     * Update or create field value for rental
     */
    public static function updateOrCreateForRental(int $rentalId, int $fieldId, $value): self
    {
        // If value is empty and not a checkbox, delete the record
        $field = RentalField::find($fieldId);
        if ($field && $field->field_type !== 'checkbox' && empty($value)) {
            static::where('rental_id', $rentalId)
                ->where('field_id', $fieldId)
                ->delete();
            return new static();
        }

        $fieldValue = static::updateOrCreate(
            ['rental_id' => $rentalId, 'field_id' => $fieldId],
            ['field_value' => $value]
        );

        // Set typed value to ensure proper formatting
        $fieldValue->setTypedValue($value);
        $fieldValue->save();

        return $fieldValue;
    }

    /**
     * Get all values for a rental as key-value array
     */
    public static function getValuesForRental($rental): array
    {
        return static::with('field')
            ->where('rental_id', $rental)
            ->whereNotNull('field_value')
            ->where('field_value', '!=', '')
            ->get()
            ->mapWithKeys(function ($value) {
                return [$value->field->field_name => $value->typed_value];
            })
            ->toArray();
    }

    /**
     * Bulk update values for a rental
     */
    public static function bulkUpdateForRental(int $rentalId, array $values): void
    {
        foreach ($values as $fieldName => $value) {
            $field = RentalField::where('field_name', $fieldName)->first();
            if ($field && !empty($value)) {
                static::updateOrCreateForRental($rentalId, $field->id, $value);
            }
        }
    }
}
