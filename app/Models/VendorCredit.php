<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class VendorCredit extends Model
{
    protected $fillable = [
        'vendor_id',
        'credit_package_id',
        'credits_purchased',
        'credits_remaining',
        'amount_paid',
        'payment_status',
        'payment_reference',
        'payment_provider',
        'purchased_at'
    ];

    protected $casts = [
        'credits_purchased' => 'integer',
        'credits_remaining' => 'integer',
        'amount_paid' => 'decimal:2',
        'purchased_at' => 'datetime'
    ];

    // Relationships
    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function creditPackage()
    {
        return $this->belongsTo(CreditPackage::class);
    }

    public function promotions()
    {
        return $this->hasMany(RentalPromotion::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('payment_status', 'completed');
    }

    public function scopeWithBalance($query)
    {
        return $query->where('credits_remaining', '>', 0);
    }

    public function scopeForVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    // Business logic methods
    public function canSpendCredits($amount)
    {
        return $this->payment_status === 'completed' && $this->credits_remaining >= $amount;
    }

    public function spendCredits($amount, $promotionId = null)
    {
        if (!$this->canSpendCredits($amount)) {
            throw new \Exception('Insufficient credits or invalid payment status');
        }

        $this->decrement('credits_remaining', $amount);

        // Log credit usage
        $this->logCreditUsage($amount, $promotionId);

        return $this;
    }

    protected function logCreditUsage($amount, $promotionId = null)
    {
        // Create audit log entry for credit usage
        \Log::info('Credits spent', [
            'vendor_credit_id' => $this->id,
            'vendor_id' => $this->vendor_id,
            'credits_spent' => $amount,
            'promotion_id' => $promotionId,
            'remaining_balance' => $this->credits_remaining
        ]);
    }

    public function getUsedCreditsAttribute()
    {
        return $this->credits_purchased - $this->credits_remaining;
    }

    public function getUsagePercentageAttribute()
    {
        return $this->credits_purchased > 0
            ? round(($this->used_credits / $this->credits_purchased) * 100, 1)
            : 0;
    }

    // Static methods for vendor credit management
    public static function getVendorBalance($vendorId)
    {
        return static::forVendor($vendorId)
            ->completed()
            ->sum('credits_remaining');
    }

    public static function getVendorCreditHistory($vendorId, $limit = 10)
    {
        return static::forVendor($vendorId)
            ->with('creditPackage')
            ->orderBy('purchased_at', 'desc')
            ->limit($limit)
            ->get();
    }
}