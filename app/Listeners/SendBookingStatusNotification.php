<?php

namespace App\Listeners;

use App\Events\BookingStatusChanged;
use App\Notifications\BookingStatusUpdate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class SendBookingStatusNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BookingStatusChanged $event): void
    {
        try {
            // Send notification to customer
            if ($event->booking->guest_email) {
                Notification::route('mail', $event->booking->guest_email)
                    ->notify(new BookingStatusUpdate($event->booking, $event->oldStatus, $event->newStatus));
            } elseif ($event->booking->renter) {
                $event->booking->renter->notify(new BookingStatusUpdate($event->booking, $event->oldStatus, $event->newStatus));
            }

            // Send notification to vendor if status is relevant for them
            if (in_array($event->newStatus, ['cancelled']) && $event->booking->rental->user) {
                $event->booking->rental->user->notify(new BookingStatusUpdate($event->booking, $event->oldStatus, $event->newStatus));
            }

        } catch (\Exception $e) {
            Log::error('Failed to send booking status notification: ' . $e->getMessage());
        }
    }
}
