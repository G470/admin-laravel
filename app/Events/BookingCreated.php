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

class BookingCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $booking;

    /**
     * Create a new event instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking->load(['rental', 'rental.user', 'renter']);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('vendor.' . $this->booking->rental->vendor_id),
            new PrivateChannel('user.' . $this->booking->renter_id),
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
            'booking_token' => $this->booking->booking_token,
            'rental_title' => $this->booking->rental->title,
            'customer_name' => $this->booking->guest_name ?? $this->booking->renter->name,
            'status' => $this->booking->status,
            'total_price' => $this->booking->total_price,
            'message' => 'Neue Buchungsanfrage erhalten'
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'booking.created';
    }
}
