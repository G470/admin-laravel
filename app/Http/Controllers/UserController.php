<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\Rental;
use App\Models\Review;

class UserController extends Controller
{
    /**
     * Show the user dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $recentBookings = Booking::where('renter_id', $user->id)
            ->latest()
            ->take(5)
            ->with('rental')
            ->get();
        
        $totalBookings = Booking::where('renter_id', $user->id)->count();
        $activeBookings = Booking::where('renter_id', $user->id)
            ->where('status', 'active')
            ->count();

        return view('user.dashboard', compact('user', 'recentBookings', 'totalBookings', 'activeBookings'));
    }

    /**
     * Show user profile
     */
    public function profile()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:100',
        ]);

        $user->update($request->only([
            'name', 'email', 'phone', 'address', 'city', 'postal_code', 'country'
        ]));

        return redirect()->route('user.profile')->with('success', 'Profil erfolgreich aktualisiert!');
    }

    /**
     * Show user bookings
     */
    public function bookings(Request $request)
    {
        $user = Auth::user();
        $status = $request->get('status', 'all');
        
        $query = Booking::where('renter_id', $user->id)->with(['rental', 'rental.user']);
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $bookings = $query->latest()->paginate(10);

        return view('user.bookings', compact('bookings', 'status'));
    }

    /**
     * Show specific booking details
     */
    public function bookingDetails($id)
    {
        $user = Auth::user();
        $booking = Booking::where('renter_id', $user->id)
            ->where('id', $id)
            ->with(['rental', 'rental.user'])
            ->firstOrFail();

        return view('user.booking-details', compact('booking'));
    }

    /**
     * Show user reviews (reviews they've written)
     */
    public function reviews()
    {
        $user = Auth::user();
        $reviews = Review::where('user_id', $user->id)
            ->with(['rental', 'rental.user'])
            ->latest()
            ->paginate(10);

        return view('user.reviews', compact('reviews'));
    }

    /**
     * Show user favorites
     */
    public function favorites()
    {
        $user = Auth::user();
        // Assuming we'll add a favorites relationship later
        $favorites = collect(); // Placeholder for now

        return view('user.favorites', compact('favorites'));
    }

    /**
     * Show user settings
     */
    public function settings()
    {
        $user = Auth::user();
        return view('user.settings', compact('user'));
    }

    /**
     * Update user settings
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'notifications_email' => 'boolean',
            'notifications_sms' => 'boolean',
            'language' => 'string|in:de,en',
            'timezone' => 'string',
        ]);

        // Update user settings (assuming we'll add these fields later)
        // For now, just redirect back with success message
        
        return redirect()->route('user.settings')->with('success', 'Einstellungen erfolgreich aktualisiert!');
    }
}
