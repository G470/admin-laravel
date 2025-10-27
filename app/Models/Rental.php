<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    use HasFactory;

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

    protected $fillable = [
        'name',
        'title',
        'description',
        'vendor_id',
        'category_id',
        'location_id',
        'city_id',
        'address',
        'price',
        'price_ranges_id',
        'price_range_hour',
        'price_range_day',
        'price_range_once',
        'service_fee',
        'currency',
        'status',
        'featured',
        'images',
        'amenities',
        'rules',
        'cancellation_policy',
        'availability',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'latitude',
        'longitude',

    ];

    protected $casts = [
        'images' => 'array',
        'amenities' => 'array',
        'rules' => 'array',
        'availability' => 'array',
        'featured' => 'boolean',
        'price' => 'decimal:2',
        'price_range_hour' => 'decimal:2',
        'price_range_day' => 'decimal:2',
        'price_range_once' => 'decimal:2',
        'service_fee' => 'decimal:2',

    ];

    // Beziehungen
    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get additional locations for this rental (many-to-many relationship)
     */
    public function additionalLocations()
    {
        return $this->belongsToMany(Location::class, 'rental_locations');
    }

    /**
     * Get all locations for this rental (primary + additional)
     */
    public function locations()
    {
        return $this->additionalLocations();
    }

    public function city()
    {
        return $this->belongsTo(CitySeo::class, 'city_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the reviews for this rental.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the rental images.
     */
    public function images()
    {
        return $this->hasMany(RentalImage::class)->orderBy('order');
    }

    /**
     * Get the rental documents.
     */
    public function documents()
    {
        return $this->hasMany(RentalDocument::class);
    }

    /**
     * Get the user (vendor) for backward compatibility.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    /**
     * Get the dynamic field values for this rental.
     */
    public function fieldValues()
    {
        return $this->hasMany(RentalFieldValue::class, 'rental_id');
    }

    /**
     * Get the average rating for this rental.
     */
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->where('status', 'published')->avg('rating') ?? 0;
    }

    /**
     * Get the total number of reviews for this rental.
     */
    public function getReviewsCountAttribute()
    {
        return $this->reviews()->where('status', 'published')->count();
    }

    public function getTotalBookingsAttribute()
    {
        return $this->bookings()->count();
    }

    public function getMainImageAttribute()
    {
        return $this->images[0] ?? null;
    }

    /**
     * Get the active price type for this rental
     */
    public function getPriceTypeAttribute()
    {
        if ($this->price_range_hour && $this->price_range_hour > 0) {
            return 'hour';
        } elseif ($this->price_range_day && $this->price_range_day > 0) {
            return 'day';
        } elseif ($this->price_range_once && $this->price_range_once > 0) {
            return 'once';
        } elseif ($this->price && $this->price > 0) {
            return 'fixed';
        }
        return null;
    }

    /**
     * Get the active price value for this rental
     */
    public function getPriceValueAttribute()
    {
        switch ($this->price_type) {
            case 'hour':
                return $this->price_range_hour;
            case 'day':
                return $this->price_range_day;
            case 'once':
                return $this->price_range_once;
            case 'fixed':
                return $this->price;
            default:
                return 0;
        }
    }

    /**
     * Get the formatted price label for this rental
     */
    public function getPriceLabelAttribute()
    {
        if (!$this->price_type) {
            return 'Preis auf Anfrage';
        }

        $value = number_format($this->price_value, 2, ',', '.') . ' €';

        switch ($this->price_type) {
            case 'hour':
                return $value . ' / Stunde';
            case 'day':
                return $value . ' / Tag';
            case 'once':
                return $value . ' / Einmalig';
            case 'fixed':
                return $value;
            default:
                return 'Preis auf Anfrage';
        }
    }

    /**
     * Get the price display for listing (shorter version)
     */
    public function getPriceDisplayAttribute()
    {
        if (!$this->price_type) {
            return 'P.a.A.'; // Preis auf Anfrage
        }

        $value = number_format($this->price_value, 2, ',', '.') . ' €';

        switch ($this->price_type) {
            case 'hour':
                return $value . ' /h';
            case 'day':
                return $value . ' /Tag';
            case 'once':
                return $value;
            case 'fixed':
                return $value;
            default:
                return 'P.a.A.';
        }
    }
}
