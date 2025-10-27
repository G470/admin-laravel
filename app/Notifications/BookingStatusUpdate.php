<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Booking;

class BookingStatusUpdate extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;
    protected $oldStatus;
    protected $newStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking, string $oldStatus, string $newStatus)
    {
        $this->booking = $booking->load(['rental', 'rental.user', 'renter']);
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $customerName = $this->booking->guest_name ?? $this->booking->renter->name ?? 'Kunde';
        $statusMessage = $this->getStatusMessage();
        $actionText = $this->getActionText();

        return (new MailMessage)
            ->subject('Anfragestatus geändert - ' . $this->booking->rental->title)
            ->greeting('Hallo ' . $customerName . '!')
            ->line($statusMessage)
            ->line('')
            ->line('**Anfragedetails:**')
            ->line('• Artikel: ' . $this->booking->rental->title)
            ->line('• Anfragenummer: #' . $this->booking->id)
            ->line('• Zeitraum: ' . $this->booking->start_date->format('d.m.Y') . ' - ' . $this->booking->end_date->format('d.m.Y'))
            ->line('• Gesamtbetrag: €' . number_format($this->booking->total_price, 2, ',', '.'))
            ->line('• Neuer Status: ' . $this->booking->status_label)
            ->line('')
            ->when($this->newStatus === 'confirmed', function ($mail) {
                return $mail->line('**Anbieter Kontaktdaten:**')
                    ->line('Name: ' . $this->booking->rental->user->name)
                    ->line('E-Mail: ' . $this->booking->rental->user->email)
                    ->line('')
                    ->line('Bitte kontaktieren Sie den Anbieter für weitere Details zur Abholung/Übergabe.');
            })
            ->when($this->newStatus === 'cancelled', function ($mail) {
                return $mail->line('Falls Sie Fragen zur Stornierung haben, können Sie den Anbieter kontaktieren oder sich an unseren Support wenden.');
            })
            ->action($actionText, route('booking.token', $this->booking->booking_token))
            ->line('Bei Fragen stehen wir Ihnen gerne zur Verfügung.')
            ->salutation('Freundliche Grüße, Ihr Inlando Team');
    }

    /**
     * Get status change message
     */
    private function getStatusMessage(): string
    {
        return match ($this->newStatus) {
            'confirmed' => '🎉 Großartige Neuigkeiten! Ihre Anfrage wurde bestätigt.',
            'cancelled' => '❌ Ihre Anfrage wurde leider storniert.',
            'completed' => '✅ Ihre Anfrage wurde als abgeschlossen markiert.',
            default => 'Der Status Ihrer Anfrage wurde geändert.'
        };
    }

    /**
     * Get action button text
     */
    private function getActionText(): string
    {
        return match ($this->newStatus) {
            'confirmed' => 'Anfragedetails anzeigen',
            'cancelled' => 'Anfrage anzeigen',
            'completed' => 'Anfrage bewerten',
            default => 'Anfrage anzeigen'
        };
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'booking_token' => $this->booking->booking_token,
            'rental_title' => $this->booking->rental->title,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'status_label' => $this->booking->status_label,
        ];
    }
}
