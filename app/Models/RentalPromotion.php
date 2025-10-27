<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RentalPromotion extends Model
{
    protected $fillable = [
        'rental_id',
        'vendor_id',
        'category_id',
        'vendor_credit_id',
        'credits_spent',
        'promotion_type',
        'starts_at',
        'expires_at',
        'is_active'
    ];

    protected $casts = [
        'credits_spent' => 'integer',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function vendorCredit()
    {
        return $this->belongsTo(VendorCredit::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('expires_at', '>', now());
    }

    public function scopeForCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeForVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('promotion_type', $type);
    }

    // Business logic methods
    public function isCurrentlyActive()
    {
        return $this->is_active
            && $this->starts_at <= now()
            && $this->expires_at > now();
    }

    public function getRemainingDays()
    {
        if (!$this->isCurrentlyActive())
            return 0;

        return now()->diffInDays($this->expires_at);
    }

    public function getRemainingHours()
    {
        if (!$this->isCurrentlyActive())
            return 0;

        return now()->diffInHours($this->expires_at);
    }

    public function extend($days)
    {
        $this->expires_at = $this->expires_at->addDays($days);
        return $this->save();
    }

    public function deactivate()
    {
        $this->is_active = false;
        return $this->save();
    }

    // Static methods for promotion management
    public static function createPromotion($rentalId, $vendorId, $categoryId, $creditsToSpend, $durationDays = 7, $promotionType = 'featured')
    {
        // Find available vendor credits
        $vendorCredit = VendorCredit::forVendor($vendorId)
            ->completed()
            ->withBalance()
            ->where('credits_remaining', '>=', $creditsToSpend)
            ->first();

        if (!$vendorCredit) {
            throw new \Exception('Insufficient credits available');
        }

        // Spend credits
        $vendorCredit->spendCredits($creditsToSpend);

        // Create promotion
        return static::create([
            'rental_id' => $rentalId,
            'vendor_id' => $vendorId,
            'category_id' => $categoryId,
            'vendor_credit_id' => $vendorCredit->id,
            'credits_spent' => $creditsToSpend,
            'promotion_type' => $promotionType,
            'starts_at' => now(),
            'expires_at' => now()->addDays($durationDays),
            'is_active' => true
        ]);
    }

    public static function getActivePromotionsForCategory($categoryId)
    {
        return static::active()
            ->forCategory($categoryId)
            ->with(['rental', 'vendor'])
            ->orderBy('promotion_type', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();
    }
}