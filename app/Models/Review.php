<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'rental_id',
        'user_id',
        'rating',
        'comment',
        'status',
        'is_verified',
        'stay_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'rating' => 'integer',
        'is_verified' => 'boolean',
        'stay_date' => 'date',
    ];

    /**
     * Get the rental that the review belongs to.
     */
    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }

    /**
     * Get the user who wrote the review.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the replies for this review.
     */
    public function replies()
    {
        return $this->hasMany(ReviewReply::class);
    }

    /**
     * Scope a query to only include reviews of a specific status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include verified reviews.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope a query to filter by rating.
     */
    public function scopeRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }
}
