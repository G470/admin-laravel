<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreditPackage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'credits_amount',
        'standard_price',
        'offer_price',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'credits_amount' => 'integer',
        'standard_price' => 'decimal:2',
        'offer_price' => 'decimal:2',
        'sort_order' => 'integer',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function vendorCredits()
    {
        return $this->hasMany(VendorCredit::class);
    }

    public function purchases()
    {
        return $this->vendorCredits()->where('payment_status', 'completed');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrderedForDisplay($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Accessors
    public function getDiscountPercentageAttribute()
    {
        if ($this->standard_price == 0)
            return 0;

        return round((($this->standard_price - $this->offer_price) / $this->standard_price) * 100);
    }

    public function getPricePerCreditAttribute()
    {
        return $this->credits_amount > 0 ? $this->offer_price / $this->credits_amount : 0;
    }

    public function getIsDiscountedAttribute()
    {
        return $this->offer_price < $this->standard_price;
    }

    // Business logic methods
    public function getTotalPurchases()
    {
        return $this->purchases()->sum('credits_purchased');
    }

    public function getTotalRevenue()
    {
        return $this->purchases()->sum('amount_paid');
    }

    public function getPopularityScore()
    {
        $purchases = $this->purchases()->count();
        $totalPackages = static::active()->count();

        return $totalPackages > 0 ? ($purchases / $totalPackages) * 100 : 0;
    }

    // Validation rules
    public static function rules($id = null)
    {
        return [
            'name' => 'required|string|max:100|unique:credit_packages,name,' . $id,
            'description' => 'nullable|string|max:1000',
            'credits_amount' => 'required|integer|min:1|max:10000',
            'standard_price' => 'required|numeric|min:0.01|max:9999.99',
            'offer_price' => 'required|numeric|min:0.01|max:9999.99|lte:standard_price',
            'sort_order' => 'required|integer|min:0|max:999',
            'is_active' => 'boolean'
        ];
    }
}