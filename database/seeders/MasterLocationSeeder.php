<?php

namespace Database\Seeders;

use App\Models\MasterLocation;
use Illuminate\Database\Seeder;

class MasterLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            // Germany (DE)
            ['postcode' => '10115', 'city' => 'Berlin', 'city_encoded' => 'berlin', 'state' => 'Berlin', 'country' => 'de', 'lat' => 52.5200, 'lng' => 13.4050],
            ['postcode' => '20095', 'city' => 'Hamburg', 'city_encoded' => 'hamburg', 'state' => 'Hamburg', 'country' => 'de', 'lat' => 53.5511, 'lng' => 9.9937],
            ['postcode' => '80331', 'city' => 'München', 'city_encoded' => 'muenchen', 'state' => 'Bayern', 'country' => 'de', 'lat' => 48.1351, 'lng' => 11.5820],
            ['postcode' => '50667', 'city' => 'Köln', 'city_encoded' => 'koeln', 'state' => 'Nordrhein-Westfalen', 'country' => 'de', 'lat' => 50.9375, 'lng' => 6.9603],
            ['postcode' => '60311', 'city' => 'Frankfurt am Main', 'city_encoded' => 'frankfurt-am-main', 'state' => 'Hessen', 'country' => 'de', 'lat' => 50.1109, 'lng' => 8.6821],
            ['postcode' => '70173', 'city' => 'Stuttgart', 'city_encoded' => 'stuttgart', 'state' => 'Baden-Württemberg', 'country' => 'de', 'lat' => 48.7758, 'lng' => 9.1829],
            ['postcode' => '40213', 'city' => 'Düsseldorf', 'city_encoded' => 'duesseldorf', 'state' => 'Nordrhein-Westfalen', 'country' => 'de', 'lat' => 51.2277, 'lng' => 6.7735],
            ['postcode' => '04109', 'city' => 'Leipzig', 'city_encoded' => 'leipzig', 'state' => 'Sachsen', 'country' => 'de', 'lat' => 51.3397, 'lng' => 12.3731],
            ['postcode' => '01067', 'city' => 'Dresden', 'city_encoded' => 'dresden', 'state' => 'Sachsen', 'country' => 'de', 'lat' => 51.0504, 'lng' => 13.7373],
            ['postcode' => '30159', 'city' => 'Hannover', 'city_encoded' => 'hannover', 'state' => 'Niedersachsen', 'country' => 'de', 'lat' => 52.3759, 'lng' => 9.7320],
            
            // Austria (AT)
            ['postcode' => '1010', 'city' => 'Wien', 'city_encoded' => 'wien', 'state' => 'Wien', 'country' => 'at', 'lat' => 48.2082, 'lng' => 16.3738],
            ['postcode' => '5020', 'city' => 'Salzburg', 'city_encoded' => 'salzburg', 'state' => 'Salzburg', 'country' => 'at', 'lat' => 47.8095, 'lng' => 13.0550],
            ['postcode' => '6020', 'city' => 'Innsbruck', 'city_encoded' => 'innsbruck', 'state' => 'Tirol', 'country' => 'at', 'lat' => 47.2692, 'lng' => 11.4041],
            ['postcode' => '8010', 'city' => 'Graz', 'city_encoded' => 'graz', 'state' => 'Steiermark', 'country' => 'at', 'lat' => 47.0707, 'lng' => 15.4395],
            ['postcode' => '4020', 'city' => 'Linz', 'city_encoded' => 'linz', 'state' => 'Oberösterreich', 'country' => 'at', 'lat' => 48.3069, 'lng' => 14.2858],
            
            // Switzerland (CH)
            ['postcode' => '8001', 'city' => 'Zürich', 'city_encoded' => 'zuerich', 'state' => 'Zürich', 'country' => 'ch', 'lat' => 47.3769, 'lng' => 8.5417],
            ['postcode' => '1201', 'city' => 'Genève', 'city_encoded' => 'geneve', 'state' => 'Genève', 'country' => 'ch', 'lat' => 46.2044, 'lng' => 6.1432],
            ['postcode' => '4001', 'city' => 'Basel', 'city_encoded' => 'basel', 'state' => 'Basel-Stadt', 'country' => 'ch', 'lat' => 47.5596, 'lng' => 7.5886],
            ['postcode' => '3001', 'city' => 'Bern', 'city_encoded' => 'bern', 'state' => 'Bern', 'country' => 'ch', 'lat' => 46.9480, 'lng' => 7.4474],
            ['postcode' => '1003', 'city' => 'Lausanne', 'city_encoded' => 'lausanne', 'state' => 'Vaud', 'country' => 'ch', 'lat' => 46.5197, 'lng' => 6.6323],
        ];

        foreach ($locations as $location) {
            MasterLocation::create($location);
        }
    }
}
