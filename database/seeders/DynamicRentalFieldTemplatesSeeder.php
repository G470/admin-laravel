<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RentalFieldTemplate;
use App\Models\RentalField;
use App\Models\Category;

class DynamicRentalFieldTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Creating 10 Dynamic Rental Field Templates...');

        // 1. WOHNUNG & HAUS TEMPLATE
        $this->createWohnungHausTemplate();

        // 2. BÜRO & GESCHÄFTSRÄUME TEMPLATE
        $this->createBuroGeschaeftsraeumeTemplate();

        // 3. VERANSTALTUNGSRÄUME TEMPLATE
        $this->createVeranstaltungsraeumeTemplate();

        // 4. LAGER & PRODUKTION TEMPLATE
        $this->createLagerProduktionTemplate();

        // 5. GASTRONOMIE TEMPLATE
        $this->createGastronomieTemplate();

        // 6. GESUNDHEITSWESEN TEMPLATE
        $this->createGesundheitswesenTemplate();

        // 7. BILDUNGSWESEN TEMPLATE
        $this->createBildungswesenTemplate();

        // 8. SPORT & FITNESS TEMPLATE
        $this->createSportFitnessTemplate();

        // 9. KREATIV & MEDIEN TEMPLATE
        $this->createKreativMedienTemplate();

        // 10. TECHNIK & IT TEMPLATE
        $this->createTechnikItTemplate();

        $this->command->info('🎉 All 10 Dynamic Rental Field Templates created successfully!');
    }

    private function createWohnungHausTemplate()
    {
        $template = RentalFieldTemplate::create([
            'name' => 'Wohnung & Haus',
            'description' => 'Template für Wohnungen und Häuser mit Immobiliendetails',
            'is_active' => true,
            'sort_order' => 1,
            'settings' => ['show_in_search' => true, 'group_display' => true],
        ]);

        $fields = [
            [
                'field_type' => 'select',
                'field_name' => 'immobilientyp',
                'field_label' => 'Immobilientyp',
                'field_description' => 'Art der Immobilie',
                'options' => [
                    'wohnung' => 'Wohnung',
                    'haus' => 'Haus',
                    'penthouse' => 'Penthouse',
                    'loft' => 'Loft',
                    'ferienwohnung' => 'Ferienwohnung',
                    'gartenhaus' => 'Gartenhaus',
                ],
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => true,
                'sort_order' => 1,
            ],
            [
                'field_type' => 'number',
                'field_name' => 'quadratmeter',
                'field_label' => 'Quadratmeter',
                'field_description' => 'Wohnfläche in m²',
                'validation_rules' => ['min_value' => 10, 'max_value' => 1000],
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => false,
                'sort_order' => 2,
            ],
            [
                'field_type' => 'number',
                'field_name' => 'zimmer_anzahl',
                'field_label' => 'Anzahl Zimmer',
                'field_description' => 'Anzahl der Zimmer',
                'validation_rules' => ['min_value' => 1, 'max_value' => 20],
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => false,
                'sort_order' => 3,
            ],
            [
                'field_type' => 'checkbox',
                'field_name' => 'ausstattung',
                'field_label' => 'Ausstattung',
                'field_description' => 'Verfügbare Ausstattung',
                'options' => [
                    'kueche' => 'Küche',
                    'bad' => 'Bad',
                    'wc' => 'WC',
                    'balkon' => 'Balkon',
                    'terrasse' => 'Terrasse',
                    'garten' => 'Garten',
                    'keller' => 'Keller',
                    'dachboden' => 'Dachboden',
                    'garage' => 'Garage',
                    'stellplatz' => 'Stellplatz',
                ],
                'is_required' => false,
                'is_filterable' => true,
                'is_searchable' => true,
                'sort_order' => 4,
            ],
            [
                'field_type' => 'select',
                'field_name' => 'heizung',
                'field_label' => 'Heizung',
                'field_description' => 'Art der Heizung',
                'options' => [
                    'zentralheizung' => 'Zentralheizung',
                    'etagenheizung' => 'Etagenheizung',
                    'fussbodenheizung' => 'Fußbodenheizung',
                    'kamin' => 'Kamin',
                    'ofen' => 'Ofen',
                    'waermepumpe' => 'Wärmepumpe',
                ],
                'is_required' => false,
                'is_filterable' => true,
                'is_searchable' => false,
                'sort_order' => 5,
            ],
        ];

        foreach ($fields as $fieldData) {
            $template->fields()->create($fieldData);
        }

        // Assign to Wohnung/Haus categories
        $categories = Category::where('name', 'LIKE', '%wohnung%')
            ->orWhere('name', 'LIKE', '%haus%')
            ->orWhere('name', 'LIKE', '%immobilie%')
            ->take(5)
            ->get();

        if ($categories->count() > 0) {
            $template->categories()->attach($categories->pluck('id'));
            $this->command->info('🏠 Wohnung & Haus template created with ' . $template->fields->count() . ' fields');
        }
    }

    private function createBuroGeschaeftsraeumeTemplate()
    {
        $template = RentalFieldTemplate::create([
            'name' => 'Büro & Geschäftsräume',
            'description' => 'Template für Büros und Geschäftsräume',
            'is_active' => true,
            'sort_order' => 2,
            'settings' => ['show_in_search' => true, 'group_display' => true],
        ]);

        $fields = [
            [
                'field_type' => 'select',
                'field_name' => 'raumtyp',
                'field_label' => 'Raumtyp',
                'field_description' => 'Art des Geschäftsraums',
                'options' => [
                    'buero' => 'Büro',
                    'bueroflaeche' => 'Bürofläche',
                    'bueroetage' => 'Büroetage',
                    'buerozentrum' => 'Bürozentrum',
                    'coworking' => 'Coworking Space',
                    'ladenlokal' => 'Ladenlokal',
                    'praxis' => 'Praxis',
                    'showroom' => 'Showroom',
                ],
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => true,
                'sort_order' => 1,
            ],
            [
                'field_type' => 'number',
                'field_name' => 'bueroflaeche',
                'field_label' => 'Bürofläche (m²)',
                'field_description' => 'Verfügbare Bürofläche',
                'validation_rules' => ['min_value' => 10, 'max_value' => 2000],
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => false,
                'sort_order' => 2,
            ],
            [
                'field_type' => 'number',
                'field_name' => 'arbeitsplaetze',
                'field_label' => 'Arbeitsplätze',
                'field_description' => 'Anzahl verfügbarer Arbeitsplätze',
                'validation_rules' => ['min_value' => 1, 'max_value' => 100],
                'is_required' => false,
                'is_filterable' => true,
                'is_searchable' => false,
                'sort_order' => 3,
            ],
            [
                'field_type' => 'checkbox',
                'field_name' => 'buero_ausstattung',
                'field_label' => 'Büroausstattung',
                'field_description' => 'Verfügbare Büroausstattung',
                'options' => [
                    'moebel' => 'Möbel',
                    'computer' => 'Computer',
                    'drucker' => 'Drucker',
                    'internet' => 'Internet',
                    'telefon' => 'Telefon',
                    'klimaanlage' => 'Klimaanlage',
                    'kueche' => 'Küche',
                    'parkplatz' => 'Parkplatz',
                    'empfang' => 'Empfang',
                    'konferenzraum' => 'Konferenzraum',
                ],
                'is_required' => false,
                'is_filterable' => true,
                'is_searchable' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($fields as $fieldData) {
            $template->fields()->create($fieldData);
        }

        // Assign to Büro categories
        $categories = Category::where('name', 'LIKE', '%büro%')
            ->orWhere('name', 'LIKE', '%buero%')
            ->orWhere('name', 'LIKE', '%geschäft%')
            ->orWhere('name', 'LIKE', '%office%')
            ->take(5)
            ->get();

        if ($categories->count() > 0) {
            $template->categories()->attach($categories->pluck('id'));
            $this->command->info('🏢 Büro & Geschäftsräume template created with ' . $template->fields->count() . ' fields');
        }
    }

    private function createVeranstaltungsraeumeTemplate()
    {
        $template = RentalFieldTemplate::create([
            'name' => 'Veranstaltungsräume',
            'description' => 'Template für Veranstaltungsräume und Eventlocations',
            'is_active' => true,
            'sort_order' => 3,
            'settings' => ['show_in_search' => true, 'group_display' => true],
        ]);

        $fields = [
            [
                'field_type' => 'select',
                'field_name' => 'veranstaltungstyp',
                'field_label' => 'Veranstaltungstyp',
                'field_description' => 'Geeignet für Veranstaltungstypen',
                'options' => [
                    'hochzeit' => 'Hochzeit',
                    'geburtstag' => 'Geburtstag',
                    'firmenfeier' => 'Firmenfeier',
                    'konferenz' => 'Konferenz',
                    'seminar' => 'Seminar',
                    'messe' => 'Messe',
                    'konzert' => 'Konzert',
                    'ausstellung' => 'Ausstellung',
                    'party' => 'Party',
                    'meeting' => 'Meeting',
                ],
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => true,
                'sort_order' => 1,
            ],
            [
                'field_type' => 'number',
                'field_name' => 'max_personen',
                'field_label' => 'Maximale Personenanzahl',
                'field_description' => 'Maximale Anzahl Personen',
                'validation_rules' => ['min_value' => 10, 'max_value' => 1000],
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => false,
                'sort_order' => 2,
            ],
            [
                'field_type' => 'number',
                'field_name' => 'raumflaeche',
                'field_label' => 'Raumfläche (m²)',
                'field_description' => 'Verfügbare Raumfläche',
                'validation_rules' => ['min_value' => 20, 'max_value' => 2000],
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => false,
                'sort_order' => 3,
            ],
            [
                'field_type' => 'checkbox',
                'field_name' => 'veranstaltungs_ausstattung',
                'field_label' => 'Veranstaltungsausstattung',
                'field_description' => 'Verfügbare Ausstattung',
                'options' => [
                    'buehne' => 'Bühne',
                    'tontechnik' => 'Tontechnik',
                    'lichttechnik' => 'Lichttechnik',
                    'projektor' => 'Projektor',
                    'leinwand' => 'Leinwand',
                    'kueche' => 'Küche',
                    'bar' => 'Bar',
                    'parkplatz' => 'Parkplatz',
                    'garderobe' => 'Garderobe',
                    'klimaanlage' => 'Klimaanlage',
                ],
                'is_required' => false,
                'is_filterable' => true,
                'is_searchable' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($fields as $fieldData) {
            $template->fields()->create($fieldData);
        }

        // Assign to Veranstaltungs categories
        $categories = Category::where('name', 'LIKE', '%veranstaltung%')
            ->orWhere('name', 'LIKE', '%event%')
            ->orWhere('name', 'LIKE', '%party%')
            ->orWhere('name', 'LIKE', '%messe%')
            ->take(5)
            ->get();

        if ($categories->count() > 0) {
            $template->categories()->attach($categories->pluck('id'));
            $this->command->info('🎉 Veranstaltungsräume template created with ' . $template->fields->count() . ' fields');
        }
    }

    private function createLagerProduktionTemplate()
    {
        $template = RentalFieldTemplate::create([
            'name' => 'Lager & Produktion',
            'description' => 'Template für Lagerräume und Produktionsflächen',
            'is_active' => true,
            'sort_order' => 4,
            'settings' => ['show_in_search' => true, 'group_display' => true],
        ]);

        $fields = [
            [
                'field_type' => 'select',
                'field_name' => 'lager_typ',
                'field_label' => 'Lager-/Produktionstyp',
                'field_description' => 'Art der Lager- oder Produktionsfläche',
                'options' => [
                    'lagerhalle' => 'Lagerhalle',
                    'produktionshalle' => 'Produktionshalle',
                    'werkstatt' => 'Werkstatt',
                    'kuehlhaus' => 'Kühlhaus',
                    'trockenlager' => 'Trockenlager',
                    'hochregallager' => 'Hochregallager',
                    'freilager' => 'Freilager',
                    'container' => 'Container',
                ],
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => true,
                'sort_order' => 1,
            ],
            [
                'field_type' => 'number',
                'field_name' => 'lagerflaeche',
                'field_label' => 'Lagerfläche (m²)',
                'field_description' => 'Verfügbare Lagerfläche',
                'validation_rules' => ['min_value' => 10, 'max_value' => 10000],
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => false,
                'sort_order' => 2,
            ],
            [
                'field_type' => 'number',
                'field_name' => 'deckenhöhe',
                'field_label' => 'Deckenhöhe (m)',
                'field_description' => 'Deckenhöhe in Metern',
                'validation_rules' => ['min_value' => 2, 'max_value' => 20],
                'is_required' => false,
                'is_filterable' => true,
                'is_searchable' => false,
                'sort_order' => 3,
            ],
            [
                'field_type' => 'checkbox',
                'field_name' => 'lager_ausstattung',
                'field_label' => 'Lagerausstattung',
                'field_description' => 'Verfügbare Ausstattung',
                'options' => [
                    'rampe' => 'Laderampe',
                    'tor' => 'Tor',
                    'kran' => 'Kran',
                    'heizung' => 'Heizung',
                    'klimaanlage' => 'Klimaanlage',
                    'sicherheit' => 'Sicherheitssystem',
                    'strom' => 'Stromanschluss',
                    'wasser' => 'Wasseranschluss',
                    'parkplatz' => 'Parkplatz',
                    'buero' => 'Büro',
                ],
                'is_required' => false,
                'is_filterable' => true,
                'is_searchable' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($fields as $fieldData) {
            $template->fields()->create($fieldData);
        }

        // Assign to Lager/Produktion categories
        $categories = Category::where('name', 'LIKE', '%lager%')
            ->orWhere('name', 'LIKE', '%produktion%')
            ->orWhere('name', 'LIKE', '%werkstatt%')
            ->orWhere('name', 'LIKE', '%halle%')
            ->take(5)
            ->get();

        if ($categories->count() > 0) {
            $template->categories()->attach($categories->pluck('id'));
            $this->command->info('🏭 Lager & Produktion template created with ' . $template->fields->count() . ' fields');
        }
    }

    private function createGastronomieTemplate()
    {
        $template = RentalFieldTemplate::create([
            'name' => 'Gastronomie',
            'description' => 'Template für Gastronomiebetriebe und Restaurants',
            'is_active' => true,
            'sort_order' => 5,
            'settings' => ['show_in_search' => true, 'group_display' => true],
        ]);

        $fields = [
            [
                'field_type' => 'select',
                'field_name' => 'gastronomie_typ',
                'field_label' => 'Gastronomietyp',
                'field_description' => 'Art des Gastronomiebetriebs',
                'options' => [
                    'restaurant' => 'Restaurant',
                    'cafe' => 'Café',
                    'bar' => 'Bar',
                    'kneipe' => 'Kneipe',
                    'imbiss' => 'Imbiss',
                    'catering' => 'Catering',
                    'bistro' => 'Bistro',
                    'pizzeria' => 'Pizzeria',
                    'döner' => 'Döner',
                    'bäckerei' => 'Bäckerei',
                ],
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => true,
                'sort_order' => 1,
            ],
            [
                'field_type' => 'number',
                'field_name' => 'sitzplaetze',
                'field_label' => 'Sitzplätze',
                'field_description' => 'Anzahl der Sitzplätze',
                'validation_rules' => ['min_value' => 5, 'max_value' => 500],
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => false,
                'sort_order' => 2,
            ],
            [
                'field_type' => 'checkbox',
                'field_name' => 'gastronomie_ausstattung',
                'field_label' => 'Gastronomieausstattung',
                'field_description' => 'Verfügbare Ausstattung',
                'options' => [
                    'kueche' => 'Küche',
                    'bar' => 'Bar',
                    'terrasse' => 'Terrasse',
                    'garten' => 'Garten',
                    'parkplatz' => 'Parkplatz',
                    'klimaanlage' => 'Klimaanlage',
                    'heizung' => 'Heizung',
                    'toiletten' => 'Toiletten',
                    'kasse' => 'Kasse',
                    'lager' => 'Lager',
                ],
                'is_required' => false,
                'is_filterable' => true,
                'is_searchable' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($fields as $fieldData) {
            $template->fields()->create($fieldData);
        }

        // Assign to Gastronomie categories
        $categories = Category::where('name', 'LIKE', '%gastronomie%')
            ->orWhere('name', 'LIKE', '%restaurant%')
            ->orWhere('name', 'LIKE', '%cafe%')
            ->orWhere('name', 'LIKE', '%bar%')
            ->take(5)
            ->get();

        if ($categories->count() > 0) {
            $template->categories()->attach($categories->pluck('id'));
            $this->command->info('🍽️ Gastronomie template created with ' . $template->fields->count() . ' fields');
        }
    }

    private function createGesundheitswesenTemplate()
    {
        $template = RentalFieldTemplate::create([
            'name' => 'Gesundheitswesen',
            'description' => 'Template für Praxen und Gesundheitsräume',
            'is_active' => true,
            'sort_order' => 6,
            'settings' => ['show_in_search' => true, 'group_display' => true],
        ]);

        $fields = [
            [
                'field_type' => 'select',
                'field_name' => 'gesundheit_typ',
                'field_label' => 'Gesundheitstyp',
                'field_description' => 'Art der Gesundheitspraxis',
                'options' => [
                    'arztpraxis' => 'Arztpraxis',
                    'zahnarzt' => 'Zahnarztpraxis',
                    'physiotherapie' => 'Physiotherapie',
                    'massage' => 'Massagepraxis',
                    'psychologie' => 'Psychologiepraxis',
                    'heilpraktiker' => 'Heilpraktiker',
                    'apotheke' => 'Apotheke',
                    'optiker' => 'Optiker',
                    'krankengymnastik' => 'Krankengymnastik',
                    'ergotherapie' => 'Ergotherapie',
                ],
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => true,
                'sort_order' => 1,
            ],
            [
                'field_type' => 'number',
                'field_name' => 'behandlungsraeume',
                'field_label' => 'Behandlungsräume',
                'field_description' => 'Anzahl der Behandlungsräume',
                'validation_rules' => ['min_value' => 1, 'max_value' => 20],
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => false,
                'sort_order' => 2,
            ],
            [
                'field_type' => 'checkbox',
                'field_name' => 'gesundheit_ausstattung',
                'field_label' => 'Gesundheitsausstattung',
                'field_description' => 'Verfügbare Ausstattung',
                'options' => [
                    'wartezimmer' => 'Wartezimmer',
                    'behandlungsraum' => 'Behandlungsraum',
                    'buero' => 'Büro',
                    'lager' => 'Lager',
                    'toilette' => 'Toilette',
                    'parkplatz' => 'Parkplatz',
                    'aufzug' => 'Aufzug',
                    'klimaanlage' => 'Klimaanlage',
                    'heizung' => 'Heizung',
                    'sicherheit' => 'Sicherheitssystem',
                ],
                'is_required' => false,
                'is_filterable' => true,
                'is_searchable' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($fields as $fieldData) {
            $template->fields()->create($fieldData);
        }

        // Assign to Gesundheitswesen categories
        $categories = Category::where('name', 'LIKE', '%gesundheit%')
            ->orWhere('name', 'LIKE', '%arzt%')
            ->orWhere('name', 'LIKE', '%praxis%')
            ->orWhere('name', 'LIKE', '%medizin%')
            ->take(5)
            ->get();

        if ($categories->count() > 0) {
            $template->categories()->attach($categories->pluck('id'));
            $this->command->info('🏥 Gesundheitswesen template created with ' . $template->fields->count() . ' fields');
        }
    }

    private function createBildungswesenTemplate()
    {
        $template = RentalFieldTemplate::create([
            'name' => 'Bildungswesen',
            'description' => 'Template für Bildungsräume und Schulungsräume',
            'is_active' => true,
            'sort_order' => 7,
            'settings' => ['show_in_search' => true, 'group_display' => true],
        ]);

        $fields = [
            [
                'field_type' => 'select',
                'field_name' => 'bildung_typ',
                'field_label' => 'Bildungstyp',
                'field_description' => 'Art der Bildungseinrichtung',
                'options' => [
                    'schulungsraum' => 'Schulungsraum',
                    'seminarraum' => 'Seminarraum',
                    'klassenzimmer' => 'Klassenzimmer',
                    'hochschulraum' => 'Hochschulraum',
                    'kindergarten' => 'Kindergarten',
                    'sprachschule' => 'Sprachschule',
                    'musikschule' => 'Musikschule',
                    'sportstudio' => 'Sportstudio',
                    'bibliothek' => 'Bibliothek',
                    'labor' => 'Labor',
                ],
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => true,
                'sort_order' => 1,
            ],
            [
                'field_type' => 'number',
                'field_name' => 'schueler_anzahl',
                'field_label' => 'Schüleranzahl',
                'field_description' => 'Maximale Anzahl Schüler/Teilnehmer',
                'validation_rules' => ['min_value' => 5, 'max_value' => 100],
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => false,
                'sort_order' => 2,
            ],
            [
                'field_type' => 'checkbox',
                'field_name' => 'bildung_ausstattung',
                'field_label' => 'Bildungsausstattung',
                'field_description' => 'Verfügbare Ausstattung',
                'options' => [
                    'projektor' => 'Projektor',
                    'leinwand' => 'Leinwand',
                    'whiteboard' => 'Whiteboard',
                    'computer' => 'Computer',
                    'internet' => 'Internet',
                    'klimaanlage' => 'Klimaanlage',
                    'heizung' => 'Heizung',
                    'toiletten' => 'Toiletten',
                    'parkplatz' => 'Parkplatz',
                    'kueche' => 'Küche',
                ],
                'is_required' => false,
                'is_filterable' => true,
                'is_searchable' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($fields as $fieldData) {
            $template->fields()->create($fieldData);
        }

        // Assign to Bildungswesen categories
        $categories = Category::where('name', 'LIKE', '%bildung%')
            ->orWhere('name', 'LIKE', '%schule%')
            ->orWhere('name', 'LIKE', '%seminar%')
            ->orWhere('name', 'LIKE', '%schulung%')
            ->take(5)
            ->get();

        if ($categories->count() > 0) {
            $template->categories()->attach($categories->pluck('id'));
            $this->command->info('📚 Bildungswesen template created with ' . $template->fields->count() . ' fields');
        }
    }

    private function createSportFitnessTemplate()
    {
        $template = RentalFieldTemplate::create([
            'name' => 'Sport & Fitness',
            'description' => 'Template für Sport- und Fitnessräume',
            'is_active' => true,
            'sort_order' => 8,
            'settings' => ['show_in_search' => true, 'group_display' => true],
        ]);

        $fields = [
            [
                'field_type' => 'select',
                'field_name' => 'sport_typ',
                'field_label' => 'Sporttyp',
                'field_description' => 'Art der Sporteinrichtung',
                'options' => [
                    'fitnessstudio' => 'Fitnessstudio',
                    'yoga' => 'Yoga-Studio',
                    'pilates' => 'Pilates-Studio',
                    'tanzen' => 'Tanzstudio',
                    'kampfsport' => 'Kampfsport',
                    'schwimmen' => 'Schwimmbad',
                    'tennis' => 'Tennisplatz',
                    'fußball' => 'Fußballplatz',
                    'basketball' => 'Basketballplatz',
                    'klettern' => 'Kletterhalle',
                ],
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => true,
                'sort_order' => 1,
            ],
            [
                'field_type' => 'number',
                'field_name' => 'sportflaeche',
                'field_label' => 'Sportfläche (m²)',
                'field_description' => 'Verfügbare Sportfläche',
                'validation_rules' => ['min_value' => 20, 'max_value' => 2000],
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => false,
                'sort_order' => 2,
            ],
            [
                'field_type' => 'checkbox',
                'field_name' => 'sport_ausstattung',
                'field_label' => 'Sportausstattung',
                'field_description' => 'Verfügbare Ausstattung',
                'options' => [
                    'geraete' => 'Sportgeräte',
                    'matten' => 'Matten',
                    'spiegel' => 'Spiegel',
                    'klimaanlage' => 'Klimaanlage',
                    'heizung' => 'Heizung',
                    'duschen' => 'Duschen',
                    'umkleiden' => 'Umkleiden',
                    'parkplatz' => 'Parkplatz',
                    'kueche' => 'Küche',
                    'buero' => 'Büro',
                ],
                'is_required' => false,
                'is_filterable' => true,
                'is_searchable' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($fields as $fieldData) {
            $template->fields()->create($fieldData);
        }

        // Assign to Sport/Fitness categories
        $categories = Category::where('name', 'LIKE', '%sport%')
            ->orWhere('name', 'LIKE', '%fitness%')
            ->orWhere('name', 'LIKE', '%yoga%')
            ->orWhere('name', 'LIKE', '%tanzen%')
            ->take(5)
            ->get();

        if ($categories->count() > 0) {
            $template->categories()->attach($categories->pluck('id'));
            $this->command->info('💪 Sport & Fitness template created with ' . $template->fields->count() . ' fields');
        }
    }

    private function createKreativMedienTemplate()
    {
        $template = RentalFieldTemplate::create([
            'name' => 'Kreativ & Medien',
            'description' => 'Template für kreative Räume und Medienproduktion',
            'is_active' => true,
            'sort_order' => 9,
            'settings' => ['show_in_search' => true, 'group_display' => true],
        ]);

        $fields = [
            [
                'field_type' => 'select',
                'field_name' => 'kreativ_typ',
                'field_label' => 'Kreativtyp',
                'field_description' => 'Art der kreativen Einrichtung',
                'options' => [
                    'atelier' => 'Atelier',
                    'fotostudio' => 'Fotostudio',
                    'filmstudio' => 'Filmstudio',
                    'tonstudio' => 'Tonstudio',
                    'kunstschule' => 'Kunstschule',
                    'werkstatt' => 'Werkstatt',
                    'galerie' => 'Galerie',
                    'theater' => 'Theater',
                    'musikraum' => 'Musikraum',
                    'designstudio' => 'Designstudio',
                ],
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => true,
                'sort_order' => 1,
            ],
            [
                'field_type' => 'number',
                'field_name' => 'kreativflaeche',
                'field_label' => 'Kreativfläche (m²)',
                'field_description' => 'Verfügbare kreative Fläche',
                'validation_rules' => ['min_value' => 10, 'max_value' => 1000],
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => false,
                'sort_order' => 2,
            ],
            [
                'field_type' => 'checkbox',
                'field_name' => 'kreativ_ausstattung',
                'field_label' => 'Kreativausstattung',
                'field_description' => 'Verfügbare Ausstattung',
                'options' => [
                    'beleuchtung' => 'Beleuchtung',
                    'kamera' => 'Kamera',
                    'mikrofon' => 'Mikrofon',
                    'computer' => 'Computer',
                    'drucker' => 'Drucker',
                    'werkzeug' => 'Werkzeug',
                    'klimaanlage' => 'Klimaanlage',
                    'heizung' => 'Heizung',
                    'parkplatz' => 'Parkplatz',
                    'buero' => 'Büro',
                ],
                'is_required' => false,
                'is_filterable' => true,
                'is_searchable' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($fields as $fieldData) {
            $template->fields()->create($fieldData);
        }

        // Assign to Kreativ/Medien categories
        $categories = Category::where('name', 'LIKE', '%kreativ%')
            ->orWhere('name', 'LIKE', '%medien%')
            ->orWhere('name', 'LIKE', '%atelier%')
            ->orWhere('name', 'LIKE', '%studio%')
            ->take(5)
            ->get();

        if ($categories->count() > 0) {
            $template->categories()->attach($categories->pluck('id'));
            $this->command->info('🎨 Kreativ & Medien template created with ' . $template->fields->count() . ' fields');
        }
    }

    private function createTechnikItTemplate()
    {
        $template = RentalFieldTemplate::create([
            'name' => 'Technik & IT',
            'description' => 'Template für Technik- und IT-Räume',
            'is_active' => true,
            'sort_order' => 10,
            'settings' => ['show_in_search' => true, 'group_display' => true],
        ]);

        $fields = [
            [
                'field_type' => 'select',
                'field_name' => 'technik_typ',
                'field_label' => 'Techniktyp',
                'field_description' => 'Art der Technikeinrichtung',
                'options' => [
                    'serverraum' => 'Serverraum',
                    'rechenzentrum' => 'Rechenzentrum',
                    'it_buero' => 'IT-Büro',
                    'labor' => 'Labor',
                    'werkstatt' => 'Werkstatt',
                    'schulungsraum' => 'IT-Schulungsraum',
                    'showroom' => 'Technik-Showroom',
                    'testraum' => 'Testraum',
                    'entwicklung' => 'Entwicklungsraum',
                    'support' => 'Support-Büro',
                ],
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => true,
                'sort_order' => 1,
            ],
            [
                'field_type' => 'number',
                'field_name' => 'technikflaeche',
                'field_label' => 'Technikfläche (m²)',
                'field_description' => 'Verfügbare Technikfläche',
                'validation_rules' => ['min_value' => 10, 'max_value' => 1000],
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => false,
                'sort_order' => 2,
            ],
            [
                'field_type' => 'checkbox',
                'field_name' => 'technik_ausstattung',
                'field_label' => 'Technikausstattung',
                'field_description' => 'Verfügbare Ausstattung',
                'options' => [
                    'server' => 'Server',
                    'computer' => 'Computer',
                    'netzwerk' => 'Netzwerk',
                    'internet' => 'Internet',
                    'klimaanlage' => 'Klimaanlage',
                    'heizung' => 'Heizung',
                    'sicherheit' => 'Sicherheitssystem',
                    'notstrom' => 'Notstrom',
                    'parkplatz' => 'Parkplatz',
                    'buero' => 'Büro',
                ],
                'is_required' => false,
                'is_filterable' => true,
                'is_searchable' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($fields as $fieldData) {
            $template->fields()->create($fieldData);
        }

        // Assign to Technik/IT categories
        $categories = Category::where('name', 'LIKE', '%technik%')
            ->orWhere('name', 'LIKE', '%it%')
            ->orWhere('name', 'LIKE', '%computer%')
            ->orWhere('name', 'LIKE', '%server%')
            ->take(5)
            ->get();

        if ($categories->count() > 0) {
            $template->categories()->attach($categories->pluck('id'));
            $this->command->info('💻 Technik & IT template created with ' . $template->fields->count() . ' fields');
        }
    }
} 