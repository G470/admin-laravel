<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class RentalPush extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'rental_id',
        'category_id',
        'location_id',
        'frequency',
        'credits_per_push',
        'total_credits_needed',
        'credits_used',
        'status',
        'start_date',
        'end_date',
        'last_push_at',
        'next_push_at',
        'is_active'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'last_push_at' => 'datetime',
        'next_push_at' => 'datetime',
        'is_active' => 'boolean',
        'credits_per_push' => 'integer',
        'total_credits_needed' => 'integer',
        'credits_used' => 'integer',
        'frequency' => 'integer'
    ];

    // Relationships
    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function creditTransactions()
    {
        return $this->hasMany(RentalPushCreditTransaction::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    public function scopeForRental($query, $rentalId)
    {
        return $query->where('rental_id', $rentalId);
    }

    public function scopeForCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeForLocation($query, $locationId)
    {
        return $query->where('location_id', $locationId);
    }

    public function scopeDueForPush($query)
    {
        return $query->where('next_push_at', '<=', now())
            ->where('is_active', true)
            ->where('status', 'active');
    }

    // Business logic methods
    public function calculateCreditsNeeded()
    {
        $daysDiff = $this->start_date->diffInDays($this->end_date);
        $pushesPerDay = $this->frequency;
        $totalPushes = $daysDiff * $pushesPerDay;

        return $totalPushes * $this->credits_per_push;
    }

    public function canExecutePush()
    {
        return $this->is_active &&
            $this->status === 'active' &&
            $this->next_push_at <= now() &&
            $this->hasEnoughCredits();
    }

    public function hasEnoughCredits()
    {
        $vendorBalance = VendorCredit::getVendorBalance($this->vendor_id);
        return $vendorBalance >= $this->credits_per_push;
    }

    public function executePush()
    {
        if (!$this->canExecutePush()) {
            throw new \Exception('Cannot execute push: insufficient credits or invalid status');
        }

        // Deduct credits from vendor
        $this->deductCredits();

        // Update push timestamps
        $this->last_push_at = now();
        $this->next_push_at = $this->calculateNextPushTime();
        $this->credits_used += $this->credits_per_push;

        // Check if push campaign should end
        if ($this->credits_used >= $this->total_credits_needed) {
            $this->status = 'completed';
            $this->is_active = false;
        }

        $this->save();

        // Log the push execution
        $this->logPushExecution();

        return $this;
    }

    protected function deductCredits()
    {
        $vendorCredits = VendorCredit::forVendor($this->vendor_id)
            ->completed()
            ->withBalance()
            ->orderBy('purchased_at', 'asc')
            ->first();

        if (!$vendorCredits) {
            throw new \Exception('No available credits found');
        }

        $vendorCredits->spendCredits($this->credits_per_push, $this->id);
    }

    protected function calculateNextPushTime()
    {
        $hoursBetweenPushes = 24 / $this->frequency;
        return now()->addHours($hoursBetweenPushes);
    }

    protected function logPushExecution()
    {
        // Create credit transaction record
        RentalPushCreditTransaction::create([
            'rental_push_id' => $this->id,
            'vendor_id' => $this->vendor_id,
            'credits_used' => $this->credits_per_push,
            'push_executed_at' => now(),
            'next_push_at' => $this->next_push_at
        ]);

        \Log::info('Rental push executed', [
            'rental_push_id' => $this->id,
            'rental_id' => $this->rental_id,
            'vendor_id' => $this->vendor_id,
            'category_id' => $this->category_id,
            'location_id' => $this->location_id,
            'credits_used' => $this->credits_per_push,
            'next_push_at' => $this->next_push_at
        ]);
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        return [
            'active' => 'Aktiv',
            'paused' => 'Pausiert',
            'completed' => 'Abgeschlossen',
            'cancelled' => 'Abgebrochen'
        ][$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        return [
            'active' => 'success',
            'paused' => 'warning',
            'completed' => 'info',
            'cancelled' => 'danger'
        ][$this->status] ?? 'secondary';
    }

    public function getFrequencyLabelAttribute()
    {
        return $this->frequency . 'x am Tag';
    }

    public function getRemainingCreditsAttribute()
    {
        return $this->total_credits_needed - $this->credits_used;
    }

    public function getProgressPercentageAttribute()
    {
        return $this->total_credits_needed > 0
            ? round(($this->credits_used / $this->total_credits_needed) * 100, 1)
            : 0;
    }

    public function getTimeUntilNextPushAttribute()
    {
        if (!$this->next_push_at) {
            return null;
        }

        $diff = now()->diff($this->next_push_at);

        if ($diff->days > 0) {
            return $diff->days . ' Tag(e) ' . $diff->h . ' Stunde(n)';
        } elseif ($diff->h > 0) {
            return $diff->h . ' Stunde(n) ' . $diff->i . ' Minute(n)';
        } else {
            return $diff->i . ' Minute(n)';
        }
    }

    // Static methods
    public static function getFrequencyOptions()
    {
        return [
            1 => '1x am Tag / 24 Stunden',
            2 => '2x am Tag / 12 Stunden',
            3 => '3x am Tag / 8 Stunden',
            4 => '4x am Tag / 6 Stunden',
            5 => '5x am Tag / 4.8 Stunden',
            6 => '6x am Tag / 4 Stunden',
            7 => '7x am Tag (Empfohlen) / 3.4 Stunden'
        ];
    }

    public static function getStatusOptions()
    {
        return [
            'active' => 'Aktiv',
            'paused' => 'Pausiert',
            'completed' => 'Abgeschlossen',
            'cancelled' => 'Abgebrochen'
        ];
    }
}