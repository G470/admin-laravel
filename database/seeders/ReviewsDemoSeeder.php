<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\Rental;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory as Faker;

class ReviewsDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('de_DE');
        
        // Hole abgeschlossene Buchungen für realistische Reviews
        $completedBookings = Booking::where('status', 'completed')
            ->with(['rental', 'renter'])
            ->get();
        
        if ($completedBookings->isEmpty()) {
            $this->command->warn('⚠️  Keine abgeschlossenen Buchungen gefunden. ReviewsDemoSeeder übersprungen.');
            return;
        }

        $this->command->info('⭐ Erstelle Demo-Reviews für Rentals...');

        $reviewCount = 0;

        foreach ($completedBookings as $booking) {
            // Nicht alle abgeschlossenen Buchungen bekommen Reviews (70% Wahrscheinlichkeit)
            if ($faker->boolean(30)) {
                continue;
            }

            $rental = $booking->rental;
            $customer = $booking->renter;
            
            if (!$rental || !$customer) {
                continue;
            }

            // Prüfe ob bereits Review für diese Kombination existiert
            $existingReview = Review::where('rental_id', $rental->id)
                ->where('user_id', $customer->id)
                ->first();
                
            if ($existingReview) {
                continue;
            }

            $rating = $this->generateRealisticRating($faker);
            $comment = $this->generateReviewComment($faker, $rental, $rating);
            $stayDate = $booking->end_date;
            $createdAt = $stayDate->copy()->addDays($faker->numberBetween(1, 14));

            Review::create([
                'rental_id' => $rental->id,
                'user_id' => $customer->id,
                'rating' => $rating,
                'comment' => $comment,
                'status' => $faker->randomElement(['published', 'published', 'published', 'pending']), // 75% published
                'is_verified' => $faker->boolean(80), // 80% verifiziert
                'stay_date' => $stayDate,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            $reviewCount++;
        }

        // Zusätzliche Reviews für Rentals ohne Buchungen erstellen
        $this->createAdditionalReviews($faker);

        $this->command->info("✅ {$reviewCount} Demo-Reviews aus Buchungen erstellt!");
    }

    /**
     * Erstelle zusätzliche Reviews für bessere Statistiken
     */
    private function createAdditionalReviews($faker): void
    {
        $rentals = Rental::where('status', 'active')->get();
        $customers = User::where('is_vendor', false)->where('is_admin', false)->get();
        
        $additionalReviews = 0;

        foreach ($rentals as $rental) {
            $currentReviewCount = $rental->reviews()->count();
            
            // Stelle sicher, dass beliebte Rentals mehr Reviews haben
            $targetReviews = $faker->numberBetween(2, 12);
            $neededReviews = max(0, $targetReviews - $currentReviewCount);
            
            for ($i = 0; $i < $neededReviews; $i++) {
                $customer = $customers->random();
                
                // Prüfe Duplikate
                $existingReview = Review::where('rental_id', $rental->id)
                    ->where('user_id', $customer->id)
                    ->first();
                    
                if ($existingReview) {
                    continue;
                }

                $rating = $this->generateRealisticRating($faker);
                $comment = $this->generateReviewComment($faker, $rental, $rating);
                $stayDate = Carbon::now()->subDays($faker->numberBetween(7, 365));
                $createdAt = $stayDate->copy()->addDays($faker->numberBetween(1, 21));

                Review::create([
                    'rental_id' => $rental->id,
                    'user_id' => $customer->id,
                    'rating' => $rating,
                    'comment' => $comment,
                    'status' => $faker->randomElement(['published', 'published', 'published', 'pending']),
                    'is_verified' => $faker->boolean(75),
                    'stay_date' => $stayDate,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                $additionalReviews++;
            }
        }

        $this->command->info("✅ {$additionalReviews} zusätzliche Demo-Reviews erstellt!");
    }

    /**
     * Generiere realistische Bewertungen (Tendenz zu höheren Bewertungen)
     */
    private function generateRealisticRating($faker): int
    {
        // Realistische Verteilung: mehr 4-5 Sterne, weniger 1-2 Sterne
        $weights = [
            1 => 5,   // 5%
            2 => 8,   // 8%
            3 => 15,  // 15%
            4 => 35,  // 35%
            5 => 37   // 37%
        ];

        return $faker->randomElement(
            array_merge(
                array_fill(0, $weights[1], 1),
                array_fill(0, $weights[2], 2),
                array_fill(0, $weights[3], 3),
                array_fill(0, $weights[4], 4),
                array_fill(0, $weights[5], 5)
            )
        );
    }

    /**
     * Generiere Reviews basierend auf Bewertung und Rental
     */
    private function generateReviewComment($faker, Rental $rental, int $rating): string
    {
        $rentalTitle = $rental->title;
        
        switch ($rating) {
            case 5:
                $comments = [
                    "Absolut perfekt! {$rentalTitle} hat einwandfrei funktioniert. Der Vermieter war sehr hilfsbereit und professionell. Gerne wieder!",
                    "Top Gerät, top Service! {$rentalTitle} war genau das was ich brauchte. Schnelle Abwicklung und faire Preise. Sehr empfehlenswert!",
                    "Hervorragend! {$rentalTitle} in bestem Zustand, ausführliche Einweisung erhalten. Alles reibungslos gelaufen. 5 Sterne verdient!",
                    "Perfekte Erfahrung mit {$rentalTitle}. Zuverlässiger Vermieter, gepflegtes Gerät, pünktliche Übergabe. Werde sicher wieder mieten!",
                    "Kann ich nur weiterempfehlen! {$rentalTitle} hat mein Projekt perfekt unterstützt. Professionelle Abwicklung von A bis Z.",
                    "Exzellent! {$rentalTitle} war in tadellosem Zustand. Der Vermieter ist sehr kundenorientiert. Danke für den super Service!",
                ];
                break;
                
            case 4:
                $comments = [
                    "Sehr gut! {$rentalTitle} hat prima funktioniert. Kleiner Abzug nur wegen der etwas umständlichen Abholung. Ansonsten top!",
                    "Gute Erfahrung mit {$rentalTitle}. Gerät war OK, Vermieter freundlich. Würde wieder mieten, auch wenn der Preis etwas hoch war.",
                    "Solide 4 Sterne! {$rentalTitle} erfüllte den Zweck vollkommen. Einziger Kritikpunkt: könnte sauberer sein. Aber funktionell top!",
                    "{$rentalTitle} hat gut funktioniert. Service war freundlich, nur die Terminkoordination war etwas schwierig. Trotzdem empfehlenswert!",
                    "Gut vermietet! {$rentalTitle} war in Ordnung, hat seinen Job gemacht. Vermieter hilfsbereit. Kleine Mängel, aber nichts Dramatisches.",
                    "Zufrieden mit {$rentalTitle}. Hat alles geklappt wie gewünscht. Preis-Leistung stimmt. Gerne wieder bei Bedarf!",
                ];
                break;
                
            case 3:
                $comments = [
                    "Mittelmäßig. {$rentalTitle} hat funktioniert, aber nicht perfekt. Vermieter war OK. Gibt bessere Alternativen am Markt.",
                    "Geht so. {$rentalTitle} hatte kleine Macken, trotzdem nutzbar. Service ausbaufähig. Für den Notfall OK, sonst andere suchen.",
                    "Durchschnitt. {$rentalTitle} erfüllte grundsätzlich den Zweck. Hätte mir mehr Zubehör und bessere Beratung gewünscht.",
                    "OK aber nicht überragend. {$rentalTitle} lief, hatte aber Schwächen. Vermieter bemüht, trotzdem nur mittelmäßige Erfahrung.",
                    "3 Sterne für {$rentalTitle}. Hat funktioniert, war aber schon etwas abgenutzt. Preis war angemessen, Service durchschnittlich.",
                ];
                break;
                
            case 2:
                $comments = [
                    "Nicht zufrieden. {$rentalTitle} hatte mehrere Probleme. Vermieter wenig hilfsbereit. Würde nicht nochmal mieten.",
                    "Enttäuschend! {$rentalTitle} war in schlechtem Zustand. Keine ordentliche Einweisung bekommen. Service mangelhaft.",
                    "Leider nicht gut. {$rentalTitle} hatte Defekte, trotzdem vollen Preis zahlen müssen. Kommunikation schwierig.",
                    "Problematisch. {$rentalTitle} funktionierte nur teilweise. Vermieter unfreundlich bei Reklamation. Nicht empfehlenswert.",
                    "Schlechte Erfahrung mit {$rentalTitle}. Gerät dreckig, Funktion eingeschränkt. Für den Preis eine Frechheit.",
                ];
                break;
                
            case 1:
            default:
                $comments = [
                    "Katastrophe! {$rentalTitle} war defekt, Vermieter unerreichbar. Kompletter Ausfall meines Projekts. Finger weg!",
                    "Absolute Enttäuschung! {$rentalTitle} funktionierte gar nicht. Geld nicht zurückbekommen. Unseriöser Vermieter!",
                    "Sehr schlecht! {$rentalTitle} total verdreckt und kaputt. Vermieter unverschämt. Nie wieder!",
                    "1 Stern ist noch zu viel! {$rentalTitle} unbrauchbar, Service eine Zumutung. Reinste Geldverschwendung!",
                    "Furchtbar! {$rentalTitle} war Schrott. Termin nicht eingehalten, keine Entschuldigung. Unprofessionell!",
                ];
        }

        return $faker->randomElement($comments);
    }
}
