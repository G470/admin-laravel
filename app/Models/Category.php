<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'status',
        'order',
        'meta_title',
        'meta_description',
        'default_text_content',
        'category_image',
        'form_template_display_style',
    ];

    /**
     * Get the parent category
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the child categories
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Get all rentals in this category
     */
    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }

    /**
     * Get rental field templates associated with this category
     */
    public function rentalFieldTemplates(): BelongsToMany
    {
        return $this->belongsToMany(
            RentalFieldTemplate::class,
            'rental_field_template_categories',
            'category_id',
            'template_id'
        )->withTimestamps();
    }

    /**
     * Scope to order categories by order field
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }

    /**
     * Scope to get only online categories
     */
    public function scopeOnline($query)
    {
        return $query->where('status', 'online');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get the full path of the category
     */
    public function getFullPathAttribute()
    {
        $path = [$this->name];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }

        return implode(' > ', $path);
    }
}