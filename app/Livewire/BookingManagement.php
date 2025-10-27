<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Booking;
use App\Models\Rental;
use Illuminate\Support\Facades\Auth;

class BookingManagement extends Component
{
    public $bookings;
    public $selectedStatus = '';
    public $search = '';
    public $showModal = false;
    public $selectedBooking = null;

    public function mount()
    {
        $this->loadBookings();
    }

    public function loadBookings()
    {
        $query = Booking::with(['rental', 'user']);

        if (Auth::user()->is_vendor) {
            // Vendor sieht nur seine eigenen Anfragen
            $query->whereHas('rental', function ($q) {
                $q->where('vendor_id', Auth::user()->id);
            });
        } else {
            // User sieht nur seine eigenen Anfragen
            $query->where('user_id', Auth::user()->id);
        }

        if ($this->selectedStatus) {
            $query->where('status', $this->selectedStatus);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('rental', function ($rental) {
                    $rental->where('title', 'like', '%' . $this->search . '%');
                })
                    ->orWhere('first_name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $this->bookings = $query->orderBy('created_at', 'desc')->get();
    }

    public function confirmBooking($bookingId)
    {
        $booking = Booking::find($bookingId);

        if (!Auth::user()->is_vendor) {
            $this->addError('general', 'Nur Anbieter können Anfragen bestätigen.');
            return;
        }

        if (!$booking || $booking->rental->vendor_id !== Auth::user()->id) {
            $this->addError('general', 'Anfrage nicht gefunden oder Sie haben keine Berechtigung.');
            return;
        }

        if ($booking->status !== 'pending') {
            $this->addError('general', 'Diese Anfrage kann nicht bestätigt werden.');
            return;
        }

        try {
            $booking->update(['status' => 'confirmed']);

            // Event für Benachrichtigungen
            event(new \App\Events\BookingStatusChanged($booking, 'confirmed'));

            session()->flash('message', 'Anfrage erfolgreich bestätigt!');
            $this->loadBookings();
        } catch (\Exception $e) {
            $this->addError('general', 'Fehler beim Bestätigen der Anfrage.');
        }
    }

    public function rejectBooking($bookingId)
    {
        $booking = Booking::find($bookingId);

        if (!Auth::user()->is_vendor) {
            $this->addError('general', 'Nur Anbieter können Anfragen ablehnen.');
            return;
        }

        if (!$booking || $booking->rental->vendor_id !== Auth::user()->id) {
            $this->addError('general', 'Anfrage nicht gefunden oder Sie haben keine Berechtigung.');
            return;
        }

        if ($booking->status !== 'pending') {
            $this->addError('general', 'Diese Anfrage kann nicht abgelehnt werden.');
            return;
        }

        try {
            $booking->update(['status' => 'cancelled']);

            // Event für Benachrichtigungen
            event(new \App\Events\BookingStatusChanged($booking, 'cancelled'));

            session()->flash('message', 'Anfrage abgelehnt.');
            $this->loadBookings();
        } catch (\Exception $e) {
            $this->addError('general', 'Fehler beim Ablehnen der Anfrage.');
        }
    }

    public function cancelBooking($bookingId)
    {
        $booking = Booking::find($bookingId);

        if (!$booking || ($booking->user_id !== Auth::user()->id && $booking->rental->vendor_id !== Auth::user()->id)) {
            $this->addError('general', 'Anfrage nicht gefunden oder Sie haben keine Berechtigung.');
            return;
        }

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            $this->addError('general', 'Diese Anfrage kann nicht storniert werden.');
            return;
        }

        try {
            $booking->update(['status' => 'cancelled']);

            // Event für Benachrichtigungen
            event(new \App\Events\BookingStatusChanged($booking, 'cancelled'));

            session()->flash('message', 'Anfrage erfolgreich storniert!');
            $this->loadBookings();
        } catch (\Exception $e) {
            $this->addError('general', 'Fehler beim Stornieren der Anfrage.');
        }
    }

    public function showBookingDetails($bookingId)
    {
        $this->selectedBooking = Booking::with(['rental', 'user'])->find($bookingId);
        $this->showModal = true;

        // Event für Statistiken
        event(new \App\Events\BookingViewed($this->selectedBooking, [
            'user_id' => Auth::user()->id,
            'message' => 'Neue Anfrage erhalten!'
        ]));
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedBooking = null;

        // Event für Benachrichtigungen
        event(new \App\Events\BookingViewed(null, [
            'user_id' => Auth::user()->id,
            'message' => 'Neue Anfrage!'
        ]));
    }

    public function updatedSelectedStatus()
    {
        $this->loadBookings();
    }

    public function updatedSearch()
    {
        $this->loadBookings();
    }

    public function render()
    {
        return view('livewire.booking-management');
    }
}
