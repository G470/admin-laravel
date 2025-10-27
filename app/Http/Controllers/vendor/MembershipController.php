<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Services\VendorMembershipService;
use App\Services\SubscriptionPricingService;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    public function __construct()
    {
        // Middleware is handled by the route group
    }

    /**
     * Show membership overview
     */
    public function index()
    {
        $vendor = auth()->user();
        $subscription = VendorMembershipService::getCurrentSubscription($vendor);
        $stats = VendorMembershipService::getSubscriptionStats($vendor);

        // Get current usage for real-time calculation
        $currentPricing = SubscriptionPricingService::calculatePrice($vendor);

        return view('content.vendor.membership-packages', compact(
            'subscription',
            'stats',
            'currentPricing'
        ));
    }

    /**
     * Cancel subscription
     */
    public function cancel(Request $request)
    {
        $request->validate([
            'confirmation' => 'required|accepted',
        ]);

        $vendor = auth()->user();

        if (VendorMembershipService::processCancellation($vendor)) {
            return redirect()->route('vendor.membership.index')
                ->with('success', 'Ihr Abonnement wurde erfolgreich gekündigt.');
        }

        return redirect()->route('vendor.membership.index')
            ->with('error', 'Kündigung konnte nicht verarbeitet werden.');
    }

    /**
     * Change subscription (placeholder for future functionality)
     */
    public function change(Request $request)
    {
        // Future implementation for subscription plan changes
        return redirect()->route('vendor.membership.index')
            ->with('info', 'Funktion wird in Kürze verfügbar sein.');
    }
}