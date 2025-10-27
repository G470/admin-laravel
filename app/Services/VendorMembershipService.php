<?php

namespace App\Services;

use App\Models\User;
use App\Models\VendorSubscription;
use App\Models\VendorSubscriptionHistory;
use Carbon\Carbon;

class VendorMembershipService
{
    /**
     * Get current subscription for vendor
     */
    public static function getCurrentSubscription(User $vendor): ?VendorSubscription
    {
        return VendorSubscription::where('vendor_id', $vendor->id)
            ->where('status', 'active')
            ->latest()
            ->first();
    }

    /**
     * Create or update subscription for vendor
     */
    public static function createOrUpdateSubscription(User $vendor): VendorSubscription
    {
        $pricing = SubscriptionPricingService::calculatePrice($vendor);

        $subscription = self::getCurrentSubscription($vendor);

        if (!$subscription) {
            $subscription = new VendorSubscription();
            $subscription->vendor_id = $vendor->id;
            $subscription->status = 'active';
            $subscription->start_date = now();
            $action = 'created';
        } else {
            $action = 'updated';
        }

        // Update subscription details
        $subscription->monthly_price = $pricing['total'];
        $subscription->rental_count = $pricing['variable_costs']['rental_submissions']['count'];
        $subscription->category_count = $pricing['variable_costs']['booked_categories']['count'];
        $subscription->location_count = $pricing['variable_costs']['booked_locations']['count'];
        $subscription->pricing_breakdown = $pricing;

        // Calculate dates
        $subscription->end_date = $subscription->start_date->addMonths(3);
        $subscription->next_billing_date = $subscription->start_date->addMonth();
        $subscription->cancellation_deadline = $subscription->next_billing_date->subDays(30);

        $subscription->save();

        // Log history
        self::logSubscriptionChange($subscription, $action);

        return $subscription;
    }

    /**
     * Process subscription cancellation
     */
    public static function processCancellation(User $vendor): bool
    {
        $subscription = self::getCurrentSubscription($vendor);

        if (!$subscription || !$subscription->canBeCancelled()) {
            return false;
        }

        $subscription->status = 'cancelled';
        $subscription->save();

        // Log cancellation
        self::logSubscriptionChange($subscription, 'cancelled');

        return true;
    }

    /**
     * Calculate next billing date
     */
    public static function calculateNextBillingDate(VendorSubscription $subscription): Carbon
    {
        return $subscription->next_billing_date->addMonth();
    }

    /**
     * Log subscription changes
     */
    private static function logSubscriptionChange(VendorSubscription $subscription, string $action): void
    {
        VendorSubscriptionHistory::create([
            'vendor_id' => $subscription->vendor_id,
            'subscription_id' => $subscription->id,
            'action' => $action,
            'old_price' => $subscription->getOriginal('monthly_price'),
            'new_price' => $subscription->monthly_price,
            'rental_count' => $subscription->rental_count,
            'category_count' => $subscription->category_count,
            'location_count' => $subscription->location_count,
            'notes' => "Subscription {$action} for vendor {$subscription->vendor_id}",
        ]);
    }

    /**
     * Get subscription statistics
     */
    public static function getSubscriptionStats(User $vendor): array
    {
        $subscription = self::getCurrentSubscription($vendor);

        if (!$subscription) {
            return [
                'status' => 'No active subscription',
                'monthly_price' => 0,
                'rental_count' => 0,
                'category_count' => 0,
                'location_count' => 0,
                'next_billing' => null,
                'cancellation_deadline' => null,
            ];
        }

        return [
            'status' => $subscription->status,
            'monthly_price' => $subscription->monthly_price,
            'rental_count' => $subscription->rental_count,
            'category_count' => $subscription->category_count,
            'location_count' => $subscription->location_count,
            'next_billing' => $subscription->next_billing_date,
            'cancellation_deadline' => $subscription->cancellation_deadline,
            'total_with_vat' => $subscription->total_with_vat,
            'vat_amount' => $subscription->vat_amount,
        ];
    }
}