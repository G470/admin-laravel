<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Notification;
use App\Models\Booking;
use App\Models\Rental;
use App\Models\User;
use App\Events\BookingCreated;
use App\Events\BookingStatusChanged;
use App\Notifications\BookingConfirmation;
use App\Notifications\BookingStatusUpdate;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Show all bookings for the authenticated user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $status = $request->get('status', 'all');
        
        $query = Booking::where('renter_id', $user->id)
            ->with(['rental', 'rental.user'])
            ->withCount('messages');
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $bookings = $query->latest()->paginate(10);
        
        // Get recent bookings for dashboard
        $recentBookings = Booking::where('renter_id', $user->id)
            ->with(['rental', 'rental.user'])
            ->latest()
            ->limit(5)
            ->get();
        $totalBookings = Booking::where('renter_id', $user->id)->count();
        $pendingBookings = Booking::where('renter_id', $user->id)
            ->where('status', 'pending')
            ->count();
        $confirmedBookings = Booking::where('renter_id', $user->id)
            ->where('status', 'confirmed')
            ->count();

        return view('user.bookings', compact('bookings', 'status', 'user', 'recentBookings', 'totalBookings', 'pendingBookings', 'confirmedBookings'));
    }

    /**
     * Show booking details (with token support for guest access)
     */
    public function show($identifier)
    {
        $user = Auth::user();
        
        // Check if identifier is a token (32 characters) or ID
        if (strlen($identifier) === 32) {
            $booking = Booking::where('booking_token', $identifier)
                ->with(['rental', 'rental.user', 'renter', 'messages.user'])
                ->firstOrFail();
                
            // Set guest email in session for chat access
            if ($booking->guest_email) {
                session(['guest_booking_email' => $booking->guest_email]);
            }
        } else {
            if (!$user) {
                abort(403, 'Unauthorized access');
            }
            
            $booking = Booking::where('renter_id', $user->id)
                ->where('id', $identifier)
                ->with(['rental', 'rental.user', 'messages.user'])
                ->firstOrFail();
        }

        return view('bookings.show', compact('booking'));
    }

    /**
     * Show booking details by token (for guest access)
     */
    public function showByToken($token)
    {
        $booking = Booking::where('booking_token', $token)
            ->with(['rental', 'rental.user', 'renter', 'messages.user'])
            ->firstOrFail();

        // Set guest email in session for chat access
        if ($booking->guest_email) {
            session(['guest_booking_email' => $booking->guest_email]);
        }

        return view('bookings.show', compact('booking'));
    }

    /**
     * Create a new booking
     */
    public function create(Request $request)
    {
        $rental = Rental::findOrFail($request->get('rental_id'));
        
        return view('bookings.create', compact('rental'));
    }

    /**
     * Store a new booking
     */
    public function store(Request $request)
    {
        $validation = $request->validate([
            'rental_id' => 'required|exists:rentals,id',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after:start_date',
            'rental_type' => 'required|in:hourly,daily,once',
            'message' => 'nullable|string|max:1000',
            'guest_name' => 'required_without:renter_id|string|max:255',
            'guest_email' => 'required_without:renter_id|email|max:255',
            'guest_phone' => 'nullable|string|max:20',
        ]);

        $user = Auth::user();
        $rental = Rental::findOrFail($request->rental_id);
        
        try {
            DB::beginTransaction();
            
            // Calculate total price
            $totalPrice = $this->calculatePrice($rental, $request);
            
            // Create user account if not logged in
            if (!$user && $request->guest_email) {
                $user = $this->createGuestUser($request);
            }

            $booking = Booking::create([
                'rental_id' => $request->rental_id,
                'renter_id' => $user->id ?? null,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'rental_type' => $request->rental_type,
                'total_price' => $totalPrice,
                'status' => 'pending',
                'message' => $request->message,
                'guest_email' => $request->guest_email ?? $user->email ?? null,
                'guest_name' => $request->guest_name ?? $user->name ?? null,
                'guest_phone' => $request->guest_phone,
            ]);

            // Fire booking created event
            event(new BookingCreated($booking));
            
            // Send notifications
            $this->sendBookingNotifications($booking);
            
            DB::commit();
            
            // Redirect to booking details using token
            return redirect()->route('booking.token', $booking->booking_token)
                ->with('success', 'Buchungsanfrage erfolgreich gesendet! Sie erhalten eine Bestätigungs-E-Mail.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking creation failed: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', 'Fehler beim Erstellen der Buchung. Bitte versuchen Sie es erneut.');
        }
    }

    /**
     * Cancel a booking
     */
    public function cancel($identifier)
    {
        $booking = $this->findBookingByIdentifier($identifier);
        
        if (!$booking->canBeCancelled()) {
            return back()->with('error', 'Diese Buchung kann nicht storniert werden.');
        }

        try {
            $oldStatus = $booking->status;
            $booking->update(['status' => 'cancelled']);
            
            // Fire status changed event
            event(new BookingStatusChanged($booking, $oldStatus, 'cancelled'));
            
            return back()->with('success', 'Buchung erfolgreich storniert!');
            
        } catch (\Exception $e) {
            Log::error('Booking cancellation failed: ' . $e->getMessage());
            return back()->with('error', 'Fehler beim Stornieren der Buchung.');
        }
    }

    /**
     * Show booking confirmation page for vendors
     */
    public function confirmPage($id)
    {
        $user = Auth::user();
        
        if (!$user->is_vendor) {
            abort(403, 'Only vendors can access this page');
        }
        
        $booking = Booking::whereHas('rental', function($query) use ($user) {
            $query->where('vendor_id', $user->id);
        })->with(['rental', 'renter'])->findOrFail($id);
        
        return view('vendor.bookings.confirm', compact('booking'));
    }

    /**
     * Vendor confirms booking
     */
    public function confirm($id)
    {
        $user = Auth::user();
        
        if (!$user->is_vendor) {
            abort(403, 'Only vendors can confirm bookings');
        }
        
        $booking = Booking::whereHas('rental', function($query) use ($user) {
            $query->where('vendor_id', $user->id);
        })->findOrFail($id);
        
        if (!$booking->canBeConfirmed()) {
            return back()->with('error', 'Diese Buchung kann nicht bestätigt werden.');
        }

        try {
            $oldStatus = $booking->status;
            $booking->update(['status' => 'confirmed']);
            
            // Fire status changed event
            event(new BookingStatusChanged($booking, $oldStatus, 'confirmed'));
            
            return back()->with('success', 'Buchung erfolgreich bestätigt!');
            
        } catch (\Exception $e) {
            Log::error('Booking confirmation failed: ' . $e->getMessage());
            return back()->with('error', 'Fehler beim Bestätigen der Buchung.');
        }
    }

    /**
     * Vendor rejects booking
     */
    public function reject($id)
    {
        $user = Auth::user();
        
        if (!$user->is_vendor) {
            abort(403, 'Only vendors can reject bookings');
        }
        
        $booking = Booking::whereHas('rental', function($query) use ($user) {
            $query->where('vendor_id', $user->id);
        })->findOrFail($id);
        
        if (!$booking->canBeConfirmed()) {
            return back()->with('error', 'Diese Buchung kann nicht abgelehnt werden.');
        }

        try {
            $oldStatus = $booking->status;
            $booking->update(['status' => 'cancelled']);
            
            // Fire status changed event
            event(new BookingStatusChanged($booking, $oldStatus, 'cancelled'));
            
            return back()->with('success', 'Buchung abgelehnt.');
            
        } catch (\Exception $e) {
            Log::error('Booking rejection failed: ' . $e->getMessage());
            return back()->with('error', 'Fehler beim Ablehnen der Buchung.');
        }
    }

    /**
     * Calculate booking price based on rental type and dates
     */
    private function calculatePrice($rental, $request)
    {
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $days = $startDate->diffInDays($endDate) + 1; // Include both start and end day
        
        return match($request->rental_type) {
            'hourly' => ($rental->price_range_hour ?? 0) * 8 * $days, // Assume 8 hours per day
            'daily' => ($rental->price_range_day ?? 0) * $days,
            'once' => $rental->price_range_once ?? 0,
            default => 0
        };
    }

    /**
     * Create guest user account
     */
    private function createGuestUser($request)
    {
        return User::create([
            'name' => $request->guest_name,
            'email' => $request->guest_email,
            'password' => bcrypt(Str::random(16)), // Random password
            'email_verified_at' => now(), // Auto-verify for booking users
        ]);
    }

    /**
     * Send booking notifications
     */
    private function sendBookingNotifications($booking)
    {
        try {
            // Send confirmation email to customer
            if ($booking->guest_email) {
                Notification::route('mail', $booking->guest_email)
                    ->notify(new BookingConfirmation($booking));
            }
            
            // Send notification to vendor
            $vendor = $booking->rental->user;
            if ($vendor && $vendor->email) {
                $vendor->notify(new BookingConfirmation($booking, true));
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to send booking notifications: ' . $e->getMessage());
        }
    }

    /**
     * Find booking by ID or token
     */
    private function findBookingByIdentifier($identifier)
    {
        if (strlen($identifier) === 32) {
            return Booking::where('booking_token', $identifier)->firstOrFail();
        }
        
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Unauthorized access');
        }
        
        return Booking::where('renter_id', $user->id)
            ->where('id', $identifier)
            ->firstOrFail();
    }
}
