<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\ReviewReply;
use App\Models\Rental;
use App\Models\User;
use Carbon\Carbon;

class ReviewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all rentals
        $rentals = Rental::all();

        // Get customers (users that are not vendors or admins)
        $customers = User::where('is_vendor', false)->where('is_admin', false)->get();

        if ($rentals->count() === 0 || $customers->count() === 0) {
            $this->command->info('No rentals or customers found. Please seed rentals and users first.');
            return;
        }

        // Create 50 reviews
        for ($i = 0; $i < 50; $i++) {
            $rental = $rentals->random();
            $customer = $customers->random();
            $status = ['published', 'pending', 'rejected'][rand(0, 2)];
            $isVerified = rand(0, 1);
            $rating = rand(1, 5);

            $review = Review::create([
                'rental_id' => $rental->id,
                'user_id' => $customer->id,
                'rating' => $rating,
                'comment' => $this->getRandomComment($rating),
                'status' => $status,
                'is_verified' => $isVerified,
                'stay_date' => Carbon::now()->subDays(rand(7, 90))->format('Y-m-d'),
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
            ]);

            // Add replies to some reviews
            if (rand(0, 1) && $status === 'published') {
                $vendorId = $rental->vendor_id;

                ReviewReply::create([
                    'review_id' => $review->id,
                    'user_id' => $vendorId,
                    'reply' => $this->getRandomReply($rating),
                    'created_at' => $review->created_at->addDays(rand(1, 5)),
                ]);
            }
        }
    }

    /**
     * Get a random comment based on the rating.
     */
    private function getRandomComment($rating): string
    {
        $comments = [
            1 => [
                'Die Unterkunft war weit unter unseren Erwartungen. Nicht empfehlenswert.',
                'Sehr schmutzig und unorganisiert. Es gab viele Probleme während unseres Aufenthalts.',
                'Wir waren sehr enttäuscht. Die Beschreibung entsprach überhaupt nicht der Realität.',
                'Schlechte Erfahrung. Würde nie wieder buchen.',
                'Das schlechteste Ferienhaus, in dem ich je übernachtet habe. Viel zu teuer für das, was geboten wird.'
            ],
            2 => [
                'Es gab einige Probleme, aber insgesamt war es in Ordnung.',
                'Nicht das beste, aber auch nicht das schlechteste. Es gibt definitiv Verbesserungspotential.',
                'Die Lage war gut, aber die Unterkunft selbst hat uns nicht überzeugt.',
                'Durchschnittlich, mit einigen Mängeln.',
                'Es war okay für einen kurzen Aufenthalt, aber für längere Zeit würde ich woanders buchen.'
            ],
            3 => [
                'Durchschnittliche Unterkunft zu einem angemessenen Preis.',
                'Es war in Ordnung, nichts Besonderes, aber auch nichts Schlechtes.',
                'Die Lage war gut, aber es gab ein paar kleine Probleme mit der Sauberkeit.',
                'Akzeptabel für unseren kurzen Aufenthalt.',
                'Mittelmäßig, erfüllt den Zweck, aber ohne besondere Highlights.'
            ],
            4 => [
                'Tolle Unterkunft! Wir hatten eine schöne Zeit dort.',
                'Sehr sauber und gut ausgestattet. Wir kommen gerne wieder!',
                'Der Gastgeber war sehr hilfsbereit. Wir haben unseren Aufenthalt genossen.',
                'Schöne Unterkunft in einer tollen Lage. Nur kleine Abzüge in der Bewertung.',
                'Fast perfekt! Wir waren sehr zufrieden.'
            ],
            5 => [
                'Absolut fantastisch! Die beste Unterkunft, die wir je hatten.',
                'Perfekter Aufenthalt! Alles war makellos und der Gastgeber war unglaublich freundlich.',
                'Wir kommen definitiv wieder! Besser geht es nicht.',
                'Eine wahre Perle! Jeder Aspekt war perfekt und hat unsere Erwartungen übertroffen.',
                '5 Sterne sind nicht genug für diese wundervolle Unterkunft. Hervorragend!'
            ]
        ];

        return $comments[$rating][rand(0, 4)];
    }

    /**
     * Get a random reply based on the review rating.
     */
    private function getRandomReply($rating): string
    {
        $replies = [
            1 => [
                'Vielen Dank für Ihr Feedback. Es tut uns sehr leid, dass wir Ihre Erwartungen nicht erfüllen konnten. Wir werden an den angesprochenen Punkten arbeiten.',
                'Wir entschuldigen uns für die Unannehmlichkeiten und werden uns umgehend um die Probleme kümmern.',
                'Es tut uns leid zu hören, dass Sie unzufrieden waren. Bitte kontaktieren Sie uns direkt, damit wir die Probleme besprechen können.',
            ],
            2 => [
                'Vielen Dank für Ihr Feedback. Wir nehmen Ihre Anmerkungen ernst und werden daran arbeiten.',
                'Danke für Ihre ehrliche Bewertung. Wir werden uns bemühen, unseren Service zu verbessern.',
                'Wir schätzen Ihre konstruktive Kritik und werden die angesprochenen Punkte verbessern.',
            ],
            3 => [
                'Vielen Dank für Ihren Aufenthalt und Ihre Rückmeldung. Wir werden uns bemühen, beim nächsten Mal einen noch besseren Eindruck zu hinterlassen.',
                'Danke für Ihre Bewertung. Wir freuen uns, dass Ihnen einige Aspekte gefallen haben, und arbeiten an den Verbesserungspunkten.',
                'Wir schätzen Ihr Feedback und hoffen, Sie bald wieder bei uns begrüßen zu dürfen - dann mit noch besserem Service.',
            ],
            4 => [
                'Vielen Dank für Ihre positive Bewertung! Es freut uns sehr, dass Sie einen angenehmen Aufenthalt hatten.',
                'Danke für Ihr tolles Feedback! Wir freuen uns darauf, Sie bald wieder bei uns begrüßen zu dürfen.',
                'Wir sind glücklich, dass Sie Ihren Aufenthalt genossen haben, und nehmen Ihre konstruktiven Vorschläge gerne auf.',
            ],
            5 => [
                'Herzlichen Dank für Ihre fantastische Bewertung! Es war uns eine Freude, Sie als Gast bei uns zu haben.',
                'Vielen Dank für Ihre wundervolle Bewertung! Wir freuen uns sehr, dass wir Ihre Erwartungen übertreffen konnten.',
                'Wir sind begeistert, dass Sie einen perfekten Aufenthalt hatten! Vielen Dank für Ihr tolles Feedback und wir hoffen, Sie bald wieder bei uns zu sehen.',
            ]
        ];

        return $replies[$rating][rand(0, 2)];
    }
}
