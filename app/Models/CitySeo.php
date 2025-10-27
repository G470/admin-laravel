<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CitySeo extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'city',
        'state',
        'country',
        'slug',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'content',
        'description',
        'status',
        'category_id',
        'featured_image',
        'latitude',
        'longitude',
        'population',
        'sort_order',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'population' => 'integer',
        'sort_order' => 'integer',
    ];

    public function getRelatedRentalsCount()
    {
        // Get count of rentals in locations that match this city
        return Rental::whereHas('location', function ($query) {
            $query->where('city', $this->city)
                  ->where('country', $this->country);
        })->count();
    }

    public function getRelatedLocations()
    {
        // Get locations that match this city
        return Location::where('city', $this->city)
                      ->where('country', $this->country)
                      ->get();
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getFullLocationAttribute()
    {
        $parts = array_filter([$this->city, $this->state, $this->country]);
        return implode(', ', $parts);
    }
}