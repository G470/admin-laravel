<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BookingMessage;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory as Faker;

class BookingMessagesDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('de_DE');
        
        // Hole alle Buchungen mit Beziehungen
        $bookings = Booking::with(['renter', 'rental.vendor'])->get();
        
        if ($bookings->isEmpty()) {
            $this->command->warn('⚠️  Keine Buchungen gefunden. BookingMessagesDemoSeeder übersprungen.');
            return;
        }

        $this->command->info('💬 Erstelle Demo-Nachrichten für Buchungen...');

        $messageCount = 0;

        foreach ($bookings as $booking) {
            // Nicht alle Buchungen haben Nachrichten (80% Wahrscheinlichkeit)
            if ($faker->boolean(20)) {
                continue;
            }

            $vendor = $booking->rental->vendor;
            $customer = $booking->renter;
            
            if (!$vendor || !$customer) {
                continue;
            }

            // Anzahl Nachrichten pro Buchung (1-8)
            $messageCount = $faker->numberBetween(1, 8);
            $currentTime = $booking->created_at->copy()->addMinutes($faker->numberBetween(5, 60));

            for ($i = 0; $i < $messageCount; $i++) {
                $isVendorMessage = $i === 0 ? false : $faker->boolean(50); // Erste Nachricht meist vom Kunden
                $sender = $isVendorMessage ? $vendor : $customer;
                
                $message = $this->generateMessage($faker, $booking, $isVendorMessage, $i);
                
                BookingMessage::create([
                    'booking_id' => $booking->id,
                    'user_id' => $sender->id,
                    'message' => $message,
                    'is_vendor_message' => $isVendorMessage,
                    'read_at' => $faker->boolean(85) ? $currentTime->copy()->addMinutes($faker->numberBetween(1, 240)) : null,
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime,
                ]);

                // Zeitabstand zwischen Nachrichten
                $currentTime->addMinutes($faker->numberBetween(10, 720)); // 10 Min bis 12 Stunden
            }

            $messageCount++;
        }

        $this->command->info("✅ Demo-Nachrichten für {$messageCount} Buchungen erstellt!");
    }

    /**
     * Generiere realistische Nachrichten basierend auf Kontext
     */
    private function generateMessage($faker, Booking $booking, bool $isVendorMessage, int $messageIndex): string
    {
        $rental = $booking->rental;
        
        if ($isVendorMessage) {
            return $this->generateVendorMessage($faker, $booking, $messageIndex);
        } else {
            return $this->generateCustomerMessage($faker, $booking, $messageIndex);
        }
    }

    /**
     * Generiere Vermieter-Nachrichten
     */
    private function generateVendorMessage($faker, Booking $booking, int $messageIndex): string
    {
        $rental = $booking->rental;
        $status = $booking->status;
        
        if ($messageIndex === 0 || $messageIndex === 1) {
            // Erste Antworten vom Vermieter
            $responses = [
                "Hallo! Vielen Dank für Ihr Interesse an '{$rental->title}'. Gerne können wir einen Termin vereinbaren.",
                "Hallo, {$rental->title} ist für Ihren gewünschten Zeitraum verfügbar. Können Sie sich das Gerät vorher ansehen?",
                "Vielen Dank für Ihre Anfrage! Ja, '{$rental->title}' ist verfügbar. Wann möchten Sie es abholen?",
                "Hallo! Gerne vermiete ich Ihnen '{$rental->title}'. Haben Sie bereits Erfahrung mit diesem Gerät?",
                "Danke für Ihre Nachricht. '{$rental->title}' ist frei. Soll ich Ihnen eine Einweisung geben?",
            ];
        } else {
            // Weitere Nachrichten je nach Status
            switch ($status) {
                case 'confirmed':
                    $responses = [
                        "Perfekt! Die Buchung ist bestätigt. Ich bereite alles für Sie vor.",
                        "Alles klar, Sie können das Gerät morgen ab 9 Uhr abholen.",
                        "Die Kaution habe ich erhalten. Sie können jederzeit vorbeikommen.",
                        "Buchung bestätigt! Hier ist meine Handynummer für Rückfragen: " . $faker->phoneNumber,
                        "Sehr gut! Ich packe Ihnen auch das Zubehör mit ein.",
                        "Bestätigt! Bitte bringen Sie einen Ausweis zur Abholung mit.",
                    ];
                    break;
                    
                case 'cancelled':
                    $responses = [
                        "Schade, aber kein Problem. Die Buchung ist storniert.",
                        "Verstehe, dann streichen wir den Termin. Vielleicht ein anderes Mal!",
                        "Alles klar, habe die Stornierung vermerkt. Bis zum nächsten Mal!",
                        "Kein Problem! Bei Bedarf können Sie gerne wieder anfragen.",
                    ];
                    break;
                    
                case 'completed':
                    $responses = [
                        "Vielen Dank für die ordentliche Rückgabe! Hat alles geklappt?",
                        "Super, dass alles reibungslos verlaufen ist. Gerne wieder!",
                        "Gerät ist wieder da und in bestem Zustand. Vielen Dank!",
                        "Hat Ihnen '{$rental->title}' geholfen? Freue mich über eine Bewertung!",
                        "Perfekte Zusammenarbeit! Sie können jederzeit wieder mieten.",
                    ];
                    break;
                    
                default: // pending
                    $responses = [
                        "Können Sie mir noch sagen, wofür Sie das Gerät benötigen?",
                        "Brauchen Sie auch eine Einweisung oder kennen Sie sich aus?",
                        "Soll ich Ihnen noch Zubehör mit einpacken?",
                        "Haben Sie einen Transporter oder soll ich liefern?",
                        "Wann passt Ihnen die Abholung am besten?",
                        "Gibt es noch Fragen zur Bedienung?",
                    ];
            }
        }
        
        return $faker->randomElement($responses);
    }

    /**
     * Generiere Kunden-Nachrichten
     */
    private function generateCustomerMessage($faker, Booking $booking, int $messageIndex): string
    {
        $rental = $booking->rental;
        $status = $booking->status;
        
        if ($messageIndex === 0) {
            // Erste Nachricht ist meist die ursprüngliche Anfrage
            return $booking->message ?: "Hallo! Interesse an '{$rental->title}'. Ist das Gerät verfügbar?";
        }
        
        switch ($status) {
            case 'confirmed':
                $responses = [
                    "Super, vielen Dank! Kann ich morgen um 10 Uhr vorbeikommen?",
                    "Perfekt! Brauche ich noch etwas Bestimmtes mitbringen?",
                    "Danke für die schnelle Antwort! Freue mich auf morgen.",
                    "Alles klar! Wie lange dauert normalerweise die Einweisung?",
                    "Vielen Dank! Können Sie mir noch die genaue Adresse schicken?",
                    "Top! Ist Kartenzahlung für die Kaution möglich?",
                ];
                break;
                
            case 'cancelled':
                $responses = [
                    "Leider muss ich absagen. Tut mir wirklich leid!",
                    "Sorry, aber ich kann den Termin nicht einhalten. Entschuldigung!",
                    "Muss leider stornieren wegen Terminkonflikt. Vielleicht später?",
                    "Geht leider nicht. Das Projekt wurde verschoben.",
                    "Absage meinerseits. Vielen Dank für Ihr Verständnis!",
                ];
                break;
                
            case 'completed':
                $responses = [
                    "Alles bestens gelaufen! Vielen Dank für das tolle Gerät.",
                    "Hat perfekt funktioniert. Gerne wieder!",
                    "Super Service! Werde Sie weiterempfehlen.",
                    "Vielen Dank! Läuft bei Ihnen wirklich professionell.",
                    "Top! Bei Bedarf melde ich mich gerne wieder.",
                    "Perfekt! Bewertung folgt.",
                ];
                break;
                
            default: // pending
                $responses = [
                    "Ja, habe bereits Erfahrung mit dem Gerät.",
                    "Eine kurze Einweisung wäre super, danke!",
                    "Brauche es für ein Heimprojekt. Wann kann ich abholen?",
                    "Kann auch gerne etwas früher kommen, falls das passt.",
                    "Haben Sie auch Zubehör dazu?",
                    "Wie funktioniert das mit der Kaution?",
                    "Ist Selbstabholung oder liefern Sie auch?",
                    "Perfekt! Wann haben Sie Zeit für einen Termin?",
                ];
        }
        
        return $faker->randomElement($responses);
    }
}
