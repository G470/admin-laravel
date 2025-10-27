<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Booking;
use App\Events\BookingStatusChanged;
use App\Models\Rental;
use App\Models\Location;


class BookingController extends Controller
{
    /**
     * Show all bookings for the vendor
     */
    public function index(Request $request)
    {
        $vendor = Auth::user();
        $status = $request->get('status', 'all');

       /* $query = Booking::whereHas('rental', function ($q) use ($vendor) {
            $q->where('vendor_id', $vendor->id);
        })->with(['rental', 'renter'])->withCount('messages');
*/
// modify query to include rental images
        $query = Booking::whereHas('rental', function ($q) use ($vendor) {
            $q->where('vendor_id', $vendor->id);
        })->with(['rental', 'renter', 'rental.images'])->withCount('messages');




        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $bookings = $query->latest()->paginate(10);
        // Get statistics
        $stats = [
            'total' => Booking::whereHas('rental', function ($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id);
            })->count(),
            'pending' => Booking::whereHas('rental', function ($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id);
            })->where('status', 'pending')->count(),
            'confirmed' => Booking::whereHas('rental', function ($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id);
            })->where('status', 'confirmed')->count(),
            'completed' => Booking::whereHas('rental', function ($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id);
            })->where('status', 'completed')->count(),
            'total_revenue' => Booking::whereHas('rental', function ($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id);
            })->whereIn('status', ['confirmed', 'completed'])->sum('total_price'),
        ];

        // Add individual variables for view compatibility
        $totalBookings = $stats['total'];
        $pendingBookings = $stats['pending'];
        $confirmedBookings = $stats['confirmed'];
        $completedBookings = $stats['completed'];

        return view('vendor.bookings.index', compact(
            'bookings',
            'status',
            'stats',
            'totalBookings',
            'pendingBookings',
            'confirmedBookings',
            'completedBookings'
        ));
    }

    /**
     * Show booking details for vendor
     */
    public function show($id)
    {
        $vendor = Auth::user();

        $booking = Booking::whereHas('rental', function ($query) use ($vendor) {
            $query->where('vendor_id', $vendor->id);
        })->with(['rental', 'renter', 'messages.user'])->findOrFail($id);

        return view('vendor.bookings.show', compact('booking'));
    }

    /**
     * Mark booking as completed
     */
    public function complete($id)
    {
        $vendor = Auth::user();

        $booking = Booking::whereHas('rental', function ($query) use ($vendor) {
            $query->where('vendor_id', $vendor->id);
        })->findOrFail($id);

        if ($booking->status !== 'confirmed') {
            return back()->with('error', 'Nur bestätigte Buchungen können als abgeschlossen markiert werden.');
        }

        try {
            $oldStatus = $booking->status;
            $booking->update(['status' => 'completed']);

            // Fire status changed event
            event(new BookingStatusChanged($booking, $oldStatus, 'completed'));

            return back()->with('success', 'Buchung als abgeschlossen markiert!');

        } catch (\Exception $e) {
            Log::error('Booking completion failed: ' . $e->getMessage());
            return back()->with('error', 'Fehler beim Abschließen der Buchung.');
        }
    }

    /**
     * Add vendor notes to booking
     */
    public function addNotes(Request $request, $id)
    {
        $vendor = Auth::user();

        $request->validate([
            'vendor_notes' => 'required|string|max:1000'
        ]);

        $booking = Booking::whereHas('rental', function ($query) use ($vendor) {
            $query->where('vendor_id', $vendor->id);
        })->findOrFail($id);

        try {
            $booking->update(['vendor_notes' => $request->vendor_notes]);

            return back()->with('success', 'Notizen erfolgreich gespeichert!');

        } catch (\Exception $e) {
            Log::error('Failed to save vendor notes: ' . $e->getMessage());
            return back()->with('error', 'Fehler beim Speichern der Notizen.');
        }
    }
}
