<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class SmtpService
{
    /**
     * Configure mail settings using admin SMTP configuration
     */
    public static function configureMailSettings()
    {
        $smtpSettings = self::getSmtpSettings();

        // Only configure if SMTP settings are available
        if (!empty($smtpSettings['host']) && !empty($smtpSettings['username'])) {
            Config::set([
                'mail.mailers.smtp.host' => $smtpSettings['host'],
                'mail.mailers.smtp.port' => $smtpSettings['port'],
                'mail.mailers.smtp.username' => $smtpSettings['username'],
                'mail.mailers.smtp.password' => $smtpSettings['password'],
                'mail.mailers.smtp.encryption' => $smtpSettings['encryption'],
                'mail.from.address' => $smtpSettings['from_address'] ?: $smtpSettings['username'],
                'mail.from.name' => $smtpSettings['from_name'] ?: config('app.name'),
            ]);
        }
    }

    /**
     * Get SMTP settings from admin configuration
     */
    public static function getSmtpSettings(): array
    {
        return [
            'host' => Setting::get('smtp_host'),
            'port' => (int) Setting::get('smtp_port', 587),
            'username' => Setting::get('smtp_username'),
            'password' => Setting::get('smtp_password'),
            'encryption' => Setting::get('smtp_encryption', 'tls'),
            'from_address' => Setting::get('smtp_from_address'),
            'from_name' => Setting::get('smtp_from_name'),
        ];
    }

    /**
     * Check if SMTP is properly configured
     */
    public static function isConfigured(): bool
    {
        $settings = self::getSmtpSettings();
        return !empty($settings['host']) && !empty($settings['username']);
    }

    /**
     * Send email with configured SMTP settings
     */
    public static function send($to, $subject, $content, $from = null, $fromName = null)
    {
        self::configureMailSettings();

        $smtpSettings = self::getSmtpSettings();
        $fromAddress = $from ?: $smtpSettings['from_address'] ?: $smtpSettings['username'];
        $fromName = $fromName ?: $smtpSettings['from_name'] ?: config('app.name');

        return Mail::raw($content, function ($message) use ($to, $subject, $fromAddress, $fromName) {
            $message->to($to)
                ->subject($subject)
                ->from($fromAddress, $fromName);
        });
    }
}