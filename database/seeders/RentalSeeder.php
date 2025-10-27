<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rental;
use App\Models\Category;
use App\Models\Location;
use App\Models\User;

class RentalSeeder extends Seeder
{
    public function run(): void
    {
        // Beispielprodukte für jede Kategorie
        $rentals = [
            // Toiletten, WC & Dusche
            [
                'title' => 'Mobiler Duschcontainer Premium',
                'description' => 'Hochwertiger mobiler Duschcontainer mit Warmwasserbereitung und Spiegelschrank. Ideal für Events und Baustellen.',
                'category' => 'Duschcontainer',
                'price_range_hour' => 25.00,
                'price_range_day' => 150.00,
                'price_range_once' => 0.00,
                'service_fee' => 10.00,
                'currency' => 'EUR',
                'status' => 'online',
                'price_ranges_id' => 1,
            ],
            [
                'title' => 'Toilettenwagen Deluxe',
                'description' => 'Luxuriöser Toilettenwagen mit 4 Kabinen, Waschbecken und Heizung. Perfekt für Hochzeiten und große Events.',
                'category' => 'Toilettenwagen',
                'price_range_hour' => 35.00,
                'price_range_day' => 250.00,
                'price_range_once' => 0.00,
                'service_fee' => 15.00,
                'currency' => 'EUR',
                'status' => 'online',
                'price_ranges_id' => 1,
            ],
            // Ton & Beschallung
            [
                'title' => 'Professionelles PA-System',
                'description' => 'Komplettes PA-System mit 2x 1000W Subwoofer, 2x 500W Topboxen und Mischpult. Ideal für Konzerte und Events.',
                'category' => 'Lautsprecher & Musikanlagen',
                'price_range_hour' => 50.00,
                'price_range_day' => 300.00,
                'price_range_once' => 0.00,
                'service_fee' => 20.00,
                'currency' => 'EUR',
                'status' => 'online',
                'price_ranges_id' => 1,
            ],
            [
                'title' => 'Drahtloses Mikrofon-Set',
                'description' => 'Set mit 4 drahtlosen Handmikrofonen und Empfänger. Perfekt für Konferenzen und Präsentationen.',
                'category' => 'Mikrofone',
                'price_range_hour' => 15.00,
                'price_range_day' => 100.00,
                'price_range_once' => 0.00,
                'service_fee' => 5.00,
                'currency' => 'EUR',
                'status' => 'online',
                'price_ranges_id' => 1,
            ],
            // Zelte & Zeltsysteme
            [
                'title' => 'Großes Pagodenzelt',
                'description' => 'Stabiles Pagodenzelt 6x6m mit Seitenwänden und Boden. Ideal für Gartenfeste und Events.',
                'category' => 'Pagodenzelt',
                'price_range_hour' => 30.00,
                'price_range_day' => 200.00,
                'price_range_once' => 0.00,
                'service_fee' => 15.00,
                'currency' => 'EUR',
                'status' => 'online',
                'price_ranges_id' => 1,
            ],
            [
                'title' => 'Aufblasbares Partyzelt',
                'description' => 'Schnell aufzubauendes aufblasbares Zelt 4x4m. Perfekt für spontane Outdoor-Events.',
                'category' => 'Aufblasbare Zelte',
                'price_range_hour' => 20.00,
                'price_range_day' => 150.00,
                'price_range_once' => 0.00,
                'service_fee' => 10.00,
                'currency' => 'EUR',
                'status' => 'online',
                'price_ranges_id' => 1,
            ],
            // Kostüme
            [
                'title' => 'Tierkostüm-Set',
                'description' => 'Set mit verschiedenen Tierkostümen (Löwe, Tiger, Bär). Ideal für Kindergeburtstage und Events.',
                'category' => 'Tierkostüm',
                'price_range_hour' => 10.00,
                'price_range_day' => 50.00,
                'price_range_once' => 0.00,
                'service_fee' => 5.00,
                'currency' => 'EUR',
                'status' => 'online',
                'price_ranges_id' => 1,
            ],
            // Sport, Freizeit & Kunst
            [
                'title' => 'Mountainbike-Verleih',
                'description' => 'Hochwertiges Mountainbike mit Helm und Zubehör. Perfekt für Ausflüge und Touren.',
                'category' => 'Fahrrad',
                'price_range_hour' => 15.00,
                'price_range_day' => 80.00,
                'price_range_once' => 0.00,
                'service_fee' => 8.00,
                'currency' => 'EUR',
                'status' => 'online',
                'price_ranges_id' => 1,
            ],
        ];

        // Hole unseren Vendor-User
        $vendor = User::where('email', 'vendor@inlando.test')->first();
        if (!$vendor) {
            // Fallback auf einen Test-Vendor falls der Vendor aus dem UserAndAdminSeeder nicht existiert
            $vendor = User::factory()->create([
                'name' => 'Test Vendor',
                'email' => 'test@example.com',
                'is_vendor' => true,
            ]);
        }

        // Erstelle eine Test-Location
        $location = Location::first();
        if (!$location) {
            $location = Location::create([
                'name' => 'Test Location',
                'street_address' => 'Teststraße 1',
                'postal_code' => '12345',
                'city' => 'Teststadt',
                'country' => 'DE',
                'vendor_id' => $vendor->id,
                'is_active' => true,
                'is_main' => true,
            ]);
        }

        // Erstelle die Kategorien, falls sie nicht existieren
        foreach ($rentals as $rental) {
            $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $rental['category']));
            $category = Category::firstOrCreate(
                ['name' => $rental['category']],
                [
                    'name' => $rental['category'],
                    'slug' => $slug,
                    'status' => 'online',
                    'sort_order' => 0,
                ]
            );

            // Erstelle das Rental
            Rental::create([
                'title' => $rental['title'],
                'description' => $rental['description'],
                'location_id' => $location->id,
                'category_id' => $category->id,
                'price_ranges_id' => $rental['price_ranges_id'],
                'price_range_hour' => $rental['price_range_hour'],
                'price_range_day' => $rental['price_range_day'],
                'price_range_once' => $rental['price_range_once'],
                'service_fee' => $rental['service_fee'],
                'currency' => $rental['currency'],
                'status' => $rental['status'],
                'vendor_id' => $vendor->id,
            ]);
        }
    }
}
