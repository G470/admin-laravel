<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Run all demo data seeders in correct order.
     */
    public function run(): void
    {
        $this->command->info('🎯 Starte Demo-Daten Generation...');
        
        // Diese Seeder müssen nach RentalSeeder ausgeführt werden
        $this->call([
            BookingDemoSeeder::class,           // Erstelle Buchungen
            BookingMessagesDemoSeeder::class,   // Erstelle Nachrichten für Buchungen
            ReviewsDemoSeeder::class,           // Erstelle Reviews (überschreibt existierende)
            RentalStatisticsSeeder::class,      // Erstelle Statistik-Daten
        ]);
        
        $this->command->info('🎉 Alle Demo-Daten erfolgreich erstellt!');
        $this->command->info('');
        $this->command->info('📊 Erstellte Demo-Daten:');
        $this->command->info('   • ~150 Buchungen mit verschiedenen Status');
        $this->command->info('   • ~200+ Nachrichten zwischen Vendors und Kunden');
        $this->command->info('   • ~100+ Reviews mit realistischen Bewertungen');
        $this->command->info('   • 365 Tage Statistik-Daten für alle Rentals');
        $this->command->info('');
        $this->command->info('🚀 Die Plattform ist jetzt bereit für Demonstrationen!');
    }
}
