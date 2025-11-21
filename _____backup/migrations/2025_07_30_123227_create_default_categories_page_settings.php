<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add default categories page settings
        $defaultSettings = [
            // Hero section
            ['key' => 'categories_hero_title', 'value' => 'Finde und miete, was du brauchst', 'group' => 'categories_page', 'type' => 'string'],
            ['key' => 'categories_hero_subtitle', 'value' => 'Entdecke verschiedene Kategorien von Artikeln, die du mieten kannst', 'group' => 'categories_page', 'type' => 'string'],

            // Main categories section
            ['key' => 'categories_section_enabled', 'value' => true, 'group' => 'categories_page', 'type' => 'boolean'],
            ['key' => 'categories_section_title', 'value' => 'Kategorien durchsuchen', 'group' => 'categories_page', 'type' => 'string'],
            ['key' => 'categories_section_subtitle', 'value' => 'Entdecke verschiedene Kategorien von Artikeln, die du mieten kannst', 'group' => 'categories_page', 'type' => 'string'],

            // Wohnmobil section
            ['key' => 'wohnmobil_section_enabled', 'value' => true, 'group' => 'categories_page', 'type' => 'boolean'],
            ['key' => 'wohnmobil_section_title', 'value' => 'Wohnmobil entdecken', 'group' => 'categories_page', 'type' => 'string'],
            ['key' => 'wohnmobil_section_subtitle', 'value' => 'Entdecke die Freiheit auf vier Rädern – miete ein Wohnmobil und erlebe deinen perfekten Urlaub. Flexibel, unabhängig und mit allem Komfort, den du brauchst.', 'group' => 'categories_page', 'type' => 'string'],
            ['key' => 'wohnmobil_section_button_text', 'value' => 'Jetzt entdecken', 'group' => 'categories_page', 'type' => 'string'],
            ['key' => 'wohnmobil_section_button_link', 'value' => '#', 'group' => 'categories_page', 'type' => 'string'],

            // Events section
            ['key' => 'events_section_enabled', 'value' => true, 'group' => 'categories_page', 'type' => 'boolean'],
            ['key' => 'events_section_title', 'value' => 'Eventartikel mieten', 'group' => 'categories_page', 'type' => 'string'],
            ['key' => 'events_section_subtitle', 'value' => 'Alles was du für dein nächstes Event benötigst', 'group' => 'categories_page', 'type' => 'string'],
            ['key' => 'events_section_button_text', 'value' => 'Alle Eventartikel anzeigen', 'group' => 'categories_page', 'type' => 'string'],
            ['key' => 'events_section_button_link', 'value' => '/kategorien/events', 'group' => 'categories_page', 'type' => 'string'],

            // Vehicles section
            ['key' => 'vehicles_section_enabled', 'value' => true, 'group' => 'categories_page', 'type' => 'boolean'],
            ['key' => 'vehicles_section_title', 'value' => 'Nutzfahrzeuge & Freizeitfahrzeuge', 'group' => 'categories_page', 'type' => 'string'],
            ['key' => 'vehicles_section_subtitle', 'value' => 'Für Transport, Urlaub und Ausflüge', 'group' => 'categories_page', 'type' => 'string'],
            ['key' => 'vehicles_section_button_text', 'value' => 'Alle Fahrzeuge anzeigen', 'group' => 'categories_page', 'type' => 'string'],
            ['key' => 'vehicles_section_button_link', 'value' => '/kategorien/vehicles', 'group' => 'categories_page', 'type' => 'string'],

            // Construction section
            ['key' => 'construction_section_enabled', 'value' => true, 'group' => 'categories_page', 'type' => 'boolean'],
            ['key' => 'construction_section_title', 'value' => 'Baumaschinen & Bauzubehör', 'group' => 'categories_page', 'type' => 'string'],
            ['key' => 'construction_section_subtitle', 'value' => 'Professionelles Equipment für dein Bauprojekt', 'group' => 'categories_page', 'type' => 'string'],
            ['key' => 'construction_section_button_text', 'value' => 'Alle Baumaschinen anzeigen', 'group' => 'categories_page', 'type' => 'string'],
            ['key' => 'construction_section_button_link', 'value' => '/kategorien/construction', 'group' => 'categories_page', 'type' => 'string'],
        ];

        foreach ($defaultSettings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key'], 'group' => $setting['group']],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                    'description' => 'Categories page setting'
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove categories page settings
        Setting::where('group', 'categories_page')->delete();
    }
};
