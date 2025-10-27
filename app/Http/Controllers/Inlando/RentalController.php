<?php

namespace App\Http\Controllers\Inlando;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\Request;
use App\Models\User;

class RentalController extends Controller
{
    /**
     * Display the specified rental.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $rental = Rental::where('id', $id)->where('status', 'active')->firstOrFail();
        $rental->load('user', 'category', 'location', 'fieldValues.field'); // Eager load relationships including dynamic fields

        // You might want to get related rentals as well
        $relatedRentals = Rental::where('category_id', $rental->category_id)
            ->where('id', '!=', $rental->id)
            ->where('status', 'active')
            ->inRandomOrder()
            ->take(4)
            ->get();

        // Check if dateRange is set in request, otherwise set it to empty
        $dateRange = $request->get('dateRange', '');

        return view('inlando.rental-show', compact('rental', 'relatedRentals', 'dateRange'));
    }

    /**
     * Display the rental request page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function request(Request $request, $id)
    {
        $rental = Rental::where('id', $id)->where('status', 'active')->firstOrFail();
        $rental->load('user', 'category', 'location');

        // Get request parameters for pre-filling form
        $dateRange = $request->get('dateRange', '');
        $dateFrom = $request->get('dateFrom', '');
        $dateTo = $request->get('dateTo', '');
        $rentalType = $request->get('rentalType', 'daily');

        return view('inlando.rental-request', compact('rental', 'dateRange', 'dateFrom', 'dateTo', 'rentalType'));
    }

    /**
     * Store the rental booking request.
     */
    public function store(Request $request, $id)
    {
        $rental = Rental::where('id', $id)->where('status', 'active')->firstOrFail();

        // Validation logic would go here
        $validatedData = $request->validate([
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'nullable|string|max:20',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after:start_date',
            'rental_type' => 'required|in:hourly,daily,once',
            'message' => 'nullable|string|max:1000',
        ]);

        // Create booking logic would go here
        // For now, just redirect back with success message
        return redirect()->back()->with('success', 'Buchungsanfrage wurde erfolgreich gesendet!');
    }

    /**
     * Display vendor profile page
     */
    public function vendorProfile($id)
    {
        $vendor = User::where('id', $id)
            ->where('is_vendor', true)
            ->firstOrFail();

        // Get vendor's rentals
        $rentals = Rental::where('vendor_id', $id)
            ->where('status', 'active')
            ->with(['category', 'location', 'images'])
            ->paginate(12);

        // Get vendor stats
        $stats = [
            'total_rentals' => Rental::where('vendor_id', $vendor->id)->where('status', 'active')->count(),
            'member_since' => $vendor->created_at->format('Y'),
            'last_active' => $vendor->updated_at->diffForHumans(),
            'avg_rating' => Rental::where('vendor_id', $vendor->id)
                ->where('status', 'active')
                ->with('reviews')
                ->get()
                ->flatMap->reviews
                ->avg('rating') ?? 0,
        ];

        return view('inlando.vendor-profile', compact('vendor', 'rentals', 'stats'));
    }
}
