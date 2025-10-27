<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\VendorCredit;
use App\Models\CreditPackage;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get or create a welcome credit package
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

        // Give welcome credits to all existing vendors who don't have any credits yet
        $vendors = User::where('is_vendor', true)->get();

        foreach ($vendors as $vendor) {
            $existingCredits = VendorCredit::where('vendor_id', $vendor->id)->sum('credits_remaining');

            if ($existingCredits == 0) {
                VendorCredit::create([
                    'vendor_id' => $vendor->id,
                    'credit_package_id' => $welcomePackage->id,
                    'credits_purchased' => 122,
                    'credits_remaining' => 122,
                    'amount_paid' => 0.00,
                    'payment_status' => 'completed',
                    'payment_reference' => 'WELCOME_' . $vendor->id,
                    'payment_provider' => 'system',
                    'purchased_at' => now()
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove welcome credits
        VendorCredit::where('payment_reference', 'like', 'WELCOME_%')->delete();

        // Remove welcome package
        CreditPackage::where('name', 'Willkommens-Credits')->delete();
    }
};
