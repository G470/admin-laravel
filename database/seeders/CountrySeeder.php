<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            [
                'name' => 'Deutschland',
                'code' => 'DE',
                'phone_code' => '+49',
                'is_active' => true
            ],
            [
                'name' => 'Österreich',
                'code' => 'AT',
                'phone_code' => '+43',
                'is_active' => true
            ],
            [
                'name' => 'Schweiz',
                'code' => 'CH',
                'phone_code' => '+41',
                'is_active' => true
            ],
            [
                'name' => 'Italien',
                'code' => 'IT',
                'phone_code' => '+39',
                'is_active' => true
            ],
            [
                'name' => 'Frankreich',
                'code' => 'FR',
                'phone_code' => '+33',
                'is_active' => true
            ],
            [
                'name' => 'Spanien',
                'code' => 'ES',
                'phone_code' => '+34',
                'is_active' => true
            ],
            [
                'name' => 'Niederlande',
                'code' => 'NL',
                'phone_code' => '+31',
                'is_active' => true
            ],
            [
                'name' => 'Belgien',
                'code' => 'BE',
                'phone_code' => '+32',
                'is_active' => true
            ],
            [
                'name' => 'Luxemburg',
                'code' => 'LU',
                'phone_code' => '+352',
                'is_active' => true
            ],
            [
                'name' => 'Dänemark',
                'code' => 'DK',
                'phone_code' => '+45',
                'is_active' => true
            ]
        ];

        foreach ($countries as $country) {
            Country::updateOrCreate(
                ['code' => $country['code']],
                $country
            );
        }
    }
}