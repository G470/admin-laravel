<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CreditPackage;

class CreditPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Starter-Paket',
                'description' => 'Perfekt für den Einstieg in die Artikelwerbung. Ideal für neue Verkäufer.',
                'credits_amount' => 10,
                'standard_price' => 19.99,
                'offer_price' => 14.99,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Basic-Paket',
                'description' => 'Unser beliebtestes Paket für regelmäßige Werbung. Gutes Preis-Leistungs-Verhältnis.',
                'credits_amount' => 25,
                'standard_price' => 39.99,
                'offer_price' => 29.99,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Business-Paket',
                'description' => 'Für aktive Verkäufer mit vielen Artikeln. Erweiterte Werbemöglichkeiten.',
                'credits_amount' => 50,
                'standard_price' => 69.99,
                'offer_price' => 49.99,
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Premium-Paket',
                'description' => 'Maximale Reichweite für Profis. Beste Preis-Performance für Vielnutzer.',
                'credits_amount' => 100,
                'standard_price' => 119.99,
                'offer_price' => 79.99,
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Enterprise-Paket',
                'description' => 'Für Unternehmen mit großem Inventar. Maximale Flexibilität und Reichweite.',
                'credits_amount' => 250,
                'standard_price' => 249.99,
                'offer_price' => 179.99,
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Test-Paket',
                'description' => 'Kleines Testpaket für Neukunden zum Ausprobieren der Werbefunktionen.',
                'credits_amount' => 5,
                'standard_price' => 9.99,
                'offer_price' => 9.99,
                'sort_order' => 0,
                'is_active' => false, // Inaktiv als Demo für Admin-Interface
            ],
        ];

        foreach ($packages as $package) {
            CreditPackage::create($package);
        }

        $this->command->info('Credit-Pakete erfolgreich erstellt: ' . count($packages) . ' Pakete');
    }
}