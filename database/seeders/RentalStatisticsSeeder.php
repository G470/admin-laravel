<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\Rental;
use Carbon\Carbon;
use Faker\Factory as Faker;

class RentalStatisticsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('de_DE');
        
        $rentals = Rental::where('status', 'active')->get();
        
        if ($rentals->isEmpty()) {
            $this->command->warn('⚠️  Keine aktiven Rentals gefunden. RentalStatisticsSeeder übersprungen.');
            return;
        }

        $this->command->info('📊 Erstelle Statistik-Daten für Rentals...');

        foreach ($rentals as $rental) {
            $this->generateRentalStats($faker, $rental);
        }

        $this->command->info('✅ Statistik-Daten für alle Rentals erstellt!');
    }

    /**
     * Generiere Statistiken für ein Rental
     */
    private function generateRentalStats($faker, Rental $rental): void
    {
        $startDate = Carbon::now()->subDays(365); // 1 Jahr zurück
        $endDate = Carbon::now();
        
        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            // Grundwerte basierend auf Rental-Popularität
            $basePopularity = $this->calculateBasePopularity($rental);
            
            // Saisonale und Wochentag-Anpassungen
            $seasonalMultiplier = $this->getSeasonalMultiplier($currentDate, $rental);
            $weekdayMultiplier = $this->getWeekdayMultiplier($currentDate);
            
            // Generiere tägliche Statistiken
            $views = $this->generateViews($faker, $basePopularity, $seasonalMultiplier, $weekdayMultiplier);
            $favorites = $this->generateFavorites($faker, $views);
            $inquiries = $this->generateInquiries($faker, $views);
            $bookings = $this->generateBookings($faker, $inquiries);
            $revenue = $this->generateRevenue($faker, $rental, $bookings);
            
            DB::table('rental_statistics')->updateOrInsert(
                [
                    'rental_id' => $rental->id,
                    'date' => $currentDate->toDateString(),
                ],
                [
                    'views' => $views,
                    'favorites' => $favorites,
                    'inquiries' => $inquiries,
                    'bookings' => $bookings,
                    'revenue' => $revenue,
                    'created_at' => $currentDate,
                    'updated_at' => $currentDate,
                ]
            );
            
            $currentDate->addDay();
        }
    }

    /**
     * Berechne Basis-Popularität eines Rentals
     */
    private function calculateBasePopularity(Rental $rental): float
    {
        $factors = 1.0;
        
        // Featured Rentals sind beliebter
        if ($rental->featured) {
            $factors *= 2.5;
        }
        
        // Preis-Faktor (günstigere Rentals bekommen mehr Views)
        $price = $rental->price_range_day ?? $rental->price_range_hour ?? $rental->price_range_once ?? 100;
        if ($price < 50) {
            $factors *= 1.8;
        } elseif ($price < 100) {
            $factors *= 1.4;
        } elseif ($price > 200) {
            $factors *= 0.7;
        }
        
        // Kategorien-basierte Popularität
        $popularCategories = ['Events', 'Baumaschinen', 'Garten', 'Transport'];
        if ($rental->category && in_array($rental->category->name, $popularCategories)) {
            $factors *= 1.6;
        }
        
        return $factors;
    }

    /**
     * Saisonale Anpassungen
     */
    private function getSeasonalMultiplier(Carbon $date, Rental $rental): float
    {
        $month = $date->month;
        $category = $rental->category->name ?? '';
        
        // Allgemeine saisonale Trends
        $seasonal = 1.0;
        
        // Frühling/Sommer beliebter für Outdoor-Geräte
        if (in_array($month, [4, 5, 6, 7, 8]) && 
            preg_match('/garten|outdoor|event|camping/i', $category)) {
            $seasonal *= 1.8;
        }
        
        // Winter beliebter für Indoor/Heizung/Events
        if (in_array($month, [11, 12, 1, 2]) && 
            preg_match('/heizung|indoor|event|party/i', $category)) {
            $seasonal *= 1.4;
        }
        
        // Baumaschinen im Frühjahr/Sommer
        if (in_array($month, [3, 4, 5, 6, 7, 8, 9]) && 
            preg_match('/bau|maschinen|werkzeug/i', $category)) {
            $seasonal *= 1.6;
        }
        
        // Weihnachtszeit für Events
        if (in_array($month, [11, 12]) && 
            preg_match('/event|party|dekoration/i', $category)) {
            $seasonal *= 2.2;
        }
        
        return $seasonal;
    }

    /**
     * Wochentag-Anpassungen
     */
    private function getWeekdayMultiplier(Carbon $date): float
    {
        $dayOfWeek = $date->dayOfWeek;
        
        // Wochenende weniger Business-Aktivität
        if (in_array($dayOfWeek, [0, 6])) { // Sonntag, Samstag
            return 0.6;
        }
        
        // Dienstag-Donnerstag stärkste Tage
        if (in_array($dayOfWeek, [2, 3, 4])) { // Di-Do
            return 1.3;
        }
        
        // Montag/Freitag normal
        return 1.0;
    }

    /**
     * Generiere Aufrufe
     */
    private function generateViews($faker, float $basePopularity, float $seasonal, float $weekday): int
    {
        $baseViews = $faker->numberBetween(5, 80);
        $multiplier = $basePopularity * $seasonal * $weekday;
        
        $views = round($baseViews * $multiplier);
        
        // Zufälliger Spike (5% Chance)
        if ($faker->boolean(5)) {
            $views *= $faker->numberBetween(3, 8);
        }
        
        return max(0, $views);
    }

    /**
     * Generiere Favoriten basierend auf Views
     */
    private function generateFavorites($faker, int $views): int
    {
        if ($views === 0) return 0;
        
        // 2-8% der Views werden zu Favoriten
        $conversionRate = $faker->numberBetween(2, 8) / 100;
        $favorites = round($views * $conversionRate);
        
        return max(0, $favorites);
    }

    /**
     * Generiere Anfragen basierend auf Views
     */
    private function generateInquiries($faker, int $views): int
    {
        if ($views === 0) return 0;
        
        // 1-5% der Views werden zu Anfragen
        $conversionRate = $faker->numberBetween(1, 5) / 100;
        $inquiries = round($views * $conversionRate);
        
        return max(0, $inquiries);
    }

    /**
     * Generiere Buchungen basierend auf Anfragen
     */
    private function generateBookings($faker, int $inquiries): int
    {
        if ($inquiries === 0) return 0;
        
        // 20-60% der Anfragen werden zu Buchungen
        $conversionRate = $faker->numberBetween(20, 60) / 100;
        $bookings = round($inquiries * $conversionRate);
        
        return max(0, $bookings);
    }

    /**
     * Generiere Umsatz basierend auf Buchungen
     */
    private function generateRevenue($faker, Rental $rental, int $bookings): float
    {
        if ($bookings === 0) return 0.0;
        
        // Durchschnitts-Buchungswert basierend auf Preisen
        $avgPrice = 0;
        if ($rental->price_range_day) {
            $avgPrice = $rental->price_range_day * $faker->numberBetween(1, 3); // 1-3 Tage
        } elseif ($rental->price_range_hour) {
            $avgPrice = $rental->price_range_hour * $faker->numberBetween(4, 12); // 4-12 Stunden
        } elseif ($rental->price_range_once) {
            $avgPrice = $rental->price_range_once;
        } else {
            $avgPrice = $faker->numberBetween(50, 300);
        }
        
        $revenue = $bookings * $avgPrice;
        
        // Variation ±30%
        $variation = $faker->numberBetween(-30, 30) / 100;
        $revenue *= (1 + $variation);
        
        return round($revenue, 2);
    }
}
