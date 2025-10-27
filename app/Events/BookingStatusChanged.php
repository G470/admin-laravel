<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking;

class BookingStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $booking;
    public $oldStatus;
    public $newStatus;

    /**
     * Create a new event instance.
     */
    public function __construct(Booking $booking, $oldStatus = null, $newStatus = null)
    {
        $this->booking = $booking;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus ?? $booking->status;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->booking->user_id),
            new PrivateChannel('vendor.' . $this->booking->rental->vendor_id),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'booking_id' => $this->booking->id,
            'rental_title' => $this->booking->rental->title,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'customer_name' => $this->booking->first_name . ' ' . $this->booking->last_name,
            'vendor_name' => $this->booking->rental->user->name ?? '',
            'amount' => $this->booking->total_amount,
            'message' => $this->getStatusMessage($this->newStatus),
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'booking.status.changed';
    }

    /**
     * Get status-specific message
     */
    private function getStatusMessage($status)
    {
        return match ($status) {
            'confirmed' => 'Anfrage wurde bestätigt',
            'cancelled' => 'Anfrage wurde storniert',
            'completed' => 'Anfrage wurde abgeschlossen',
            default => 'Anfragestatus wurde geändert'
        };
    }
}
