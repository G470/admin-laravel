<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\RentalPush;
use App\Models\Rental;
use App\Models\Category;
use App\Models\Location;
use App\Models\VendorCredit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RentalPushController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('vendor');
    }

    /**
     * Display a listing of rental pushes
     */
    public function index()
    {
        return view('vendor.rental-pushes.index');
    }

    /**
     * Show the form for creating a new rental push
     */
    public function create()
    {
        $vendor = Auth::user();
        $rentals = Rental::where('vendor_id', $vendor->id)
            ->where('status', 'active')
            ->orderBy('title')
            ->get();
        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        $vendorBalance = VendorCredit::getVendorBalance($vendor->id);

        return view('vendor.rental-pushes.create', compact('rentals', 'categories', 'locations', 'vendorBalance'));
    }

    /**
     * Store a newly created rental push
     */
    public function store(Request $request)
    {
        $vendor = Auth::user();

        $validated = $request->validate([
            'rental_id' => 'required|exists:rentals,id',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'frequency' => 'required|integer|min:1|max:7',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ]);

        // Verify rental belongs to vendor
        $rental = Rental::where('id', $validated['rental_id'])
            ->where('vendor_id', $vendor->id)
            ->firstOrFail();

        // Calculate credits needed
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $daysDiff = $startDate->diffInDays($endDate);
        $totalPushes = $daysDiff * $validated['frequency'];
        $totalCreditsNeeded = $totalPushes; // 1 credit per push

        // Check if vendor has enough credits
        $vendorBalance = VendorCredit::getVendorBalance($vendor->id);
        if ($vendorBalance < $totalCreditsNeeded) {
            return back()->withErrors(['credits' => "Sie haben nicht genügend Credits. Benötigt: {$totalCreditsNeeded}, Verfügbar: {$vendorBalance}"]);
        }

        // Create rental push
        $rentalPush = RentalPush::create([
            'vendor_id' => $vendor->id,
            'rental_id' => $validated['rental_id'],
            'category_id' => $validated['category_id'],
            'location_id' => $validated['location_id'],
            'frequency' => $validated['frequency'],
            'credits_per_push' => 1,
            'total_credits_needed' => $totalCreditsNeeded,
            'credits_used' => 0,
            'status' => 'active',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'next_push_at' => $startDate,
            'is_active' => true
        ]);

        return redirect()->route('vendor.rental-pushes.index')
            ->with('success', 'Artikel-Push wurde erfolgreich erstellt!');
    }

    /**
     * Display the specified rental push
     */
    public function show(RentalPush $rentalPush)
    {
        // Ensure vendor owns this push
        if ($rentalPush->vendor_id !== Auth::id()) {
            abort(403);
        }

        $rentalPush->load(['rental', 'category', 'location', 'creditTransactions']);

        return view('vendor.rental-pushes.show', compact('rentalPush'));
    }

    /**
     * Show the form for editing the specified rental push
     */
    public function edit(RentalPush $rentalPush)
    {
        // Ensure vendor owns this push
        if ($rentalPush->vendor_id !== Auth::id()) {
            abort(403);
        }

        $vendor = Auth::user();
        $rentals = Rental::where('vendor_id', $vendor->id)
            ->where('status', 'active')
            ->orderBy('title')
            ->get();
        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('vendor.rental-pushes.edit', compact('rentalPush', 'rentals', 'categories', 'locations'));
    }

    /**
     * Update the specified rental push
     */
    public function update(Request $request, RentalPush $rentalPush)
    {
        // Ensure vendor owns this push
        if ($rentalPush->vendor_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'rental_id' => 'required|exists:rentals,id',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'frequency' => 'required|integer|min:1|max:7',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,paused,cancelled'
        ]);

        // Verify rental belongs to vendor
        $rental = Rental::where('id', $validated['rental_id'])
            ->where('vendor_id', Auth::id())
            ->firstOrFail();

        // Recalculate credits if dates or frequency changed
        if (
            $rentalPush->start_date != $validated['start_date'] ||
            $rentalPush->end_date != $validated['end_date'] ||
            $rentalPush->frequency != $validated['frequency']
        ) {

            $startDate = Carbon::parse($validated['start_date']);
            $endDate = Carbon::parse($validated['end_date']);
            $daysDiff = $startDate->diffInDays($endDate);
            $totalPushes = $daysDiff * $validated['frequency'];
            $totalCreditsNeeded = $totalPushes;

            // Check if vendor has enough credits for the difference
            $vendorBalance = VendorCredit::getVendorBalance(Auth::id());
            $creditsDifference = $totalCreditsNeeded - $rentalPush->total_credits_needed;

            if ($creditsDifference > 0 && $vendorBalance < $creditsDifference) {
                return back()->withErrors(['credits' => "Sie haben nicht genügend Credits für die Änderungen. Benötigt: {$creditsDifference}, Verfügbar: {$vendorBalance}"]);
            }

            $validated['total_credits_needed'] = $totalCreditsNeeded;
        }

        $rentalPush->update($validated);

        return redirect()->route('vendor.rental-pushes.index')
            ->with('success', 'Artikel-Push wurde erfolgreich aktualisiert!');
    }

    /**
     * Remove the specified rental push
     */
    public function destroy(RentalPush $rentalPush)
    {
        // Ensure vendor owns this push
        if ($rentalPush->vendor_id !== Auth::id()) {
            abort(403);
        }

        $rentalPush->update([
            'status' => 'cancelled',
            'is_active' => false
        ]);

        return redirect()->route('vendor.rental-pushes.index')
            ->with('success', 'Artikel-Push wurde erfolgreich abgebrochen!');
    }

    /**
     * Toggle push status (active/paused)
     */
    public function toggleStatus(RentalPush $rentalPush)
    {
        // Ensure vendor owns this push
        if ($rentalPush->vendor_id !== Auth::id()) {
            abort(403);
        }

        $newStatus = $rentalPush->status === 'active' ? 'paused' : 'active';
        $rentalPush->update(['status' => $newStatus]);

        $statusLabel = $newStatus === 'active' ? 'aktiviert' : 'pausiert';

        return back()->with('success', "Artikel-Push wurde erfolgreich {$statusLabel}!");
    }

    /**
     * Get push statistics for vendor
     */
    public function statistics()
    {
        $vendor = Auth::user();

        $stats = [
            'total_pushes' => RentalPush::forVendor($vendor->id)->count(),
            'active_pushes' => RentalPush::forVendor($vendor->id)->active()->count(),
            'total_credits_used' => RentalPush::forVendor($vendor->id)->sum('credits_used'),
            'current_balance' => VendorCredit::getVendorBalance($vendor->id),
            'pushes_this_month' => RentalPush::forVendor($vendor->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count()
        ];

        return response()->json($stats);
    }
}
