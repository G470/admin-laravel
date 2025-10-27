<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Booking;

class BookingConfirmation extends Notification
{
    use Queueable;

    protected $booking;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
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
        if ($notifiable->id === $this->booking->user_id) {
            // Notification für den Kunden
            return (new MailMessage)
                ->greeting('Hallo ' . $this->booking->first_name . '!')
                ->subject('Anfragebestätigung - ' . $this->booking->rental->title)
                ->line('Ihre Anfrage wurde erfolgreich übermittelt!')
                ->line('Vielen Dank für Ihre Anfrage. Hier sind die Details:')
                ->line('**Artikel:** ' . $this->booking->rental->title)
                ->line('**Zeitraum:** ' . $this->booking->start_date->format('d.m.Y') . ' bis ' . $this->booking->end_date->format('d.m.Y'))
                ->line('**Gesamtbetrag:** €' . number_format($this->booking->total_amount, 2))
                ->line('**Status:** Ausstehend')
                ->line('')
                ->line('Sie können Ihre Anfrage jederzeit über den folgenden Link verwalten:')
                ->action('Anfrage anzeigen', route('booking.token', $this->booking->booking_token))
                ->line('Der Vermieter wird sich in Kürze bei Ihnen melden.')
                ->salutation('Mit freundlichen Grüßen, Ihr Inlando Team');
        } else {
            // Notification für den Vermieter
            return (new MailMessage)
                ->greeting('Hallo ' . $notifiable->name . '!')
                ->subject('Neue Anfrage - ' . $this->booking->rental->title)
                ->line('Sie haben eine neue Anfrage für Ihren Artikel erhalten!')
                ->line('Sie haben eine neue Anfrage für Ihren Artikel erhalten:')
                ->line('**Artikel:** ' . $this->booking->rental->title)
                ->line('**Kunde:** ' . $this->booking->first_name . ' ' . $this->booking->last_name)
                ->line('**E-Mail:** ' . $this->booking->email)
                ->line('**Zeitraum:** ' . $this->booking->start_date->format('d.m.Y') . ' bis ' . $this->booking->end_date->format('d.m.Y'))
                ->line('**Gesamtbetrag:** €' . number_format($this->booking->total_amount, 2))
                ->line('')
                ->line('Bitte bestätigen oder lehnen Sie die Anfrage ab:')
                ->action('Anfrage verwalten', route('vendor.bookings.show', $this->booking->id))
                ->line('Sie können die Anfrage auch direkt in Ihrem Dashboard verwalten.')
                ->salutation('Mit freundlichen Grüßen, Ihr Inlando Team');
        }
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
            'rental_title' => $this->booking->rental->title,
            'customer_name' => $this->booking->first_name . ' ' . $this->booking->last_name,
            'amount' => $this->booking->total_amount,
            'start_date' => $this->booking->start_date,
            'end_date' => $this->booking->end_date,
            'message' => $notifiable->id === $this->booking->user_id
                ? 'Neue Anfrage erhalten'
                : 'Anfragebestätigung gesendet'
        ];
    }
}
