<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use App\Models\CreditPackage;
use App\Models\VendorCredit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AssignWelcomeCredits
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        $user = $event->user;

        // Only assign credits to vendors
        if (!$user->is_vendor) {
            return;
        }

        // Get or create welcome credit package
        $welcomePackage = CreditPackage::firstOrCreate(
            ['name' => 'Willkommens-Credits'],
            [
                'credits_amount' => 122,
                'standard_price' => 0.00,
                'offer_price' => 0.00,
                'description' => 'Kostenlose Willkommens-Credits für neue Vendor',
                'sort_order' => 0,
                'is_active' => true
            ]
        );

        // Create vendor credit record
        VendorCredit::create([
            'vendor_id' => $user->id,
            'credit_package_id' => $welcomePackage->id,
            'credits_purchased' => 122,
            'credits_remaining' => 122,
            'amount_paid' => 0.00,
            'payment_status' => 'completed',
            'payment_reference' => 'WELCOME_' . $user->id,
            'payment_provider' => 'system',
            'purchased_at' => now()
        ]);

        // Log the welcome credits assignment
        \Log::info('Welcome credits assigned to new vendor', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'credits_assigned' => 122,
            'package_id' => $welcomePackage->id
        ]);
    }
}
