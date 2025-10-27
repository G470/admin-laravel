<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rental;
use App\Models\RentalFieldTemplate;
use App\Models\RentalField;
use App\Models\RentalFieldValue;
use App\Models\Category;
use App\Models\User;
use App\Helpers\DynamicRentalFields;

class RentalFieldValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Creating test rentals with dynamic field values...');

        // Get categories that have templates
        $vehicleCategory = Category::find(243); // Fahrzeuge, Boote & Fluggeräte
        $eventCategory = Category::find(608);   // Party, Messe & Events

        if (!$vehicleCategory) {
            $this->command->warn('⚠️  Vehicle category not found');
            return;
        }

        if (!$eventCategory) {
            $this->command->warn('⚠️  Event category not found');
            return;
        }

        // Get a user for creating rentals (use first available user)
        $user = User::first();
        if (!$user) {
            $this->command->warn('⚠️  No users found. Please create a user first.');
            return;
        }

        // Create test vehicle rentals
        $this->createVehicleRentals($vehicleCategory, $user);

        // Create test event rentals
        $this->createEventRentals($eventCategory, $user);

        $this->command->info('🎉 Test rentals with dynamic field values created successfully!');
    }

    private function createVehicleRentals($category, $user)
    {
        $vehicleTemplate = RentalFieldTemplate::where('name', 'Fahrzeug Details')->first();
        if (!$vehicleTemplate) {
            $this->command->warn('⚠️  Vehicle template not found');
            return;
        }

        // Common rental attributes
        $commonAttributes = [
            'price_ranges_id' => 2, // Tagespreis
            'category_id' => $category->id,
            'vendor_id' => $user->id,
            'location_id' => 1,
            'service_fee' => 0.00,
            'status' => 'active',
            'currency' => 'EUR',
        ];

        // Vehicle 1: BMW 3er
        $bmw = Rental::create(array_merge([
            'title' => 'BMW 320i Limousine - Comfort Paket',
            'description' => 'Komfortable BMW Limousine mit Vollausstattung. Perfekt für Geschäftstermine oder besondere Anlässe. Gepflegtes Fahrzeug mit niedrigem Kilometerstand.',
            'price_range_day' => 89.00,
        ], $commonAttributes));

        $bmwFieldValues = [
            'fahrzeugmarke' => 'bmw',
            'baujahr' => '2019',
            'kraftstoff' => 'benzin',
            'getriebe' => 'automatik',
            'sitzplaetze' => '5',
            'ausstattung' => ['klimaanlage', 'navigation', 'bluetooth', 'lederausstattung', 'sitzheizung'],
        ];

        DynamicRentalFields::saveFieldValues($bmw->id, $bmwFieldValues);
        $this->command->info('✅ BMW 320i created with dynamic fields');

        // Vehicle 2: Mercedes Sprinter
        $sprinter = Rental::create(array_merge([
            'title' => 'Mercedes Sprinter Transporter - 9-Sitzer',
            'description' => 'Geräumiger Mercedes Sprinter für Gruppenfahrten oder Umzüge. Klimaanlage, viel Stauraum und zuverlässiger Motor. Ideal für Firmenveranstaltungen oder Familienausflüge.',
            'price_range_day' => 125.00,
        ], $commonAttributes));

        $sprinterFieldValues = [
            'fahrzeugmarke' => 'mercedes',
            'baujahr' => '2020',
            'kraftstoff' => 'diesel',
            'getriebe' => 'manuell',
            'sitzplaetze' => '9',
            'ausstattung' => ['klimaanlage', 'bluetooth', 'usb'],
        ];

        DynamicRentalFields::saveFieldValues($sprinter->id, $sprinterFieldValues);
        $this->command->info('✅ Mercedes Sprinter created with dynamic fields');

        // Vehicle 3: Tesla Model 3
        $tesla = Rental::create(array_merge([
            'title' => 'Tesla Model 3 Performance - Elektro Premium',
            'description' => 'Hochmoderner Tesla Model 3 mit Autopilot und Premium-Ausstattung. Umweltfreundlich und leise. Perfekt für technikaffine Fahrer und Umweltbewusste.',
            'price_range_day' => 149.00,
        ], $commonAttributes));

        $teslaFieldValues = [
            'fahrzeugmarke' => 'andere',
            'baujahr' => '2022',
            'kraftstoff' => 'elektro',
            'getriebe' => 'automatik',
            'sitzplaetze' => '5',
            'ausstattung' => ['klimaanlage', 'navigation', 'bluetooth', 'usb', 'lederausstattung'],
        ];

        DynamicRentalFields::saveFieldValues($tesla->id, $teslaFieldValues);
        $this->command->info('✅ Tesla Model 3 created with dynamic fields');
    }

    private function createEventRentals($category, $user)
    {
        $eventTemplate = RentalFieldTemplate::where('name', 'Event & Party Equipment')->first();
        if (!$eventTemplate) {
            $this->command->warn('⚠️  Event template not found');
            return;
        }

        // Common rental attributes
        $commonAttributes = [
            'price_ranges_id' => 2, // Tagespreis
            'category_id' => $category->id,
            'vendor_id' => $user->id,
            'location_id' => 1,
            'service_fee' => 0.00,
            'status' => 'active',
            'currency' => 'EUR',
        ];

        // Event 1: Hochzeits-Pavillón
        $pavilion = Rental::create(array_merge([
            'title' => 'Eleganter Hochzeitspavillon 6x12m - Weiß',
            'description' => 'Wunderschöner weißer Pavillon für Hochzeiten und festliche Anlässe. Wetterfest und elegant. Inklusive Seitenwände und Beleuchtung. Perfekt für Gartenhochzeiten oder Outdoor-Events.',
            'price_range_day' => 450.00,
        ], $commonAttributes));

        $pavilionFieldValues = [
            'event_typ' => 'hochzeit',
            'personen_anzahl' => '120',
            'indoor_outdoor' => ['outdoor', 'wettergeschuetzt'],
            'stromanschluss' => '230v',
            'aufbauzeit' => '4-6 Stunden',
            'zusatzleistungen' => ['aufbau', 'abbau', 'transport'],
        ];

        DynamicRentalFields::saveFieldValues($pavilion->id, $pavilionFieldValues);
        $this->command->info('✅ Wedding pavilion created with dynamic fields');

        // Event 2: DJ Equipment Set
        $djSet = Rental::create(array_merge([
            'title' => 'Profi DJ-Equipment Set - Pioneer DDJ-FLX10',
            'description' => 'Komplettes DJ Set mit Pioneer Controller, Lautsprechern und Mikrofon. Perfekt für Geburtstagsfeiern, Firmenfeiern oder kleinere Events. Einfache Bedienung auch für Einsteiger.',
            'price_range_day' => 85.00,
        ], $commonAttributes));

        $djSetFieldValues = [
            'event_typ' => 'geburtstag',
            'personen_anzahl' => '80',
            'indoor_outdoor' => ['indoor', 'outdoor'],
            'stromanschluss' => '230v',
            'aufbauzeit' => '1-2 Stunden',
            'zusatzleistungen' => ['aufbau', 'bedienung'],
        ];

        DynamicRentalFields::saveFieldValues($djSet->id, $djSetFieldValues);
        $this->command->info('✅ DJ Equipment Set created with dynamic fields');

        // Event 3: Messebooth
        $booth = Rental::create(array_merge([
            'title' => 'Modularer Messestand 3x3m - Premium',
            'description' => 'Professioneller modularer Messestand für Messen und Ausstellungen. Inklusive Beleuchtung, Stromanschlüsse und Präsentationsflächen. Einfacher Auf- und Abbau durch Klicksystem.',
            'price_range_day' => 280.00,
        ], $commonAttributes));

        $boothFieldValues = [
            'event_typ' => 'messe',
            'personen_anzahl' => '50',
            'indoor_outdoor' => ['indoor'],
            'stromanschluss' => '400v',
            'aufbauzeit' => '3-4 Stunden',
            'zusatzleistungen' => ['aufbau', 'abbau', 'transport', 'bedienung'],
        ];

        DynamicRentalFields::saveFieldValues($booth->id, $boothFieldValues);
        $this->command->info('✅ Trade booth created with dynamic fields');

        // Event 4: Party-Beleuchtung
        $lighting = Rental::create(array_merge([
            'title' => 'LED Party Beleuchtungsset - RGB Vollausstattung',
            'description' => 'Komplettes LED-Beleuchtungsset für Partys und Events. Steuerung per App möglich, verschiedene Farbprogramme und Effekte. Inklusive Stative und Verkabelung.',
            'price_range_day' => 65.00,
        ], $commonAttributes));

        $lightingFieldValues = [
            'event_typ' => 'privat',
            'personen_anzahl' => '60',
            'indoor_outdoor' => ['indoor', 'outdoor'],
            'stromanschluss' => '230v',
            'aufbauzeit' => '2-3 Stunden',
            'zusatzleistungen' => ['aufbau', 'bedienung'],
        ];

        DynamicRentalFields::saveFieldValues($lighting->id, $lightingFieldValues);
        $this->command->info('✅ Party lighting created with dynamic fields');
    }
}
