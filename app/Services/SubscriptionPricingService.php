<?php

namespace App\Services;

use App\Models\User;
use App\Models\Setting;
use App\Models\Rental;
use App\Models\Location;

class SubscriptionPricingService
{
    /**
     * Calculate monthly subscription price for vendor
     */
    public static function calculatePrice(User $vendor): array
    {
        $pricingRules = self::getPricingRules();

        $rentalCount = Rental::where('vendor_id', $vendor->id)->count();
        $categoryCount = Rental::where('vendor_id', $vendor->id)
            ->distinct('category_id')
            ->count('category_id');
        $locationCount = $vendor->locations()->count();

        $calculations = [
            'rental_submissions' => [
                'count' => $rentalCount,
                'price_per_item' => $pricingRules['price_per_rental'],
                'subtotal' => $rentalCount * $pricingRules['price_per_rental'],
            ],
            'booked_categories' => [
                'count' => $categoryCount,
                'price_per_item' => $pricingRules['price_per_category'],
                'subtotal' => $categoryCount * $pricingRules['price_per_category'],
            ],
            'booked_locations' => [
                'count' => $locationCount,
                'price_per_item' => $pricingRules['price_per_location'],
                'subtotal' => $locationCount * $pricingRules['price_per_location'],
            ],
        ];

        $basePrice = $pricingRules['base_monthly_fee'];
        $totalVariablePrice = array_sum(array_column($calculations, 'subtotal'));
        $totalPrice = $basePrice + $totalVariablePrice;

        // Apply volume discounts
        $discount = self::calculateVolumeDiscount($totalPrice, $pricingRules);
        $finalPrice = $totalPrice - $discount;

        return [
            'base_fee' => $basePrice,
            'variable_costs' => $calculations,
            'subtotal' => $totalPrice,
            'discount' => $discount,
            'total' => max($finalPrice, $pricingRules['minimum_monthly_fee']),
            'currency' => 'EUR',
        ];
    }

    /**
     * Get pricing rules from settings
     */
    private static function getPricingRules(): array
    {
        return [
            'base_monthly_fee' => (float) Setting::get('subscription_base_monthly_fee', 29.99),
            'price_per_rental' => (float) Setting::get('subscription_price_per_rental', 2.50),
            'price_per_category' => (float) Setting::get('subscription_price_per_category', 5.00),
            'price_per_location' => (float) Setting::get('subscription_price_per_location', 3.00),
            'minimum_monthly_fee' => (float) Setting::get('subscription_minimum_monthly_fee', 19.99),
            'volume_discount_threshold' => (float) Setting::get('subscription_volume_discount_threshold', 100.00),
            'volume_discount_percentage' => (float) Setting::get('subscription_volume_discount_percentage', 10),
        ];
    }

    /**
     * Calculate volume discount
     */
    private static function calculateVolumeDiscount(float $totalPrice, array $rules): float
    {
        if ($totalPrice >= $rules['volume_discount_threshold']) {
            return $totalPrice * ($rules['volume_discount_percentage'] / 100);
        }

        return 0;
    }

    /**
     * Update pricing rules
     */
    public static function updatePricingRules(array $rules): void
    {
        foreach ($rules as $key => $value) {
            Setting::set("subscription_{$key}", $value);
        }
    }
}