<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PostalCode;
use Illuminate\Support\Facades\DB;

class PostalCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks for faster insertion
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clear existing data
        PostalCode::truncate();
        
        // German postal codes (sample data)
        $germanPostalCodes = [
            // Berlin
            ['country_code' => 'de', 'postal_code' => '10115', 'city' => 'Berlin', 'region' => 'Berlin', 'district' => 'Mitte', 'population' => 3677472, 'latitude' => 52.5200, 'longitude' => 13.4050],
            ['country_code' => 'de', 'postal_code' => '10117', 'city' => 'Berlin', 'region' => 'Berlin', 'district' => 'Mitte', 'population' => 3677472, 'latitude' => 52.5170, 'longitude' => 13.3888],
            ['country_code' => 'de', 'postal_code' => '10119', 'city' => 'Berlin', 'region' => 'Berlin', 'district' => 'Mitte', 'population' => 3677472, 'latitude' => 52.5333, 'longitude' => 13.3833],
            ['country_code' => 'de', 'postal_code' => '10178', 'city' => 'Berlin', 'region' => 'Berlin', 'district' => 'Mitte', 'population' => 3677472, 'latitude' => 52.5170, 'longitude' => 13.4094],
            ['country_code' => 'de', 'postal_code' => '10179', 'city' => 'Berlin', 'region' => 'Berlin', 'district' => 'Mitte', 'population' => 3677472, 'latitude' => 52.5147, 'longitude' => 13.4101],
            
            // Hamburg
            ['country_code' => 'de', 'postal_code' => '20095', 'city' => 'Hamburg', 'region' => 'Hamburg', 'district' => 'Hamburg-Altstadt', 'population' => 1945532, 'latitude' => 53.5511, 'longitude' => 9.9937],
            ['country_code' => 'de', 'postal_code' => '20097', 'city' => 'Hamburg', 'region' => 'Hamburg', 'district' => 'Hammerbrook', 'population' => 1945532, 'latitude' => 53.5488, 'longitude' => 10.0155],
            ['country_code' => 'de', 'postal_code' => '20099', 'city' => 'Hamburg', 'region' => 'Hamburg', 'district' => 'St. Georg', 'population' => 1945532, 'latitude' => 53.5534, 'longitude' => 10.0154],
            
            // München
            ['country_code' => 'de', 'postal_code' => '80331', 'city' => 'München', 'region' => 'Bayern', 'district' => 'Altstadt-Lehel', 'population' => 1488202, 'latitude' => 48.1372, 'longitude' => 11.5756],
            ['country_code' => 'de', 'postal_code' => '80333', 'city' => 'München', 'region' => 'Bayern', 'district' => 'Altstadt-Lehel', 'population' => 1488202, 'latitude' => 48.1392, 'longitude' => 11.5797],
            ['country_code' => 'de', 'postal_code' => '80335', 'city' => 'München', 'region' => 'Bayern', 'district' => 'Ludwigsvorstadt-Isarvorstadt', 'population' => 1488202, 'latitude' => 48.1323, 'longitude' => 11.5661],
            
            // Köln
            ['country_code' => 'de', 'postal_code' => '50667', 'city' => 'Köln', 'region' => 'Nordrhein-Westfalen', 'district' => 'Altstadt-Nord', 'population' => 1087863, 'latitude' => 50.9375, 'longitude' => 6.9603],
            ['country_code' => 'de', 'postal_code' => '50668', 'city' => 'Köln', 'region' => 'Nordrhein-Westfalen', 'district' => 'Altstadt-Süd', 'population' => 1087863, 'latitude' => 50.9320, 'longitude' => 6.9581],
            ['country_code' => 'de', 'postal_code' => '50670', 'city' => 'Köln', 'region' => 'Nordrhein-Westfalen', 'district' => 'Neustadt-Nord', 'population' => 1087863, 'latitude' => 50.9493, 'longitude' => 6.9442],
            
            // Frankfurt
            ['country_code' => 'de', 'postal_code' => '60306', 'city' => 'Frankfurt am Main', 'region' => 'Hessen', 'district' => 'Nordend-West', 'population' => 753056, 'latitude' => 50.1109, 'longitude' => 8.6821],
            ['country_code' => 'de', 'postal_code' => '60308', 'city' => 'Frankfurt am Main', 'region' => 'Hessen', 'district' => 'Nordend-Ost', 'population' => 753056, 'latitude' => 50.1213, 'longitude' => 8.7044],
            ['country_code' => 'de', 'postal_code' => '60311', 'city' => 'Frankfurt am Main', 'region' => 'Hessen', 'district' => 'Altstadt', 'population' => 753056, 'latitude' => 50.1115, 'longitude' => 8.6842],
            
            // Stuttgart
            ['country_code' => 'de', 'postal_code' => '70173', 'city' => 'Stuttgart', 'region' => 'Baden-Württemberg', 'district' => 'Mitte', 'population' => 630305, 'latitude' => 48.7758, 'longitude' => 9.1829],
            ['country_code' => 'de', 'postal_code' => '70174', 'city' => 'Stuttgart', 'region' => 'Baden-Württemberg', 'district' => 'Mitte', 'population' => 630305, 'latitude' => 48.7682, 'longitude' => 9.1749],
            
            // Düsseldorf
            ['country_code' => 'de', 'postal_code' => '40210', 'city' => 'Düsseldorf', 'region' => 'Nordrhein-Westfalen', 'district' => 'Stadtmitte', 'population' => 621877, 'latitude' => 51.2277, 'longitude' => 6.7735],
            ['country_code' => 'de', 'postal_code' => '40211', 'city' => 'Düsseldorf', 'region' => 'Nordrhein-Westfalen', 'district' => 'Pempelfort', 'population' => 621877, 'latitude' => 51.2364, 'longitude' => 6.7789],
            
            // Leipzig
            ['country_code' => 'de', 'postal_code' => '04109', 'city' => 'Leipzig', 'region' => 'Sachsen', 'district' => 'Mitte', 'population' => 597493, 'latitude' => 51.3397, 'longitude' => 12.3731],
            ['country_code' => 'de', 'postal_code' => '04103', 'city' => 'Leipzig', 'region' => 'Sachsen', 'district' => 'Mitte', 'population' => 597493, 'latitude' => 51.3456, 'longitude' => 12.3747],
        ];
        
        // Austrian postal codes (sample data)
        $austrianPostalCodes = [
            // Wien
            ['country_code' => 'at', 'postal_code' => '1010', 'city' => 'Wien', 'region' => 'Wien', 'district' => 'Innere Stadt', 'population' => 1911728, 'latitude' => 48.2082, 'longitude' => 16.3738],
            ['country_code' => 'at', 'postal_code' => '1020', 'city' => 'Wien', 'region' => 'Wien', 'district' => 'Leopoldstadt', 'population' => 1911728, 'latitude' => 48.2167, 'longitude' => 16.3969],
            ['country_code' => 'at', 'postal_code' => '1030', 'city' => 'Wien', 'region' => 'Wien', 'district' => 'Landstraße', 'population' => 1911728, 'latitude' => 48.1975, 'longitude' => 16.3947],
            ['country_code' => 'at', 'postal_code' => '1040', 'city' => 'Wien', 'region' => 'Wien', 'district' => 'Wieden', 'population' => 1911728, 'latitude' => 48.1972, 'longitude' => 16.3681],
            
            // Graz
            ['country_code' => 'at', 'postal_code' => '8010', 'city' => 'Graz', 'region' => 'Steiermark', 'district' => 'Innere Stadt', 'population' => 328276, 'latitude' => 47.0707, 'longitude' => 15.4395],
            ['country_code' => 'at', 'postal_code' => '8020', 'city' => 'Graz', 'region' => 'Steiermark', 'district' => 'St. Leonhard', 'population' => 328276, 'latitude' => 47.0648, 'longitude' => 15.4307],
            
            // Linz
            ['country_code' => 'at', 'postal_code' => '4020', 'city' => 'Linz', 'region' => 'Oberösterreich', 'district' => 'Innere Stadt', 'population' => 206604, 'latitude' => 48.3059, 'longitude' => 14.2864],
            
            // Salzburg
            ['country_code' => 'at', 'postal_code' => '5020', 'city' => 'Salzburg', 'region' => 'Salzburg', 'district' => 'Altstadt', 'population' => 154211, 'latitude' => 47.8095, 'longitude' => 13.0550],
            
            // Innsbruck
            ['country_code' => 'at', 'postal_code' => '6020', 'city' => 'Innsbruck', 'region' => 'Tirol', 'district' => 'Innere Stadt', 'population' => 132236, 'latitude' => 47.2692, 'longitude' => 11.4041],
        ];
        
        // Swiss postal codes (sample data)
        $swissPostalCodes = [
            // Zürich
            ['country_code' => 'ch', 'postal_code' => '8001', 'city' => 'Zürich', 'region' => 'Zürich', 'district' => 'Altstadt', 'population' => 421878, 'latitude' => 47.3769, 'longitude' => 8.5417],
            ['country_code' => 'ch', 'postal_code' => '8002', 'city' => 'Zürich', 'region' => 'Zürich', 'district' => 'Enge', 'population' => 421878, 'latitude' => 47.3358, 'longitude' => 8.5319],
            ['country_code' => 'ch', 'postal_code' => '8003', 'city' => 'Zürich', 'region' => 'Zürich', 'district' => 'Wiedikon', 'population' => 421878, 'latitude' => 47.3598, 'longitude' => 8.5199],
            ['country_code' => 'ch', 'postal_code' => '8004', 'city' => 'Zürich', 'region' => 'Zürich', 'district' => 'Aussersihl', 'population' => 421878, 'latitude' => 47.3751, 'longitude' => 8.5244],
            
            // Genève
            ['country_code' => 'ch', 'postal_code' => '1200', 'city' => 'Genève', 'region' => 'Genève', 'district' => 'Centre', 'population' => 203856, 'latitude' => 46.2044, 'longitude' => 6.1432],
            ['country_code' => 'ch', 'postal_code' => '1201', 'city' => 'Genève', 'region' => 'Genève', 'district' => 'Centre', 'population' => 203856, 'latitude' => 46.2017, 'longitude' => 6.1466],
            
            // Basel
            ['country_code' => 'ch', 'postal_code' => '4001', 'city' => 'Basel', 'region' => 'Basel-Stadt', 'district' => 'Altstadt Grossbasel', 'population' => 173863, 'latitude' => 47.5596, 'longitude' => 7.5886],
            ['country_code' => 'ch', 'postal_code' => '4002', 'city' => 'Basel', 'region' => 'Basel-Stadt', 'district' => 'Vorstädte', 'population' => 173863, 'latitude' => 47.5515, 'longitude' => 7.5901],
            
            // Bern
            ['country_code' => 'ch', 'postal_code' => '3000', 'city' => 'Bern', 'region' => 'Bern', 'district' => 'Innere Stadt', 'population' => 133883, 'latitude' => 46.9481, 'longitude' => 7.4474],
            ['country_code' => 'ch', 'postal_code' => '3001', 'city' => 'Bern', 'region' => 'Bern', 'district' => 'Innere Stadt', 'population' => 133883, 'latitude' => 46.9520, 'longitude' => 7.4387],
            
            // Lausanne
            ['country_code' => 'ch', 'postal_code' => '1000', 'city' => 'Lausanne', 'region' => 'Vaud', 'district' => 'Centre', 'population' => 140202, 'latitude' => 46.5197, 'longitude' => 6.6323],
            ['country_code' => 'ch', 'postal_code' => '1001', 'city' => 'Lausanne', 'region' => 'Vaud', 'district' => 'Centre', 'population' => 140202, 'latitude' => 46.5183, 'longitude' => 6.6342],
        ];
        
        // Combine all postal codes
        $allPostalCodes = array_merge($germanPostalCodes, $austrianPostalCodes, $swissPostalCodes);
        
        // Add timestamps
        $now = now();
        foreach ($allPostalCodes as &$postalCode) {
            $postalCode['created_at'] = $now;
            $postalCode['updated_at'] = $now;
        }
        
        // Insert in chunks for better performance
        $chunks = array_chunk($allPostalCodes, 100);
        foreach ($chunks as $chunk) {
            PostalCode::insert($chunk);
        }
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info('Postal codes seeded successfully!');
        $this->command->info('Total records: ' . count($allPostalCodes));
        $this->command->info('German postal codes: ' . count($germanPostalCodes));
        $this->command->info('Austrian postal codes: ' . count($austrianPostalCodes));
        $this->command->info('Swiss postal codes: ' . count($swissPostalCodes));
    }
}
