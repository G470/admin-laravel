<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the vendor dashboard.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $vendor = Auth::user();

        // Get vendor's rentals with basic statistics
        $rentals = Rental::where('vendor_id', $vendor->id)
            ->with(['category', 'location'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Calculate dashboard statistics
        $stats = [
            'total_rentals' => Rental::where('vendor_id', $vendor->id)->count(),
            'active_rentals' => Rental::where('vendor_id', $vendor->id)->where('status', 'online')->count(),
            'draft_rentals' => Rental::where('vendor_id', $vendor->id)->where('status', 'draft')->count(),
            'total_bookings' => Booking::whereHas('rental', function ($query) use ($vendor) {
                $query->where('vendor_id', $vendor->id);
            })->count(),
            'pending_bookings' => Booking::whereHas('rental', function ($query) use ($vendor) {
                $query->where('vendor_id', $vendor->id);
            })->where('status', 'pending')->count(),
            'this_month_earnings' => 0, // Placeholder for earnings calculation
            'total_locations' => $vendor->locations()->count(),
            'active_locations' => $vendor->locations()->where('is_active', true)->count(),
        ];

        // Get recent bookings
        $recentBookings = Booking::whereHas('rental', function ($query) use ($vendor) {
            $query->where('vendor_id', $vendor->id);
        })
            ->with(['rental', 'renter'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get rental performance data (views, favorites, etc.)
        $topRentals = Rental::where('vendor_id', $vendor->id)
            ->where('status', 'online')
            ->orderBy('views', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('vendor.dashboard', compact(
            'vendor',
            'rentals',
            'stats',
            'recentBookings',
            'topRentals'
        ));
    }
}
