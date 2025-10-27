<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\Rental;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory as Faker;

class BookingDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('de_DE');
        
        // Hole alle Rentals und Users
        $rentals = Rental::where('status', 'active')->get();
        $customers = User::where('is_vendor', false)->where('is_admin', false)->get();
        
        if ($rentals->isEmpty() || $customers->isEmpty()) {
            $this->command->warn('⚠️  Keine Rentals oder Customer-User gefunden. BookingDemoSeeder übersprungen.');
            return;
        }

        $this->command->info('🏁 Erstelle Demo-Buchungen...');

        $bookingStatuses = ['pending', 'confirmed', 'cancelled', 'completed'];
        $rentalTypes = ['hourly', 'daily', 'once'];
        
        // Erstelle 150 Demo-Buchungen
        for ($i = 0; $i < 150; $i++) {
            $rental = $rentals->random();
            $customer = $customers->random();
            $status = $faker->randomElement($bookingStatuses);
            $rentalType = $faker->randomElement($rentalTypes);
            
            // Generiere realistische Daten basierend auf Status
            $startDate = $this->generateStartDate($faker, $status);
            $endDate = $this->generateEndDate($faker, $startDate, $rentalType);
            $createdAt = $this->generateCreatedAt($faker, $startDate, $status);
            
            // Berechne Preis basierend auf Rental-Typ
            $totalPrice = $this->calculatePrice($rental, $startDate, $endDate, $rentalType);
            
            Booking::create([
                'renter_id' => $customer->id,
                'rental_id' => $rental->id,
                'status' => $status,
                'total_amount' => $totalPrice,
                'commission_amount' => round($totalPrice * 0.15, 2), // 15% Provision
                'start_date' => $startDate,
                'end_date' => $endDate,
                'rental_type' => $rentalType,
                'message' => $this->generateBookingMessage($faker, $rental, $rentalType),
                'vendor_notes' => $this->generateVendorNotes($faker, $status),
                'total_price' => $totalPrice,
                'guest_email' => $customer->email,
                'guest_name' => $customer->name,
                'guest_phone' => $faker->phoneNumber,
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addMinutes($faker->numberBetween(5, 120)),
            ]);
        }

        $this->command->info('✅ 150 Demo-Buchungen erfolgreich erstellt!');
    }

    /**
     * Generiere Start-Datum basierend auf Status
     */
    private function generateStartDate($faker, $status): Carbon
    {
        switch ($status) {
            case 'completed':
                // Abgeschlossene Buchungen in der Vergangenheit
                return Carbon::now()->subDays($faker->numberBetween(30, 365));
                
            case 'cancelled':
                // Stornierte Buchungen können überall sein
                return Carbon::now()->addDays($faker->numberBetween(-180, 90));
                
            case 'confirmed':
                // Bestätigte Buchungen meist in der Zukunft
                return Carbon::now()->addDays($faker->numberBetween(-30, 180));
                
            case 'pending':
            default:
                // Pending Buchungen meist in naher Zukunft
                return Carbon::now()->addDays($faker->numberBetween(1, 60));
        }
    }

    /**
     * Generiere End-Datum basierend auf Rental-Typ
     */
    private function generateEndDate($faker, Carbon $startDate, string $rentalType): Carbon
    {
        switch ($rentalType) {
            case 'hourly':
                return $startDate->copy()->addHours($faker->numberBetween(2, 12));
                
            case 'daily':
                return $startDate->copy()->addDays($faker->numberBetween(1, 14));
                
            case 'once':
            default:
                return $startDate->copy()->addDays($faker->numberBetween(1, 7));
        }
    }

    /**
     * Generiere Erstellungsdatum basierend auf Status
     */
    private function generateCreatedAt($faker, Carbon $startDate, string $status): Carbon
    {
        switch ($status) {
            case 'completed':
                // Buchung wurde vor dem Event erstellt
                return $startDate->copy()->subDays($faker->numberBetween(1, 30));
                
            case 'cancelled':
                // Stornierung kann früh oder spät erfolgen
                return $startDate->copy()->subDays($faker->numberBetween(1, 60));
                
            default:
                // Normale Vorlaufzeit
                return $startDate->copy()->subDays($faker->numberBetween(1, 21));
        }
    }

    /**
     * Berechne Preis basierend auf Rental und Dauer
     */
    private function calculatePrice(Rental $rental, Carbon $startDate, Carbon $endDate, string $rentalType): float
    {
        switch ($rentalType) {
            case 'hourly':
                $hours = $startDate->diffInHours($endDate);
                return round(($rental->price_range_hour ?? 25) * $hours, 2);
                
            case 'daily':
                $days = $startDate->diffInDays($endDate) + 1;
                return round(($rental->price_range_day ?? 80) * $days, 2);
                
            case 'once':
            default:
                return round($rental->price_range_once ?? 150, 2);
        }
    }

    /**
     * Generiere realistische Buchungsnachrichten
     */
    private function generateBookingMessage($faker, Rental $rental, string $rentalType): string
    {
        $messages = [
            'hourly' => [
                "Hallo! Ich würde gerne {$rental->title} für ein Event mieten. Ist das Gerät verfügbar?",
                "Benötige {$rental->title} für einen Termin. Können Sie mir weitere Details geben?",
                "Interesse an stundenweiser Miete von {$rental->title}. Bitte kontaktieren Sie mich.",
                "Möchte {$rental->title} für heute Nachmittag mieten. Ist das möglich?",
            ],
            'daily' => [
                "Plane ein Projekt und benötige {$rental->title} für mehrere Tage. Verfügbarkeit?",
                "Würde gerne {$rental->title} für mein Wochenendprojekt mieten. Konditionen?",
                "Benötige {$rental->title} für eine Woche. Was kostet die Anlieferung?",
                "Interessiert an {$rental->title} für Renovierungsarbeiten. Bitte um Rückruf.",
            ],
            'once' => [
                "Brauche {$rental->title} für ein einmaliges Event. Komplettpreis verfügbar?",
                "Einmalige Miete von {$rental->title} gewünscht. Inklusive aller Kosten?",
                "Möchte {$rental->title} pauschal mieten. Können wir telefonieren?",
                "Interesse an Pauschalmiete für {$rental->title}. Verfügbar?",
            ]
        ];

        return $faker->randomElement($messages[$rentalType] ?? $messages['daily']);
    }

    /**
     * Generiere Vermieter-Notizen basierend auf Status
     */
    private function generateVendorNotes($faker, string $status): ?string
    {
        switch ($status) {
            case 'confirmed':
                $notes = [
                    'Kunde wurde über Abholung informiert.',
                    'Kaution erhalten. Kann vermietet werden.',
                    'Alle Dokumente vollständig. Freigegeben.',
                    'Termin bestätigt, Anlieferung organisiert.',
                    'Einweisung vereinbart.',
                ];
                break;
                
            case 'cancelled':
                $notes = [
                    'Kunde hat kurzfristig storniert.',
                    'Wegen Wetter abgesagt.',
                    'Terminkonflikt beim Kunden.',
                    'Objekt nicht verfügbar - entschuldigt.',
                    'Andere Prioritäten beim Mieter.',
                ];
                break;
                
            case 'completed':
                $notes = [
                    'Erfolgreich vermietet und zurückgegeben.',
                    'Keine Schäden, alles OK.',
                    'Kunde sehr zufrieden, gute Bewertung.',
                    'Planmäßige Rückgabe.',
                    'Wiederholung gewünscht.',
                ];
                break;
                
            default: // pending
                return null; // Pending haben meist noch keine Notizen
        }

        return $faker->boolean(70) ? $faker->randomElement($notes) : null;
    }
}
