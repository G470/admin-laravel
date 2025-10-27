<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // General settings
        $generalSettings = [
            [
                'key' => 'site_name',
                'value' => 'Inlando Vermietungsplattform',
                'group' => 'general',
                'type' => 'string',
                'description' => 'Name der Website'
            ],
            [
                'key' => 'site_description',
                'value' => 'Die Plattform für einfache und sichere Vermietung von Objekten aller Art',
                'group' => 'general',
                'type' => 'string',
                'description' => 'Kurze Beschreibung der Website'
            ],
            [
                'key' => 'admin_email',
                'value' => 'admin@inlando.de',
                'group' => 'general',
                'type' => 'email',
                'description' => 'Haupt-Administrator E-Mail'
            ],
            [
                'key' => 'support_email',
                'value' => 'support@inlando.de',
                'group' => 'general',
                'type' => 'email',
                'description' => 'Support-Kontakt E-Mail'
            ],
            [
                'key' => 'contact_email',
                'value' => 'kontakt@inlando.de',
                'group' => 'general',
                'type' => 'email',
                'description' => 'Contact email address'
            ],
            [
                'key' => 'default_language',
                'value' => 'de',
                'group' => 'general',
                'type' => 'string',
                'description' => 'Standard-Sprache der Plattform'
            ],
            [
                'key' => 'date_format',
                'value' => 'DD.MM.YYYY',
                'group' => 'general',
                'type' => 'string',
                'description' => 'Standard-Datumsformat'
            ],
            [
                'key' => 'maintenance_mode',
                'value' => false,
                'group' => 'general',
                'type' => 'boolean',
                'description' => 'Wartungsmodus aktivieren'
            ],
            [
                'key' => 'enable_registration',
                'value' => true,
                'group' => 'general',
                'type' => 'boolean',
                'description' => 'Neue Benutzerregistrierung erlauben'
            ],
            [
                'key' => 'maintenance_message',
                'value' => 'Unsere Website wird derzeit gewartet. Bitte versuchen Sie es später erneut.',
                'group' => 'general',
                'type' => 'textarea',
                'description' => 'Nachricht für Wartungsmodus'
            ]
        ];

        // Payment settings
        $paymentSettings = [
            [
                'key' => 'currency',
                'value' => 'EUR',
                'group' => 'payment',
                'type' => 'string',
                'description' => 'Standard-Währung'
            ],
            [
                'key' => 'tax_rate',
                'value' => 19.0,
                'group' => 'payment',
                'type' => 'float',
                'description' => 'Standard-Steuersatz in Prozent'
            ],
            [
                'key' => 'payment_methods',
                'value' => ['paypal', 'stripe', 'transfer'],
                'group' => 'payment',
                'type' => 'array',
                'description' => 'Verfügbare Zahlungsmethoden'
            ],
            [
                'key' => 'service_fee_percentage',
                'value' => 9.5,
                'group' => 'payment',
                'type' => 'float',
                'description' => 'Servicegebühr für Mieter in Prozent'
            ],
            [
                'key' => 'host_fee_percentage',
                'value' => 3.0,
                'group' => 'payment',
                'type' => 'float',
                'description' => 'Servicegebühr für Vermieter in Prozent'
            ]
        ];

        // Notification settings
        $notificationSettings = [
            [
                'key' => 'notify_admin_new_booking',
                'value' => true,
                'group' => 'notification',
                'type' => 'boolean',
                'description' => 'Admin bei neuer Buchung benachrichtigen'
            ],
            [
                'key' => 'notify_vendor_new_booking',
                'value' => true,
                'group' => 'notification',
                'type' => 'boolean',
                'description' => 'Vermieter bei neuer Buchung benachrichtigen'
            ],
            [
                'key' => 'notify_renter_new_booking',
                'value' => true,
                'group' => 'notification',
                'type' => 'boolean',
                'description' => 'Mieter bei neuer Buchung benachrichtigen'
            ],
            [
                'key' => 'notify_admin_booking_confirm',
                'value' => false,
                'group' => 'notification',
                'type' => 'boolean',
                'description' => 'Admin bei Buchungsbestätigung benachrichtigen'
            ],
            [
                'key' => 'notify_vendor_booking_confirm',
                'value' => true,
                'group' => 'notification',
                'type' => 'boolean',
                'description' => 'Vermieter bei Buchungsbestätigung benachrichtigen'
            ],
            [
                'key' => 'notify_renter_booking_confirm',
                'value' => true,
                'group' => 'notification',
                'type' => 'boolean',
                'description' => 'Mieter bei Buchungsbestätigung benachrichtigen'
            ],
            [
                'key' => 'notify_admin_payment_received',
                'value' => true,
                'group' => 'notification',
                'type' => 'boolean',
                'description' => 'Admin bei Zahlungseingang benachrichtigen'
            ],
            [
                'key' => 'notify_vendor_payment_received',
                'value' => true,
                'group' => 'notification',
                'type' => 'boolean',
                'description' => 'Vermieter bei Zahlungseingang benachrichtigen'
            ],
            [
                'key' => 'notify_renter_payment_received',
                'value' => true,
                'group' => 'notification',
                'type' => 'boolean',
                'description' => 'Mieter bei Zahlungseingang benachrichtigen'
            ]
        ];

        // SEO settings
        $seoSettings = [
            [
                'key' => 'meta_title',
                'value' => 'Inlando | Die Vermietungsplattform für alle Objekte',
                'group' => 'seo',
                'type' => 'string',
                'description' => 'Standard Meta-Titel für SEO'
            ],
            [
                'key' => 'meta_description',
                'value' => 'Finden Sie die perfekte Unterkunft, Veranstaltungsort oder Transportmittel für Ihre Bedürfnisse. Mieten Sie einfach und sicher auf Inlando.',
                'group' => 'seo',
                'type' => 'textarea',
                'description' => 'Standard Meta-Beschreibung für SEO'
            ],
            [
                'key' => 'meta_keywords',
                'value' => 'vermietung, ferienhaus, ferienwohnung, event location, transportfahrzeug, mieten',
                'group' => 'seo',
                'type' => 'string',
                'description' => 'Standard Meta-Keywords (kommagetrennt)'
            ]
        ];

        // API settings
        $apiSettings = [
            [
                'key' => 'enable_api',
                'value' => true,
                'group' => 'api',
                'type' => 'boolean',
                'description' => 'API-Zugriff aktivieren'
            ],
            [
                'key' => 'enable_developer_mode',
                'value' => false,
                'group' => 'api',
                'type' => 'boolean',
                'description' => 'Entwicklermodus mit erweiterten Logs'
            ],
            [
                'key' => 'api_rate_limit',
                'value' => 60,
                'group' => 'api',
                'type' => 'integer',
                'description' => 'API Rate Limit (Requests pro Minute)'
            ]
        ];

        // Homepage settings
        $homepageSettings = [
            [
                'key' => 'hero_title',
                'value' => 'Willkommen bei Inlando',
                'group' => 'homepage',
                'type' => 'string',
                'description' => 'Homepage hero title'
            ],
            [
                'key' => 'hero_subtitle',
                'value' => 'Die Plattform für Vermietungsobjekte',
                'group' => 'homepage',
                'type' => 'string',
                'description' => 'Homepage hero subtitle'
            ],
            [
                'key' => 'hero_button_text',
                'value' => 'Objekte entdecken',
                'group' => 'homepage',
                'type' => 'string',
                'description' => 'Hero button text'
            ],
            [
                'key' => 'hero_button_url',
                'value' => '/rentals',
                'group' => 'homepage',
                'type' => 'string',
                'description' => 'Hero button URL'
            ],
            [
                'key' => 'featured_section_title',
                'value' => 'Ausgewählte Objekte',
                'group' => 'homepage',
                'type' => 'string',
                'description' => 'Featured section title'
            ],
            [
                'key' => 'featured_section_subtitle',
                'value' => 'Entdecken Sie unsere Top-Vermietungsobjekte',
                'group' => 'homepage',
                'type' => 'string',
                'description' => 'Featured section subtitle'
            ],
            [
                'key' => 'testimonial_section_title',
                'value' => 'Kundenmeinungen',
                'group' => 'homepage',
                'type' => 'string',
                'description' => 'Testimonial section title'
            ],
            [
                'key' => 'testimonial_section_subtitle',
                'value' => 'Das sagen unsere Kunden',
                'group' => 'homepage',
                'type' => 'string',
                'description' => 'Testimonial section subtitle'
            ],
            [
                'key' => 'seo_title',
                'value' => 'Inlando - Die Plattform für Vermietungsobjekte',
                'group' => 'homepage',
                'type' => 'string',
                'description' => 'Homepage SEO title'
            ],
            [
                'key' => 'seo_description',
                'value' => 'Inlando bietet eine große Auswahl an Vermietungsobjekten für jeden Bedarf.',
                'group' => 'homepage',
                'type' => 'text',
                'description' => 'Homepage SEO description'
            ],
            [
                'key' => 'seo_keywords',
                'value' => 'inlando, vermietung, vermietungsobjekte, mieten, vermieten',
                'group' => 'homepage',
                'type' => 'string',
                'description' => 'Homepage SEO keywords'
            ]
        ];

        // Insert all settings
        foreach (array_merge(
            $generalSettings, 
            $paymentSettings, 
            $notificationSettings, 
            $seoSettings, 
            $apiSettings, 
            $homepageSettings
        ) as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('Settings seeded successfully with ' . count(array_merge(
            $generalSettings, 
            $paymentSettings, 
            $notificationSettings, 
            $seoSettings, 
            $apiSettings, 
            $homepageSettings
        )) . ' settings!');
    }
}
