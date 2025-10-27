<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RentalFieldTemplate;
use App\Models\RentalField;
use App\Models\Category;

class RentalFieldTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Creating Rental Field Templates with test data...');

        // 1. VEHICLE TEMPLATE
        $vehicleTemplate = RentalFieldTemplate::create([
            'name' => 'Fahrzeug Details',
            'description' => 'Template für Fahrzeug-Vermietungen mit technischen Details',
            'is_active' => true,
            'sort_order' => 1,
            'settings' => [
                'show_in_search' => true,
                'group_display' => true,
            ],
        ]);

        $vehicleFields = [
            [
                'field_type' => 'select',
                'field_name' => 'fahrzeugmarke',
                'field_label' => 'Fahrzeugmarke',
                'field_description' => 'Marke des Fahrzeugs',
                'options' => [
                    'audi' => 'Audi',
                    'bmw' => 'BMW',
                    'mercedes' => 'Mercedes-Benz',
                    'volkswagen' => 'Volkswagen',
                    'ford' => 'Ford',
                    'opel' => 'Opel',
                    'renault' => 'Renault',
                    'peugeot' => 'Peugeot',
                    'toyota' => 'Toyota',
                    'nissan' => 'Nissan',
                    'andere' => 'Andere'
                ],
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => true,
                'sort_order' => 1,
            ],
            [
                'field_type' => 'number',
                'field_name' => 'baujahr',
                'field_label' => 'Baujahr',
                'field_description' => 'Jahr der Erstzulassung',
                'validation_rules' => [
                    'min_value' => 1990,
                    'max_value' => date('Y') + 1,
                ],
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => false,
                'sort_order' => 2,
            ],
            [
                'field_type' => 'select',
                'field_name' => 'kraftstoff',
                'field_label' => 'Kraftstoffart',
                'field_description' => 'Art des Kraftstoffs',
                'options' => [
                    'benzin' => 'Benzin',
                    'diesel' => 'Diesel',
                    'elektro' => 'Elektro',
                    'hybrid' => 'Hybrid',
                    'erdgas' => 'Erdgas (CNG)',
                    'autogas' => 'Autogas (LPG)',
                ],
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => false,
                'sort_order' => 3,
            ],
            [
                'field_type' => 'radio',
                'field_name' => 'getriebe',
                'field_label' => 'Getriebe',
                'field_description' => 'Art des Getriebes',
                'options' => [
                    'manuell' => 'Schaltgetriebe',
                    'automatik' => 'Automatikgetriebe',
                    'halbautomatik' => 'Halbautomatik',
                ],
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => false,
                'sort_order' => 4,
            ],
            [
                'field_type' => 'number',
                'field_name' => 'sitzplaetze',
                'field_label' => 'Anzahl Sitzplätze',
                'field_description' => 'Maximale Anzahl Personen',
                'validation_rules' => [
                    'min_value' => 1,
                    'max_value' => 50,
                ],
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => false,
                'sort_order' => 5,
            ],
            [
                'field_type' => 'checkbox',
                'field_name' => 'ausstattung',
                'field_label' => 'Sonderausstattung',
                'field_description' => 'Zusätzliche Ausstattungsmerkmale',
                'options' => [
                    'klimaanlage' => 'Klimaanlage',
                    'navigation' => 'Navigationssystem',
                    'bluetooth' => 'Bluetooth',
                    'usb' => 'USB-Anschluss',
                    'aux' => 'AUX-Anschluss',
                    'cd' => 'CD-Player',
                    'lederausstattung' => 'Lederausstattung',
                    'sitzheizung' => 'Sitzheizung',
                    'xenon' => 'Xenon-Licht',
                    'anhängerku' => 'Anhängerkupplung',
                ],
                'is_required' => false,
                'is_filterable' => true,
                'is_searchable' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($vehicleFields as $fieldData) {
            $vehicleTemplate->fields()->create($fieldData);
        }

        // 2. EVENT TEMPLATE
        $eventTemplate = RentalFieldTemplate::create([
            'name' => 'Event & Party Equipment',
            'description' => 'Template für Event- und Party-Equipment mit Veranstaltungsdetails',
            'is_active' => true,
            'sort_order' => 2,
            'settings' => [
                'show_in_search' => true,
                'group_display' => true,
            ],
        ]);

        $eventFields = [
            [
                'field_type' => 'select',
                'field_name' => 'event_typ',
                'field_label' => 'Event-Typ',
                'field_description' => 'Art der Veranstaltung',
                'options' => [
                    'hochzeit' => 'Hochzeit',
                    'geburtstag' => 'Geburtstag',
                    'firmenfeier' => 'Firmenfeier',
                    'konferenz' => 'Konferenz',
                    'messe' => 'Messe',
                    'konzert' => 'Konzert',
                    'sportveranstaltung' => 'Sportveranstaltung',
                    'outdoor' => 'Outdoor-Event',
                    'privat' => 'Private Feier',
                ],
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => true,
                'sort_order' => 1,
            ],
            [
                'field_type' => 'range',
                'field_name' => 'personen_anzahl',
                'field_label' => 'Maximale Personenanzahl',
                'field_description' => 'Für wie viele Personen geeignet',
                'validation_rules' => [
                    'min_value' => 1,
                    'max_value' => 1000,
                    'step' => 10,
                ],
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => false,
                'sort_order' => 2,
            ],
            [
                'field_type' => 'checkbox',
                'field_name' => 'indoor_outdoor',
                'field_label' => 'Verwendung',
                'field_description' => 'Wo kann das Equipment verwendet werden',
                'options' => [
                    'indoor' => 'Innenbereich',
                    'outdoor' => 'Außenbereich',
                    'wettergeschuetzt' => 'Wettergeschützt',
                ],
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => false,
                'sort_order' => 3,
            ],
            [
                'field_type' => 'select',
                'field_name' => 'stromanschluss',
                'field_label' => 'Stromanschluss erforderlich',
                'field_description' => 'Welcher Stromanschluss wird benötigt',
                'options' => [
                    'keiner' => 'Kein Strom erforderlich',
                    '230v' => '230V (normaler Hausanschluss)',
                    '400v' => '400V (Starkstrom)',
                    'generator' => 'Generator erforderlich',
                ],
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => false,
                'sort_order' => 4,
            ],
            [
                'field_type' => 'text',
                'field_name' => 'aufbauzeit',
                'field_label' => 'Aufbauzeit',
                'field_description' => 'Geschätzte Zeit für Auf- und Abbau',
                'validation_rules' => [
                    'max_length' => 100,
                ],
                'is_required' => false,
                'is_filterable' => false,
                'is_searchable' => true,
                'sort_order' => 5,
            ],
            [
                'field_type' => 'checkbox',
                'field_name' => 'zusatzleistungen',
                'field_label' => 'Zusatzleistungen',
                'field_description' => 'Verfügbare Zusatzleistungen',
                'options' => [
                    'aufbau' => 'Aufbau-Service',
                    'abbau' => 'Abbau-Service',
                    'transport' => 'Transport-Service',
                    'bedienung' => 'Bedienung vor Ort',
                    'reinigung' => 'Reinigung nach Event',
                ],
                'is_required' => false,
                'is_filterable' => true,
                'is_searchable' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($eventFields as $fieldData) {
            $eventTemplate->fields()->create($fieldData);
        }

        // 3. TECH EQUIPMENT TEMPLATE
        $techTemplate = RentalFieldTemplate::create([
            'name' => 'Technik & Equipment',
            'description' => 'Template für technische Geräte und Equipment',
            'is_active' => true,
            'sort_order' => 3,
            'settings' => [
                'show_in_search' => true,
                'group_display' => false,
            ],
        ]);

        $techFields = [
            [
                'field_type' => 'select',
                'field_name' => 'geraete_kategorie',
                'field_label' => 'Gerätekategorie',
                'field_description' => 'Art des technischen Geräts',
                'options' => [
                    'audio' => 'Audio-Equipment',
                    'video' => 'Video-Equipment',
                    'beleuchtung' => 'Beleuchtung',
                    'computer' => 'Computer & IT',
                    'kamera' => 'Kamera & Foto',
                    'werkzeug' => 'Werkzeuge',
                    'messgeraet' => 'Messgeräte',
                ],
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => true,
                'sort_order' => 1,
            ],
            [
                'field_type' => 'text',
                'field_name' => 'modell_bezeichnung',
                'field_label' => 'Modellbezeichnung',
                'field_description' => 'Genaue Modellbezeichnung des Geräts',
                'validation_rules' => [
                    'max_length' => 255,
                ],
                'is_required' => false,
                'is_filterable' => false,
                'is_searchable' => true,
                'sort_order' => 2,
            ],
            [
                'field_type' => 'select',
                'field_name' => 'zustand',
                'field_label' => 'Zustand',
                'field_description' => 'Aktueller Zustand des Geräts',
                'options' => [
                    'neu' => 'Neu',
                    'sehr_gut' => 'Sehr gut',
                    'gut' => 'Gut',
                    'gebraucht' => 'Gebraucht',
                ],
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => false,
                'sort_order' => 3,
            ],
            [
                'field_type' => 'date',
                'field_name' => 'anschaffungsdatum',
                'field_label' => 'Anschaffungsdatum',
                'field_description' => 'Wann wurde das Gerät angeschafft',
                'validation_rules' => [
                    'max_date' => date('Y-m-d'),
                ],
                'is_required' => false,
                'is_filterable' => true,
                'is_searchable' => false,
                'sort_order' => 4,
            ],
        ];

        foreach ($techFields as $fieldData) {
            $techTemplate->fields()->create($fieldData);
        }

        // ASSIGN TEMPLATES TO CATEGORIES
        $this->assignTemplatesToCategories($vehicleTemplate, $eventTemplate, $techTemplate);

        $this->command->info('✅ Vehicle Template created with ' . $vehicleTemplate->fields->count() . ' fields');
        $this->command->info('✅ Event Template created with ' . $eventTemplate->fields->count() . ' fields');
        $this->command->info('✅ Tech Template created with ' . $techTemplate->fields->count() . ' fields');
        $this->command->info('🎉 Rental Field Templates seeded successfully!');
    }

    private function assignTemplatesToCategories($vehicleTemplate, $eventTemplate, $techTemplate)
    {
        try {
            // Vehicle categories (based on our analysis)
            $vehicleCategoryIds = [243, 259, 260]; // Fahrzeuge, Boote & Fluggeräte, etc.
            $vehicleCategories = Category::whereIn('id', $vehicleCategoryIds)->get();

            if ($vehicleCategories->count() > 0) {
                $vehicleTemplate->categories()->attach($vehicleCategories->pluck('id'));
                $this->command->info('🚗 Vehicle template assigned to ' . $vehicleCategories->count() . ' categories');
            }

            // Event categories (based on our analysis)
            $eventCategoryIds = [608, 646, 655]; // Party, Messe & Events, etc.
            $eventCategories = Category::whereIn('id', $eventCategoryIds)->get();

            if ($eventCategories->count() > 0) {
                $eventTemplate->categories()->attach($eventCategories->pluck('id'));
                $this->command->info('🎉 Event template assigned to ' . $eventCategories->count() . ' categories');
            }

            // Tech categories (find technology related categories)
            $techCategories = Category::where('name', 'LIKE', '%technik%')
                ->orWhere('name', 'LIKE', '%computer%')
                ->orWhere('name', 'LIKE', '%elektronik%')
                ->orWhere('name', 'LIKE', '%kamera%')
                ->orWhere('name', 'LIKE', '%audio%')
                ->take(5)
                ->get();

            if ($techCategories->count() > 0) {
                $techTemplate->categories()->attach($techCategories->pluck('id'));
                $this->command->info('🔧 Tech template assigned to ' . $techCategories->count() . ' categories');
            }

        } catch (\Exception $e) {
            $this->command->warn('⚠️  Could not assign categories: ' . $e->getMessage());
        }
    }
}
