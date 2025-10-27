<?php
namespace App\Http\Controllers\vendor;

use App\Http\Controllers\Controller;
use App\Models\CreditPackage;
use App\Models\VendorCredit;
use App\Models\RentalPromotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreditController extends Controller
{
    public function index()
    {
        $vendor = Auth::user();

        $availablePackages = CreditPackage::active()
            ->orderedForDisplay()
            ->get();

        $creditBalance = VendorCredit::getVendorBalance($vendor->id);

        $purchaseHistory = VendorCredit::getVendorCreditHistory($vendor->id, 10);

        $activePromotions = RentalPromotion::forVendor($vendor->id)
            ->active()
            ->with(['rental', 'category'])
            ->get();

        return view('vendor.credits.index', compact(
            'availablePackages',
            'creditBalance',
            'purchaseHistory',
            'activePromotions'
        ));
    }

    public function purchase(Request $request, CreditPackage $creditPackage)
    {
        if (!$creditPackage->is_active) {
            return back()->with('error', 'Dieses Credit-Paket ist nicht verfügbar.');
        }

        $vendor = Auth::user();

        // Create pending purchase record
        $vendorCredit = VendorCredit::create([
            'vendor_id' => $vendor->id,
            'credit_package_id' => $creditPackage->id,
            'credits_purchased' => $creditPackage->credits_amount,
            'credits_remaining' => $creditPackage->credits_amount,
            'amount_paid' => $creditPackage->offer_price,
            'payment_status' => 'pending',
            'purchased_at' => now()
        ]);

        // Redirect to payment processing
        return redirect()->route('vendor.credits.payment', $vendorCredit)
            ->with('info', 'Weiterleitung zur Zahlung...');
    }

    public function payment(VendorCredit $vendorCredit)
    {
        // Ensure vendor owns this credit purchase
        if ($vendorCredit->vendor_id !== Auth::id()) {
            abort(403);
        }

        if ($vendorCredit->payment_status !== 'pending') {
            return redirect()->route('vendor.credits.index')
                ->with('info', 'Diese Zahlung wurde bereits verarbeitet.');
        }

        return view('vendor.credits.payment', compact('vendorCredit'));
    }

    public function paymentSuccess(VendorCredit $vendorCredit)
    {
        // Payment webhook should have already updated status
        // This is just the success page

        if ($vendorCredit->payment_status === 'completed') {
            return view('vendor.credits.payment-success', compact('vendorCredit'));
        }

        return redirect()->route('vendor.credits.index')
            ->with('warning', 'Zahlung wird noch verarbeitet...');
    }

    public function history()
    {
        $vendor = Auth::user();

        $credits = VendorCredit::forVendor($vendor->id)
            ->with('creditPackage')
            ->orderBy('purchased_at', 'desc')
            ->paginate(20);

        $totalSpent = VendorCredit::forVendor($vendor->id)
            ->completed()
            ->sum('amount_paid');

        $totalCreditsUsed = RentalPromotion::forVendor($vendor->id)
            ->sum('credits_spent');

        return view('vendor.credits.history', compact('credits', 'totalSpent', 'totalCreditsUsed'));
    }
}