<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hole oder erstelle einen Test-Vendor
        $vendor = User::where('email', 'vendor@inlando.test')->first();
        if (!$vendor) {
            $vendor = User::factory()->create([
                'name' => 'Test Vendor',
                'email' => 'vendor@inlando.test',
                'is_vendor' => true,
            ]);
        }

        $locations = [
            'DE' => [
                'Berlin',
                'Hamburg',
                'München',
                'Köln',
                'Frankfurt',
                'Stuttgart',
                'Düsseldorf',
                'Leipzig',
                'Dortmund',
                'Essen'
            ],
            'AT' => [
                'Wien',
                'Graz',
                'Linz',
                'Salzburg',
                'Innsbruck',
                'Klagenfurt',
                'Villach',
                'Wels',
                'Sankt Pölten',
                'Dornbirn'
            ],
            'CH' => [
                'Zürich',
                'Genf',
                'Basel',
                'Bern',
                'Lausanne',
                'Winterthur',
                'St. Gallen',
                'Luzern',
                'Lugano',
                'Biel'
            ],
            'IT' => [
                'Rom',
                'Mailand',
                'Neapel',
                'Turin',
                'Palermo',
                'Genua',
                'Bologna',
                'Florenz',
                'Bari',
                'Catania'
            ],
            'FR' => [
                'Paris',
                'Marseille',
                'Lyon',
                'Toulouse',
                'Nizza',
                'Nantes',
                'Straßburg',
                'Montpellier',
                'Bordeaux',
                'Lille'
            ],
            'ES' => [
                'Madrid',
                'Barcelona',
                'Valencia',
                'Sevilla',
                'Zaragoza',
                'Málaga',
                'Murcia',
                'Palma',
                'Las Palmas',
                'Bilbao'
            ],
            'NL' => [
                'Amsterdam',
                'Rotterdam',
                'Den Haag',
                'Utrecht',
                'Eindhoven',
                'Groningen',
                'Tilburg',
                'Almere',
                'Breda',
                'Nijmegen'
            ],
            'BE' => [
                'Brüssel',
                'Antwerpen',
                'Gent',
                'Charleroi',
                'Lüttich',
                'Brügge',
                'Namur',
                'Leuven',
                'Mons',
                'Aalst'
            ],
            'LU' => [
                'Luxemburg',
                'Esch-sur-Alzette',
                'Differdange',
                'Düdelingen',
                'Ettelbrück',
                'Diekirch',
                'Wiltz',
                'Echternach',
                'Rumelange',
                'Grevenmacher'
            ],
            'DK' => [
                'Kopenhagen',
                'Aarhus',
                'Odense',
                'Aalborg',
                'Frederiksberg',
                'Esbjerg',
                'Gentofte',
                'Gladsaxe',
                'Randers',
                'Kolding'
            ]
        ];

        foreach ($locations as $countryCode => $cities) {
            $country = Country::where('code', $countryCode)->first();

            if ($country) {
                foreach ($cities as $city) {
                    Location::create([
                        'name' => $city,
                        'country_id' => $country->id,
                        'vendor_id' => $vendor->id,
                        'is_active' => true,
                        'is_main' => false,
                        'street_address' => 'Musterstraße 1',
                        'postal_code' => '12345',
                        'city' => $city,
                        'country' => $countryCode
                    ]);
                }
            }
        }

        // Setze den ersten Standort als Hauptstandort
        $firstLocation = Location::where('vendor_id', $vendor->id)->first();
        if ($firstLocation) {
            $firstLocation->update(['is_main' => true]);
        }
    }
}