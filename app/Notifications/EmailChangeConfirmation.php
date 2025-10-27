<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\EmailChangeToken;

class EmailChangeConfirmation extends Notification
{
    use Queueable;

    protected $emailChangeToken;

    /**
     * Create a new notification instance.
     */
    public function __construct(EmailChangeToken $emailChangeToken)
    {
        $this->emailChangeToken = $emailChangeToken;
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
        $confirmationUrl = route('vendor.email.confirm', $this->emailChangeToken->token);
        $cancelUrl = route('vendor.email.cancel', $this->emailChangeToken->token);

        return (new MailMessage)
            ->greeting('Hallo ' . $notifiable->name . '!')
            ->subject('E-Mail-Adresse ändern - Bestätigung erforderlich')
            ->line('Sie haben eine Änderung Ihrer E-Mail-Adresse beantragt.')
            ->line('**Neue E-Mail-Adresse:** ' . $this->emailChangeToken->new_email)
            ->line('')
            ->line('Um diese Änderung zu bestätigen, klicken Sie bitte auf den folgenden Link:')
            ->action('E-Mail-Adresse bestätigen', $confirmationUrl)
            ->line('')
            ->line('**Wichtig:** Dieser Link ist 24 Stunden gültig.')
            ->line('')
            ->line('Falls Sie diese Änderung nicht beantragt haben, können Sie sie hier abbrechen:')
            ->action('Änderung abbrechen', $cancelUrl)
            ->line('')
            ->line('Aus Sicherheitsgründen wird Ihre E-Mail-Adresse erst nach der Bestätigung geändert.')
            ->salutation('Mit freundlichen Grüßen, Ihr Inlando Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'email_change_token_id' => $this->emailChangeToken->id,
            'new_email' => $this->emailChangeToken->new_email,
            'expires_at' => $this->emailChangeToken->expires_at,
            'message' => 'E-Mail-Änderung bestätigen'
        ];
    }
}