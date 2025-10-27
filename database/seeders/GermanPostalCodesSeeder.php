<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Services\CountryDataImportService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class GermanPostalCodesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure Germany exists in countries table
        $germany = Country::updateOrCreate(
            ['code' => 'DE'],
            [
                'name' => 'Deutschland',
                'code' => 'DE',
                'phone_code' => '+49',
                'is_active' => true
            ]
        );

        $this->command->info("Germany country record ensured (ID: {$germany->id})");

        // Create postal_codes_de table using the service
        $importService = new CountryDataImportService();
        $tableName = 'postal_codes_de';

        // Create table if it doesn't exist
        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('country_code', 2)->default('DE')->index();
                $table->string('postal_code', 20)->index();
                $table->string('city', 100)->index();
                $table->string('sub_city', 100)->nullable()->index();
                $table->string('region', 100)->nullable()->index();
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();
                $table->integer('population')->nullable()->index();
                $table->json('additional_data')->nullable();
                $table->timestamps();

                // Composite indexes for performance
                $table->index(['postal_code', 'city']);
                $table->index(['region', 'city']);
                $table->index(['population', 'city']);

                // Unique constraint to prevent duplicates
                $table->unique(['postal_code', 'city', 'sub_city']);
            });

            $this->command->info("Created table: {$tableName}");
        } else {
            $this->command->info("Table {$tableName} already exists");
        }

        // Clear existing data
        DB::table($tableName)->truncate();

        // Comprehensive German postal code data
        $germanPostalCodes = [
            // Baden-Württemberg
            ['postal_code' => '70173', 'city' => 'Stuttgart', 'sub_city' => 'Mitte', 'region' => 'Baden-Württemberg', 'latitude' => 48.7758, 'longitude' => 9.1829, 'population' => 630305],
            ['postal_code' => '70174', 'city' => 'Stuttgart', 'sub_city' => 'West', 'region' => 'Baden-Württemberg', 'latitude' => 48.7682, 'longitude' => 9.1749, 'population' => 630305],
            ['postal_code' => '68159', 'city' => 'Mannheim', 'sub_city' => 'Innenstadt', 'region' => 'Baden-Württemberg', 'latitude' => 49.4875, 'longitude' => 8.4660, 'population' => 309370],
            ['postal_code' => '76131', 'city' => 'Karlsruhe', 'sub_city' => 'Innenstadt-Ost', 'region' => 'Baden-Württemberg', 'latitude' => 49.0069, 'longitude' => 8.4037, 'population' => 313092],
            ['postal_code' => '79098', 'city' => 'Freiburg im Breisgau', 'sub_city' => 'Altstadt', 'region' => 'Baden-Württemberg', 'latitude' => 47.9990, 'longitude' => 7.8421, 'population' => 230241],

            // Bayern  
            ['postal_code' => '80331', 'city' => 'München', 'sub_city' => 'Altstadt-Lehel', 'region' => 'Bayern', 'latitude' => 48.1372, 'longitude' => 11.5756, 'population' => 1488202],
            ['postal_code' => '80333', 'city' => 'München', 'sub_city' => 'Altstadt-Lehel', 'region' => 'Bayern', 'latitude' => 48.1392, 'longitude' => 11.5797, 'population' => 1488202],
            ['postal_code' => '90402', 'city' => 'Nürnberg', 'sub_city' => 'Lorenz', 'region' => 'Bayern', 'latitude' => 49.4521, 'longitude' => 11.0767, 'population' => 518365],
            ['postal_code' => '86150', 'city' => 'Augsburg', 'sub_city' => 'Innenstadt', 'region' => 'Bayern', 'latitude' => 48.3705, 'longitude' => 10.8978, 'population' => 296582],
            ['postal_code' => '97070', 'city' => 'Würzburg', 'sub_city' => 'Altstadt', 'region' => 'Bayern', 'latitude' => 49.7913, 'longitude' => 9.9534, 'population' => 127934],

            // Berlin
            ['postal_code' => '10115', 'city' => 'Berlin', 'sub_city' => 'Mitte', 'region' => 'Berlin', 'latitude' => 52.5200, 'longitude' => 13.4050, 'population' => 3677472],
            ['postal_code' => '10117', 'city' => 'Berlin', 'sub_city' => 'Mitte', 'region' => 'Berlin', 'latitude' => 52.5170, 'longitude' => 13.3888, 'population' => 3677472],
            ['postal_code' => '10178', 'city' => 'Berlin', 'sub_city' => 'Mitte', 'region' => 'Berlin', 'latitude' => 52.5170, 'longitude' => 13.4094, 'population' => 3677472],
            ['postal_code' => '10179', 'city' => 'Berlin', 'sub_city' => 'Mitte', 'region' => 'Berlin', 'latitude' => 52.5147, 'longitude' => 13.4101, 'population' => 3677472],
            ['postal_code' => '12043', 'city' => 'Berlin', 'sub_city' => 'Neukölln', 'region' => 'Berlin', 'latitude' => 52.4819, 'longitude' => 13.4419, 'population' => 3677472],

            // Brandenburg
            ['postal_code' => '14467', 'city' => 'Potsdam', 'sub_city' => 'Innenstadt', 'region' => 'Brandenburg', 'latitude' => 52.3906, 'longitude' => 13.0645, 'population' => 180334],
            ['postal_code' => '03046', 'city' => 'Cottbus', 'sub_city' => 'Mitte', 'region' => 'Brandenburg', 'latitude' => 51.7606, 'longitude' => 14.3340, 'population' => 99678],

            // Bremen
            ['postal_code' => '28195', 'city' => 'Bremen', 'sub_city' => 'Mitte', 'region' => 'Bremen', 'latitude' => 53.0793, 'longitude' => 8.8017, 'population' => 569352],
            ['postal_code' => '27568', 'city' => 'Bremerhaven', 'sub_city' => 'Mitte', 'region' => 'Bremen', 'latitude' => 53.5396, 'longitude' => 8.5809, 'population' => 113557],

            // Hamburg
            ['postal_code' => '20095', 'city' => 'Hamburg', 'sub_city' => 'Hamburg-Altstadt', 'region' => 'Hamburg', 'latitude' => 53.5511, 'longitude' => 9.9937, 'population' => 1945532],
            ['postal_code' => '20097', 'city' => 'Hamburg', 'sub_city' => 'Hammerbrook', 'region' => 'Hamburg', 'latitude' => 53.5488, 'longitude' => 10.0155, 'population' => 1945532],
            ['postal_code' => '20099', 'city' => 'Hamburg', 'sub_city' => 'St. Georg', 'region' => 'Hamburg', 'latitude' => 53.5534, 'longitude' => 10.0154, 'population' => 1945532],
            ['postal_code' => '22767', 'city' => 'Hamburg', 'sub_city' => 'Altona', 'region' => 'Hamburg', 'latitude' => 53.5510, 'longitude' => 9.9353, 'population' => 1945532],

            // Hessen
            ['postal_code' => '60306', 'city' => 'Frankfurt am Main', 'sub_city' => 'Nordend-West', 'region' => 'Hessen', 'latitude' => 50.1109, 'longitude' => 8.6821, 'population' => 753056],
            ['postal_code' => '60308', 'city' => 'Frankfurt am Main', 'sub_city' => 'Nordend-Ost', 'region' => 'Hessen', 'latitude' => 50.1213, 'longitude' => 8.7044, 'population' => 753056],
            ['postal_code' => '60311', 'city' => 'Frankfurt am Main', 'sub_city' => 'Altstadt', 'region' => 'Hessen', 'latitude' => 50.1115, 'longitude' => 8.6842, 'population' => 753056],
            ['postal_code' => '65183', 'city' => 'Wiesbaden', 'sub_city' => 'Mitte', 'region' => 'Hessen', 'latitude' => 50.0826, 'longitude' => 8.2400, 'population' => 278474],
            ['postal_code' => '34117', 'city' => 'Kassel', 'sub_city' => 'Mitte', 'region' => 'Hessen', 'latitude' => 51.3127, 'longitude' => 9.4797, 'population' => 201585],

            // Mecklenburg-Vorpommern
            ['postal_code' => '19053', 'city' => 'Schwerin', 'sub_city' => 'Altstadt', 'region' => 'Mecklenburg-Vorpommern', 'latitude' => 53.6355, 'longitude' => 11.4010, 'population' => 95818],
            ['postal_code' => '18055', 'city' => 'Rostock', 'sub_city' => 'Stadtmitte', 'region' => 'Mecklenburg-Vorpommern', 'latitude' => 54.0887, 'longitude' => 12.1409, 'population' => 209920],

            // Niedersachsen
            ['postal_code' => '30159', 'city' => 'Hannover', 'sub_city' => 'Mitte', 'region' => 'Niedersachsen', 'latitude' => 52.3759, 'longitude' => 9.7320, 'population' => 535932],
            ['postal_code' => '38100', 'city' => 'Braunschweig', 'sub_city' => 'Innenstadt', 'region' => 'Niedersachsen', 'latitude' => 52.2689, 'longitude' => 10.5268, 'population' => 248292],
            ['postal_code' => '26122', 'city' => 'Oldenburg', 'sub_city' => 'Innenstadt', 'region' => 'Niedersachsen', 'latitude' => 53.1435, 'longitude' => 8.2146, 'population' => 169605],

            // Nordrhein-Westfalen
            ['postal_code' => '50667', 'city' => 'Köln', 'sub_city' => 'Altstadt-Nord', 'region' => 'Nordrhein-Westfalen', 'latitude' => 50.9375, 'longitude' => 6.9603, 'population' => 1087863],
            ['postal_code' => '50668', 'city' => 'Köln', 'sub_city' => 'Altstadt-Süd', 'region' => 'Nordrhein-Westfalen', 'latitude' => 50.9320, 'longitude' => 6.9581, 'population' => 1087863],
            ['postal_code' => '40210', 'city' => 'Düsseldorf', 'sub_city' => 'Stadtmitte', 'region' => 'Nordrhein-Westfalen', 'latitude' => 51.2277, 'longitude' => 6.7735, 'population' => 621877],
            ['postal_code' => '45127', 'city' => 'Essen', 'sub_city' => 'Stadtkern', 'region' => 'Nordrhein-Westfalen', 'latitude' => 51.4556, 'longitude' => 7.0116, 'population' => 582760],
            ['postal_code' => '44135', 'city' => 'Dortmund', 'sub_city' => 'Innenstadt-West', 'region' => 'Nordrhein-Westfalen', 'latitude' => 51.5136, 'longitude' => 7.4653, 'population' => 588250],

            // Rheinland-Pfalz
            ['postal_code' => '55116', 'city' => 'Mainz', 'sub_city' => 'Altstadt', 'region' => 'Rheinland-Pfalz', 'latitude' => 49.9929, 'longitude' => 8.2473, 'population' => 217118],
            ['postal_code' => '67061', 'city' => 'Ludwigshafen am Rhein', 'sub_city' => 'Mitte', 'region' => 'Rheinland-Pfalz', 'latitude' => 49.4771, 'longitude' => 8.4454, 'population' => 172253],

            // Saarland
            ['postal_code' => '66111', 'city' => 'Saarbrücken', 'sub_city' => 'Mitte', 'region' => 'Saarland', 'latitude' => 49.2401, 'longitude' => 6.9969, 'population' => 179634],

            // Sachsen
            ['postal_code' => '01067', 'city' => 'Dresden', 'sub_city' => 'Altstadt', 'region' => 'Sachsen', 'latitude' => 51.0504, 'longitude' => 13.7373, 'population' => 554649],
            ['postal_code' => '04109', 'city' => 'Leipzig', 'sub_city' => 'Mitte', 'region' => 'Sachsen', 'latitude' => 51.3397, 'longitude' => 12.3731, 'population' => 597493],
            ['postal_code' => '09111', 'city' => 'Chemnitz', 'sub_city' => 'Zentrum', 'region' => 'Sachsen', 'latitude' => 50.8278, 'longitude' => 12.9214, 'population' => 246855],

            // Sachsen-Anhalt
            ['postal_code' => '39104', 'city' => 'Magdeburg', 'sub_city' => 'Altstadt', 'region' => 'Sachsen-Anhalt', 'latitude' => 52.1205, 'longitude' => 11.6276, 'population' => 237565],
            ['postal_code' => '06108', 'city' => 'Halle (Saale)', 'sub_city' => 'Altstadt', 'region' => 'Sachsen-Anhalt', 'latitude' => 51.4969, 'longitude' => 11.9688, 'population' => 238762],

            // Schleswig-Holstein
            ['postal_code' => '24103', 'city' => 'Kiel', 'sub_city' => 'Altstadt', 'region' => 'Schleswig-Holstein', 'latitude' => 54.3233, 'longitude' => 10.1228, 'population' => 247717],
            ['postal_code' => '23552', 'city' => 'Lübeck', 'sub_city' => 'Innenstadt', 'region' => 'Schleswig-Holstein', 'latitude' => 53.8697, 'longitude' => 10.6873, 'population' => 217198],

            // Thüringen
            ['postal_code' => '99084', 'city' => 'Erfurt', 'sub_city' => 'Altstadt', 'region' => 'Thüringen', 'latitude' => 50.9848, 'longitude' => 11.0299, 'population' => 213699],
            ['postal_code' => '07743', 'city' => 'Jena', 'sub_city' => 'Zentrum', 'region' => 'Thüringen', 'latitude' => 50.9278, 'longitude' => 11.5820, 'population' => 108993],

            // Additional cities with multiple postal codes
            ['postal_code' => '20146', 'city' => 'Hamburg', 'sub_city' => 'Rotherbaum', 'region' => 'Hamburg', 'latitude' => 53.5693, 'longitude' => 9.9893, 'population' => 1945532],
            ['postal_code' => '12163', 'city' => 'Berlin', 'sub_city' => 'Steglitz', 'region' => 'Berlin', 'latitude' => 52.4573, 'longitude' => 13.3305, 'population' => 3677472],
            ['postal_code' => '80797', 'city' => 'München', 'sub_city' => 'Schwabing-West', 'region' => 'Bayern', 'latitude' => 48.1612, 'longitude' => 11.5656, 'population' => 1488202],
            ['postal_code' => '70435', 'city' => 'Stuttgart', 'sub_city' => 'Zuffenhausen', 'region' => 'Baden-Württemberg', 'latitude' => 48.8341, 'longitude' => 9.1756, 'population' => 630305],
            ['postal_code' => '50823', 'city' => 'Köln', 'sub_city' => 'Ehrenfeld', 'region' => 'Nordrhein-Westfalen', 'latitude' => 50.9547, 'longitude' => 6.9138, 'population' => 1087863],

            // Smaller cities but important postal codes
            ['postal_code' => '21614', 'city' => 'Buxtehude', 'sub_city' => null, 'region' => 'Niedersachsen', 'latitude' => 53.4786, 'longitude' => 9.6947, 'population' => 40051],
            ['postal_code' => '74072', 'city' => 'Heilbronn', 'sub_city' => 'Innenstadt', 'region' => 'Baden-Württemberg', 'latitude' => 49.1427, 'longitude' => 9.2209, 'population' => 126458],
            ['postal_code' => '42103', 'city' => 'Wuppertal', 'sub_city' => 'Elberfeld', 'region' => 'Nordrhein-Westfalen', 'latitude' => 51.2562, 'longitude' => 7.1508, 'population' => 354572],
            ['postal_code' => '99423', 'city' => 'Weimar', 'sub_city' => 'Altstadt', 'region' => 'Thüringen', 'latitude' => 50.9794, 'longitude' => 11.3235, 'population' => 65090],
        ];

        // Add required fields and timestamps
        $now = now();
        foreach ($germanPostalCodes as &$postalCode) {
            $postalCode['country_code'] = 'DE';
            $postalCode['created_at'] = $now;
            $postalCode['updated_at'] = $now;

            // Ensure sub_city is properly handled
            if (empty($postalCode['sub_city'])) {
                $postalCode['sub_city'] = null;
            }
        }

        // Insert data in chunks for better performance
        $chunks = array_chunk($germanPostalCodes, 50);
        foreach ($chunks as $chunk) {
            DB::table($tableName)->insert($chunk);
        }

        $this->command->info('German postal codes seeded successfully!');
        $this->command->info('Total German postal codes: ' . count($germanPostalCodes));
        $this->command->info('Covered all 16 federal states (Bundesländer)');

        // Show statistics
        $stats = DB::table($tableName)->select(
            DB::raw('COUNT(*) as total'),
            DB::raw('COUNT(DISTINCT region) as regions'),
            DB::raw('COUNT(DISTINCT city) as cities'),
            DB::raw('COUNT(*) - COUNT(latitude) as missing_coordinates')
        )->first();

        $this->command->info("Statistics:");
        $this->command->info("- Total records: {$stats->total}");
        $this->command->info("- Federal states: {$stats->regions}");
        $this->command->info("- Cities: {$stats->cities}");
        $this->command->info("- Records with coordinates: " . ($stats->total - $stats->missing_coordinates));
    }
}