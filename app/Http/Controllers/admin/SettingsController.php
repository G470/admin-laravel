<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\SmtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('group');

        // Prepare settings with defaults for view
        $settingsData = [
            // General settings
            'site_name' => $this->getSetting($settings, 'general', 'site_name', 'Inlando Vermietungsplattform'),
            'site_description' => $this->getSetting($settings, 'general', 'site_description', 'Die Plattform für einfache und sichere Vermietung von Objekten aller Art'),
            'admin_email' => $this->getSetting($settings, 'general', 'admin_email', 'admin@inlando.de'),
            'support_email' => $this->getSetting($settings, 'general', 'support_email', 'support@inlando.de'),
            'default_language' => $this->getSetting($settings, 'general', 'default_language', 'de'),
            'date_format' => $this->getSetting($settings, 'general', 'date_format', 'DD.MM.YYYY'),
            'maintenance_mode' => (bool) $this->getSetting($settings, 'general', 'maintenance_mode', false),
            'enable_registration' => (bool) $this->getSetting($settings, 'general', 'enable_registration', true),
            'maintenance_message' => $this->getSetting($settings, 'general', 'maintenance_message', 'Unsere Website wird derzeit gewartet. Bitte versuchen Sie es später erneut.'),

            // Payment settings
            'currency' => $this->getSetting($settings, 'payment', 'currency', 'EUR'),
            'tax_rate' => (float) $this->getSetting($settings, 'payment', 'tax_rate', 19),
            'payment_methods' => $this->getSetting($settings, 'payment', 'payment_methods', ['paypal', 'stripe', 'transfer']),
            'service_fee_percentage' => (float) $this->getSetting($settings, 'payment', 'service_fee_percentage', 9.5),
            'host_fee_percentage' => (float) $this->getSetting($settings, 'payment', 'host_fee_percentage', 3),

            // Notification settings
            'notify_admin_new_booking' => (bool) $this->getSetting($settings, 'notification', 'notify_admin_new_booking', true),
            'notify_vendor_new_booking' => (bool) $this->getSetting($settings, 'notification', 'notify_vendor_new_booking', true),
            'notify_renter_new_booking' => (bool) $this->getSetting($settings, 'notification', 'notify_renter_new_booking', true),
            'notify_admin_booking_confirm' => (bool) $this->getSetting($settings, 'notification', 'notify_admin_booking_confirm', false),
            'notify_vendor_booking_confirm' => (bool) $this->getSetting($settings, 'notification', 'notify_vendor_booking_confirm', true),
            'notify_renter_booking_confirm' => (bool) $this->getSetting($settings, 'notification', 'notify_renter_booking_confirm', true),
            'notify_admin_payment_received' => (bool) $this->getSetting($settings, 'notification', 'notify_admin_payment_received', true),
            'notify_vendor_payment_received' => (bool) $this->getSetting($settings, 'notification', 'notify_vendor_payment_received', true),
            'notify_renter_payment_received' => (bool) $this->getSetting($settings, 'notification', 'notify_renter_payment_received', true),

            // SEO settings
            'meta_title' => $this->getSetting($settings, 'seo', 'meta_title', 'Inlando | Die Vermietungsplattform für alle Objekte'),
            'meta_description' => $this->getSetting($settings, 'seo', 'meta_description', 'Finden Sie die perfekte Unterkunft, Veranstaltungsort oder Transportmittel für Ihre Bedürfnisse. Mieten Sie einfach und sicher auf Inlando.'),
            'meta_keywords' => $this->getSetting($settings, 'seo', 'meta_keywords', 'vermietung, ferienhaus, ferienwohnung, event location, transportfahrzeug, mieten'),

            // API settings
            'enable_api' => (bool) $this->getSetting($settings, 'api', 'enable_api', true),
            'enable_developer_mode' => (bool) $this->getSetting($settings, 'api', 'enable_developer_mode', false),
            'api_rate_limit' => (int) $this->getSetting($settings, 'api', 'api_rate_limit', 60),

            // Integration settings
            'google_maps_enabled' => (bool) $this->getSetting($settings, 'integrations', 'google_maps_enabled', false),
            'google_maps_api_key' => $this->getSetting($settings, 'integrations', 'google_maps_api_key', ''),
            'openstreetmap_enabled' => (bool) $this->getSetting($settings, 'integrations', 'openstreetmap_enabled', false),
            'openstreetmap_api_key' => $this->getSetting($settings, 'integrations', 'openstreetmap_api_key', ''),
            'recaptcha_enabled' => (bool) $this->getSetting($settings, 'integrations', 'recaptcha_enabled', false),
            'recaptcha_site_key' => $this->getSetting($settings, 'integrations', 'recaptcha_site_key', ''),
            'recaptcha_secret_key' => $this->getSetting($settings, 'integrations', 'recaptcha_secret_key', ''),

            // SMTP settings
            'smtp_host' => $this->getSetting($settings, 'smtp', 'smtp_host', ''),
            'smtp_port' => (int) $this->getSetting($settings, 'smtp', 'smtp_port', 587),
            'smtp_username' => $this->getSetting($settings, 'smtp', 'smtp_username', ''),
            'smtp_password' => $this->getSetting($settings, 'smtp', 'smtp_password', ''),
            'smtp_encryption' => $this->getSetting($settings, 'smtp', 'smtp_encryption', 'tls'),
            'smtp_from_address' => $this->getSetting($settings, 'smtp', 'smtp_from_address', ''),
            'smtp_from_name' => $this->getSetting($settings, 'smtp', 'smtp_from_name', ''),
        ];

        return view('content.admin.settings', compact('settingsData'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string|max:500',
            'admin_email' => 'required|email',
            'support_email' => 'required|email',
            'default_language' => 'required|string|in:de,en,fr',
            'date_format' => 'required|string|in:DD.MM.YYYY,MM/DD/YYYY,YYYY-MM-DD',
            'maintenance_mode' => 'boolean',
            'enable_registration' => 'boolean',
            'maintenance_message' => 'nullable|string|max:1000',
            'currency' => 'required|string|in:EUR,USD,GBP,CHF',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'payment_methods' => 'required|array',
            'payment_methods.*' => 'required|string|in:paypal,stripe,transfer',
            'service_fee_percentage' => 'required|numeric|min:0|max:100',
            'host_fee_percentage' => 'required|numeric|min:0|max:100',
            'notify_admin_new_booking' => 'boolean',
            'notify_vendor_new_booking' => 'boolean',
            'notify_renter_new_booking' => 'boolean',
            'notify_admin_booking_confirm' => 'boolean',
            'notify_vendor_booking_confirm' => 'boolean',
            'notify_renter_booking_confirm' => 'boolean',
            'notify_admin_payment_received' => 'boolean',
            'notify_vendor_payment_received' => 'boolean',
            'notify_renter_payment_received' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'enable_api' => 'boolean',
            'enable_developer_mode' => 'boolean',
            'api_rate_limit' => 'required|integer|min:10|max:1000',
            'google_maps_enabled' => 'boolean',
            'google_maps_api_key' => 'nullable|string|max:255',
            'openstreetmap_enabled' => 'boolean',
            'openstreetmap_api_key' => 'nullable|string|max:255',
            'recaptcha_enabled' => 'boolean',
            'recaptcha_site_key' => 'nullable|string|max:255',
            'recaptcha_secret_key' => 'nullable|string|max:255',
            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|integer|min:1|max:65535',
            'smtp_username' => 'nullable|string|max:255',
            'smtp_password' => 'nullable|string|max:255',
            'smtp_encryption' => 'nullable|string|in:tls,ssl',
            'smtp_from_address' => 'nullable|email',
            'smtp_from_name' => 'nullable|string|max:255',
        ]);

        // Group settings for organized storage
        $groupedSettings = [
            'general' => [
                'site_name',
                'site_description',
                'admin_email',
                'support_email',
                'default_language',
                'date_format',
                'maintenance_mode',
                'enable_registration',
                'maintenance_message'
            ],
            'payment' => [
                'currency',
                'tax_rate',
                'payment_methods',
                'service_fee_percentage',
                'host_fee_percentage'
            ],
            'notification' => [
                'notify_admin_new_booking',
                'notify_vendor_new_booking',
                'notify_renter_new_booking',
                'notify_admin_booking_confirm',
                'notify_vendor_booking_confirm',
                'notify_renter_booking_confirm',
                'notify_admin_payment_received',
                'notify_vendor_payment_received',
                'notify_renter_payment_received'
            ],
            'seo' => [
                'meta_title',
                'meta_description',
                'meta_keywords'
            ],
            'api' => [
                'enable_api',
                'enable_developer_mode',
                'api_rate_limit'
            ],
            'integrations' => [
                'google_maps_enabled',
                'google_maps_api_key',
                'openstreetmap_enabled',
                'openstreetmap_api_key',
                'recaptcha_enabled',
                'recaptcha_site_key',
                'recaptcha_secret_key'
            ],
            'smtp' => [
                'smtp_host',
                'smtp_port',
                'smtp_username',
                'smtp_password',
                'smtp_encryption',
                'smtp_from_address',
                'smtp_from_name'
            ]
        ];

        foreach ($validated as $key => $value) {
            // Determine the group for this setting
            $group = 'general'; // default
            foreach ($groupedSettings as $groupName => $keys) {
                if (in_array($key, $keys)) {
                    $group = $groupName;
                    break;
                }
            }

            // Create or update setting with group
            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'group' => $group,
                    'type' => $this->getSettingType($key, $value)
                ]
            );
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Einstellungen wurden erfolgreich gespeichert.');
    }

    public function clearCache()
    {
        \Artisan::call('cache:clear');
        \Artisan::call('config:clear');
        \Artisan::call('view:clear');

        return redirect()->route('admin.settings.index')
            ->with('success', 'Cache wurde erfolgreich gelöscht.');
    }

    public function backup()
    {
        \Artisan::call('backup:run');

        return redirect()->route('admin.settings.index')
            ->with('success', 'Backup wurde erfolgreich erstellt.');
    }

    public function maintenanceMode(Request $request)
    {
        $enabled = $request->boolean('enabled');
        $message = $request->input('message');

        Setting::set('maintenance_mode', $enabled);
        if ($message) {
            Setting::set('maintenance_message', $message);
        }

        if ($enabled) {
            \Artisan::call('down', ['--message' => $message]);
        } else {
            \Artisan::call('up');
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Wartungsmodus wurde erfolgreich ' . ($enabled ? 'aktiviert' : 'deaktiviert') . '.');
    }

    public function testSMTP(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email'
        ]);

        try {
            // Check if SMTP is configured
            if (!SmtpService::isConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => 'SMTP-Einstellungen sind nicht vollständig konfiguriert.'
                ], 400);
            }

            // Send test email using SmtpService
            SmtpService::send(
                $request->test_email,
                'SMTP Test - Inlando Plattform',
                'Dies ist eine Test-E-Mail von der Inlando-Plattform. Die SMTP-Konfiguration funktioniert korrekt.'
            );

            return response()->json([
                'success' => true,
                'message' => 'Test-E-Mail wurde erfolgreich gesendet.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Senden der Test-E-Mail: ' . $e->getMessage()
            ], 500);
        }
    }

    public function debugSMTP()
    {
        $smtpSettings = SmtpService::getSmtpSettings();
        $isConfigured = SmtpService::isConfigured();

        return response()->json([
            'smtp_settings' => $smtpSettings,
            'is_configured' => $isConfigured,
            'all_settings' => Setting::where('group', 'smtp')->get()->toArray()
        ]);
    }

    /**
     * Helper method to get setting value with fallback
     */
    private function getSetting($settings, $group, $key, $default = null)
    {
        $groupSettings = $settings->get($group, collect());
        $setting = $groupSettings->firstWhere('key', $key);

        return $setting ? $setting->value : $default;
    }

    /**
     * Determine setting type based on key and value
     */
    private function getSettingType($key, $value)
    {
        if (is_bool($value)) {
            return 'boolean';
        }

        if (is_numeric($value)) {
            return is_int($value) ? 'integer' : 'float';
        }

        if (is_array($value)) {
            return 'array';
        }

        if (strpos($key, 'email') !== false) {
            return 'email';
        }

        if (in_array($key, ['meta_description', 'maintenance_message'])) {
            return 'textarea';
        }

        return 'string';
    }
}
